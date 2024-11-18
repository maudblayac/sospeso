<?php
namespace App\Controller\Administration;

use App\Entity\Restaurant;
use App\Entity\User;
use App\Form\AdminRestaurateurProfileType;
use App\Form\AdminRestaurantListingType;
use App\Form\AdminProductType;
use App\Repository\UserRepository;
use App\Enum\Status;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

#[Route('/admin/restaurateurs', name: 'admin_restaurateur_')]
class AdminProfileRestaurantController extends AbstractController
{
    // Liste des restaurateurs
    #[Route('/', name: 'list')]
    public function list(UserRepository $userRepository): Response
    {
        $restaurateurs = $userRepository->findByRole('ROLE_RESTAURANT');

        return $this->render('admin/profile/list.html.twig', [
            'restaurateurs' => $restaurateurs,
        ]);
    }

    #[Route('/{id}', name: 'show')]
    public function show(User $user, Request $request, EntityManagerInterface $entityManager): Response
    {
        $restaurant = $user->getRestaurant();
    
        // Vérifier que le restaurant existe
        if (!$restaurant) {
            $this->addFlash('error', 'Aucun restaurant associé à cet utilisateur.');
            return $this->redirectToRoute('admin_restaurateur_list');
        }
    
       // Formulaire pour le profil du restaurateur
        $profileForm = $this->createForm(AdminRestaurateurProfileType::class, $restaurant);
        $profileForm->handleRequest($request);
    
        // Formulaire pour l'annonce du restaurant
        $listingForm = $this->createForm(AdminRestaurantListingType::class, $restaurant, [
            'restaurant' => $restaurant, 
        ]);
        $listingForm->handleRequest($request);
    
        // Formulaire pour ajouter un produit
        $productForm = $this->createForm(AdminProductType::class);
        $productForm->handleRequest($request);
    
        if ($profileForm->isSubmitted() && $profileForm->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Profil du restaurateur mis à jour avec succès.');
            return $this->redirectToRoute('admin_restaurateur_show', ['id' => $user->getId()]);
        }
    
        if ($listingForm->isSubmitted() && $listingForm->isValid()) {
            $restaurant->setHasListing(true);
            $restaurant->setIsApproved(true); 
            $entityManager->flush();
            $this->addFlash('success', 'Annonce du restaurant mise à jour avec succès.');
            return $this->redirectToRoute('admin_restaurateur_show', ['id' => $user->getId()]);
        }
    
        if ($productForm->isSubmitted() && $productForm->isValid()) {
            $product = $productForm->getData();
            $product->setRestaurant($restaurant);
            $product->setIsApproved(true);  
            $entityManager->persist($product);
            $entityManager->flush();
            $this->addFlash('success', 'Produit ajouté avec succès.');
            return $this->redirectToRoute('admin_restaurateur_show', ['id' => $user->getId()]);
        }
    
        return $this->render('admin/profile/show.html.twig', [
            'user' => $user,
            'restaurant' => $restaurant,
            'profileForm' => $profileForm->createView(),
            'listingForm' => $listingForm->createView(),
            'productForm' => $productForm->createView(),
        ]);
    }
    
    

    // Modifier le profil d'un restaurateur
    #[Route('/{id}/edit', name: 'edit')]
    public function edit(User $user, Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!in_array('ROLE_RESTAURANT', $user->getRoles(), true)) {
            throw $this->createNotFoundException('Cet utilisateur n\'est pas un restaurateur.');
        }

        $form = $this->createForm(AdminRestaurateurProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gestion du mot de passe
            $plainPassword = $form->get('password')->getData();
            if ($plainPassword) {
                $user->setPassword(
                    password_hash($plainPassword, PASSWORD_BCRYPT)
                );
            }

            $entityManager->flush();

            $this->addFlash('success', 'Profil du restaurateur mis à jour avec succès.');

            return $this->redirectToRoute('admin_restaurateur_show', ['id' => $user->getId()]);
        }

        return $this->render('admin/profile/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    // Créer un nouveau restaurateur
    #[Route('/new', name: 'new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $user->setRoles(['ROLE_RESTAURANT']);
        $restaurant = new Restaurant();
        $user->setRestaurant($restaurant);
        $restaurant->setUser($user);

        $form = $this->createForm(AdminRestaurateurProfileType::class, $user, ['is_new' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gestion du mot de passe
            $plainPassword = $form->get('password')->getData();
            if ($plainPassword) {
                $user->setPassword(
                    password_hash($plainPassword, PASSWORD_BCRYPT)
                );
            }

            $entityManager->persist($user);
            $entityManager->persist($restaurant);
            $entityManager->flush();

            $this->addFlash('success', 'Restaurateur créé avec succès.');

            return $this->redirectToRoute('admin_restaurateur_list');
        }

        return $this->render('admin/profile/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // Changer le statut d'un restaurateur
    #[Route('/{id}/change-status', name: 'change_status', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function changeStatus(Request $request, User $user, EntityManagerInterface $entityManager): Response
      {
          $this->denyAccessUnlessGranted('ROLE_ADMIN');
  
          $newStatusValue = $request->request->get('status');
  
          // Vérifier que le statut est valide
          if (!Status::tryFrom($newStatusValue)) {
              $this->addFlash('error', 'Statut invalide.');
              return $this->redirectToRoute('admin_dashboard');
          }
  
          $newStatus = Status::from($newStatusValue);
  
          $user->setStatus($newStatus);
  
          // Si le nouvel état est ARCHIVE et que l'utilisateur est un restaurateur
          if ($newStatus === Status::ARCHIVE && in_array('ROLE_RESTAURANT', $user->getRoles(), true)) {
              $restaurant = $user->getRestaurant();
              if ($restaurant) {
                  // Archiver le restaurant
                  $restaurant->setStatus(Status::ARCHIVE);
  
                  // Archiver les produits du restaurant
                  foreach ($restaurant->getProducts() as $product) {
                      $product->setStatus(Status::ARCHIVE);
                  }
              }
          }
  
          $entityManager->flush();
  
          $this->addFlash('success', 'Le statut de l\'utilisateur a été mis à jour.');
          return $this->redirectToRoute('admin_dashboard');
      }
 
}
