<?php

namespace App\Controller;

use App\Entity\Restaurant;
use App\Form\RestaurantType;
use App\Repository\RestaurantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[Route('/restaurant', name: 'app_restaurant_')]
#[IsGranted(new Expression('is_granted("ROLE_RESTAURANT") or is_granted("ROLE_ADMIN")'))]
class RestaurantController extends AbstractController
{
    // Index 
    #[Route('/', name: 'home', methods: ['GET'])]
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

        return $this->render('restaurant/index.html.twig', [
            'restaurants' => $restaurantData,
        ]);
    }

    //new
    #[Route('/new', name: 'app_restaurant_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_RESTAURANT')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $restaurant = new Restaurant();
        $form = $this->createForm(RestaurantType::class, $restaurant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($restaurant);
            $entityManager->flush();

            $this->addFlash('success', 'Restaurant created successfully!');
            return $this->redirectToRoute('app_restaurant_index');
        }

        return $this->render('restaurant/new.html.twig', [
            'restaurant' => $restaurant,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_restaurant_show', methods: ['GET'])]
    public function show(Restaurant $restaurant): Response
    {
        return $this->render('restaurant/show.html.twig', [
            'restaurant' => $restaurant,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_restaurant_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_RESTAURANT')]
    public function edit(Request $request, Restaurant $restaurant, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RestaurantType::class, $restaurant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Restaurant updated successfully!');
            return $this->redirectToRoute('app_restaurant_index');
        }

        return $this->render('restaurant/edit.html.twig', [
            'restaurant' => $restaurant,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_restaurant_delete', methods: ['POST'])]
    #[IsGranted('ROLE_RESTAURANT')]
    public function delete(Request $request, Restaurant $restaurant, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$restaurant->getId(), $request->request->get('_token'))) {
            $entityManager->remove($restaurant);
            $entityManager->flush();

            $this->addFlash('success', 'Restaurant deleted successfully!');
        }

        return $this->redirectToRoute('app_restaurant_index');
    }
}
