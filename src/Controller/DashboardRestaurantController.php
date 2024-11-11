<?php

namespace App\Controller;

use App\Entity\Restaurant;
use App\Entity\User;
use App\Entity\Product;
use App\Form\AccountSettingsType;
use App\Form\RestaurantProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
   public function deleteAccount(Request $request, EntityManagerInterface $entityManager): Response
   {
       /** @var User $user */
       $user = $this->getUser();

       if ($this->isCsrfTokenValid('delete_account', $request->request->get('_token'))) {
           // Déconnecter l'utilisateur
           $this->container->get('security.token_storage')->setToken(null);
           $request->getSession()->invalidate();

           // Supprimer l'utilisateur
           $entityManager->remove($user);
           $entityManager->flush();

           $this->addFlash('success', 'Votre compte a été supprimé avec succès.');

           return $this->redirectToRoute('app_home'); // Redirigez vers la page d'accueil ou une autre page
       }

       $this->addFlash('error', 'Jeton CSRF invalide.');
       return $this->redirectToRoute('app_dashboard_restaurant_account_settings');
   }

    // Gestion des produits
    #[Route('/products', name: 'products')]
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
    public function restaurant(Request $request, EntityManagerInterface $entityManager): Response
    {   
         /** @var User $user */
        $user = $this->getUser();
        $restaurant = $user->getRestaurant();

        if (!$restaurant) {
            return $this->redirectToRoute('app_restaurant_new');
        }

        return $this->render('dashboard_restaurant/restaurant.html.twig', [
            'restaurant' => $restaurant,
        ]);
    }

    // Page de profil
    #[Route('/profile', name: 'profile')]
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
