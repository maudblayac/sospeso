<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/dashboard', name: 'app_dashboard_restaurant_')]
#[IsGranted(new Expression('is_granted("ROLE_RESTAURANT") or is_granted("ROLE_ADMIN")'))]
class DashboardRestaurantController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function home(): Response
    {
        $user = $this->getUser();
        return $this->render('dashboard_restaurant/home.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/profile', name: 'profile')]
    public function profile(): Response
    {
        return $this->render('dashboard_restaurant/profile.html.twig');
    }

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
