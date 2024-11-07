<?php

namespace App\Controller;

use App\Entity\Restaurant;
use App\Entity\User;
use App\Entity\Product;
use App\Form\RestaurantProfileType;
// use App\Form\RestaurantListingType;
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
    #[Route('/', name: 'home')]
    public function home(): Response
    {
        return $this->render('dashboard_restaurant/home.html.twig', [
            'user' => $this->getUser(),
        ]);
    }

    // Gestion du profil du restaurant
    #[Route('/profile', name: 'profile', methods: ['GET', 'POST'])]
    public function profile(Request $request, EntityManagerInterface $entityManager): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $restaurant = $user->getRestaurant();

        if (!$restaurant) {
            $restaurant = new Restaurant();
            $restaurant->setUser($user);
            $entityManager->persist($restaurant);
            $entityManager->flush();
        }

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

    //gestion des produits par le restaurateur 
    #[Route('/products', name: 'products')]
    public function products(EntityManagerInterface $em): Response
    {                
        /** @var User $user */
        $user = $this->getUser();
        // Récupérer les produits du restaurant du user connecté
        $products = $em->getRepository(Product::class)->findBy(['restaurant' => $user->getRestaurant()]);

        return $this->render('dashboard_restaurant/products.html.twig', [
            'products' => $products,
        ]);
    }

    #[Route('/restaurant', name: 'restaurant')]
    public function restaurant(): Response
    {
        return $this->render('dashboard_restaurant/restaurant.html.twig');
    }
}
