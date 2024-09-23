<?php

namespace App\Controller;

use App\Repository\RestaurantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RestaurantController extends AbstractController
{
    #[Route('/restaurant', name: 'app_restaurant')]
    public function index(RestaurantRepository $restaurantRepository): Response
    {
        // Récupérer tous les restaurants
        $restaurants = $restaurantRepository->findAll();

        // Pour chaque restaurant, on va ajouter les prix min et max
        $restaurantData = [];
        foreach ($restaurants as $restaurant) {
            // Récupère les prix min et max pour chaque restaurant
            $prices = $restaurantRepository->findMinMaxPriceByRestaurant($restaurant->getId());

            // Ajouter les données du restaurant avec les prix
            $restaurantData[] = [
                'restaurant' => $restaurant,
                'minPrice' => $prices['minPrice'],
                'maxPrice' => $prices['maxPrice']
            ];
        }

        return $this->render('_partials/listingRestaurant/_card.html.twig', [
            'restaurants' => $restaurantData,
        ]);
    }
}
