<?php

namespace App\Controller;

use App\DTO\RestaurantSearchDto;
use App\Form\RestaurantSearchType;
use App\Repository\RestaurantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RestaurantSearchController extends AbstractController
{
    #[Route('/restaurants', name: 'restaurant_search')]
    public function index(Request $request, RestaurantRepository $restaurantRepository): Response
    {
        $searchDto = new RestaurantSearchDto();
        $form = $this->createForm(RestaurantSearchType::class, $searchDto);
        $form->handleRequest($request);

        $restaurants = [];

        if ($form->isSubmitted() && $form->isValid()) {
            // Appel au repository pour rechercher les restaurants en fonction des critères du DTO
            $restaurants = $restaurantRepository->searchByCriteria($searchDto);
        }

        return $this->render('restaurant/index.html.twig', [
            'form' => $form->createView(),
            'restaurants' => $restaurants,
        ]);
    }
}
