<?php

namespace App\Controller;

use App\Entity\Restaurant;
use App\Entity\User;
use App\Enum\Status;
use App\Entity\Product;
use App\Form\AccountSettingsType;
use App\Form\RestaurantProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Form\RestaurantListingType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/dashboard', name: 'app_dashboard_restaurant_')]
#[IsGranted(new Expression('is_granted("ROLE_RESTAURANT") or is_granted("ROLE_ADMIN")'))]
class DashboardRestaurantController extends AbstractController
{
    // Page d'accueil du Dashboard
    #[Route('/', name: 'home')]
    public function home(): Response
    {
        return $this->render('dashboard_restaurant/home.html.twig', [
            'user' => $this->getUser(),
        ]);
    }
   // Page des paramètres du compte
   #[Route('/account-settings', name: 'account_settings')]
   #[IsGranted(new Expression('is_granted("ROLE_RESTAURANT") or is_granted("ROLE_ADMIN")'))]
   public function accountSettings(Request $request, EntityManagerInterface $entityManager): Response
   {
       /** @var User $user */
       $user = $this->getUser();
       $restaurant = $user->getRestaurant();

        // Vérifiez que le restaurant existe
        if (!$restaurant) {
            $this->addFlash('error', 'Vous n\'avez pas de restaurant associé à votre compte.');
            return $this->redirectToRoute('app_dashboard_restaurant_home');
        }

        // Créer le formulaire
        $form = $this->createForm(AccountSettingsType::class, $restaurant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($restaurant);
            $entityManager->flush();

            $this->addFlash('success', 'Paramètres du compte mis à jour avec succès.');
            return $this->redirectToRoute('app_dashboard_restaurant_account_settings');
        }

       return $this->render('dashboard_restaurant/accountSettings.html.twig', [
           'user' => $user,
           'form' => $form->createView(),
       ]);
    }

   // Méthode pour supprimer le compte utilisateur
   #[Route('/account/delete', name: 'account_delete', methods: ['POST'])]
   #[IsGranted(new Expression('is_granted("ROLE_RESTAURANT") or is_granted("ROLE_ADMIN")'))]
   public function deleteAccount(Request $request, EntityManagerInterface $entityManager): Response
{
    /** @var User $user */
    $user = $this->getUser();
    $restaurant = $user->getRestaurant();

    if ($this->isCsrfTokenValid('delete_account', $request->request->get('_token'))) {
        // Marquer l'utilisateur comme archivé
        $user->setStatus(Status::ARCHIVE);

        // Marquer le restaurant comme archivé
        if ($restaurant) {
            $restaurant->setStatus(Status::ARCHIVE);

            // Marquer les produits du restaurant comme archivés
            foreach ($restaurant->getProducts() as $product) {
                $product->setStatus(Status::ARCHIVE);
            }
        }

        // Déconnecter l'utilisateur et invalider la session
        $this->container->get('security.token_storage')->setToken(null);
        $request->getSession()->invalidate();

        // Enregistrer les modifications
        $entityManager->flush();

        $this->addFlash('success', 'Votre compte a été archivé avec succès.');
        return $this->redirectToRoute('app_home');
    }

    $this->addFlash('error', 'Jeton CSRF invalide.');
    return $this->redirectToRoute('app_dashboard_restaurant_account_settings');
}

    // Gestion des produits
    #[Route('/products', name: 'products')]
    #[IsGranted(new Expression('is_granted("ROLE_RESTAURANT") or is_granted("ROLE_ADMIN")'))]
    public function products(EntityManagerInterface $em): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $products = $em->getRepository(Product::class)->findBy(['restaurant' => $user->getRestaurant()]);

        return $this->render('dashboard_restaurant/products.html.twig', [
            'products' => $products,
        ]);
    }

    
    // Page pour gérer l'annonce de restaurant
    #[Route('/restaurant', name: 'restaurant')]
    #[IsGranted(new Expression('is_granted("ROLE_RESTAURANT") or is_granted("ROLE_ADMIN")'))]
    public function restaurant(Request $request, EntityManagerInterface $entityManager): Response
    {   
        /** @var User $user */
        $user = $this->getUser();
        $restaurant = $user->getRestaurant();

        // Si l'utilisateur n'a pas encore de restaurant, redirection vers la création
        if (!$restaurant) {
            return $this->redirectToRoute('app_restaurant_new');
        }

        // S'il y a déjà une annonce
        if ($restaurant->hasListing()) {
            return $this->render('dashboard_restaurant/restaurant.html.twig', [
                'restaurant' => $restaurant,
            ]);
        }

        // Vérifie si le restaurant a au moins un produit**
        // if ($restaurant->getProducts()->isEmpty()) {
        //     $this->addFlash('error', 'Vous devez créer au moins un produit avant de pouvoir publier votre annonce.');
        //     return $this->redirectToRoute('app_dashboard_restaurant_products');
    

        // Si aucune annonce n'existe, affiche le formulaire de création
        $form = $this->createForm(RestaurantListingType::class, $restaurant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $restaurant->setHasListing(true);
            $restaurant->setIsApproved(false);
            $entityManager->persist($restaurant);
            $entityManager->flush();

            $this->addFlash('success', 'Votre annonce a été créée et est en attente d\'approbation.');
            return $this->redirectToRoute('app_dashboard_restaurant_restaurant');
        }

        return $this->render('dashboard_restaurant/restaurant.html.twig', [
            'form' => $form->createView(),
            'restaurant' => $restaurant,
        ]);
    }

    // Page de profil
    #[Route('/profile', name: 'profile')]
    #[IsGranted(new Expression('is_granted("ROLE_RESTAURANT") or is_granted("ROLE_ADMIN")'))]
    public function profile(Request $request, EntityManagerInterface $entityManager): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $restaurant = $user->getRestaurant();

        $form = $this->createForm(RestaurantProfileType::class, $restaurant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Profil mis à jour avec succès.');
        }

        return $this->render('dashboard_restaurant/profile.html.twig', [
            'form' => $form->createView(),
            'restaurant' => $restaurant,
        ]);
    }
}
