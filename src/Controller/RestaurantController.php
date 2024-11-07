<?php

namespace App\Controller;

use App\Entity\Restaurant;
use App\Form\RestaurantListingType;
use App\Repository\RestaurantRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\RestaurantSearchType;
use App\DTO\RestaurantSearchDto;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/restaurant', name: 'app_restaurant_')]
class RestaurantController extends AbstractController
{
    // Route publique pour afficher tous les restaurants validés
    #[Route('/public', name: 'public_index', methods: ['GET', 'POST'])]
    public function publicIndex(Request $request, RestaurantRepository $restaurantRepository): Response
    {
        // Créer l'instance du DTO pour les critères de recherche
        $searchDto = new RestaurantSearchDto();
        $form = $this->createForm(RestaurantSearchType::class, $searchDto, [
            'method' => 'GET',
        ]);

        // Traiter la requête pour récupérer les données du formulaire
        $form->handleRequest($request);

        // Filtrer les restaurants complets et approuvés en fonction des critères de recherche
        $restaurants = $form->isSubmitted() && $form->isValid()
            ? $restaurantRepository->searchByCriteria($searchDto, true) // Inclut seulement les annonces approuvées
            : $restaurantRepository->findBy(['hasListing' => true, 'isApproved' => true]);

        // Préparer les données des restaurants (avec min et max des prix)
        $restaurantData = $this->getRestaurantData($restaurants);

        return $this->render('restaurant/public_index.html.twig', [
            'form' => $form->createView(),
            'restaurants' => $restaurantData,
        ]);
    }

    // Route pour les utilisateurs autorisés (restaurateurs et administrateurs)
    #[Route('/index', name: 'index', methods: ['GET'])]
    #[IsGranted(new Expression('is_granted("ROLE_RESTAURANT") or is_granted("ROLE_ADMIN")'))]
    public function index(RestaurantRepository $restaurantRepository): Response
    {
        // Administrateurs voient tous les restaurants; restaurateurs voient seulement leurs propres restaurants
        $restaurants = $this->isGranted('ROLE_ADMIN') 
            ? $restaurantRepository->findAll() 
            : $restaurantRepository->findBy(['user' => $this->getUser()]);

        $restaurantData = $this->getRestaurantData($restaurants);

        return $this->render('restaurant/index.html.twig', [
            'restaurants' => $restaurantData,
        ]);
    }

    // Créer un nouveau restaurant
    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_RESTAURANT')]
    public function new(Request $request, RestaurantRepository $restaurantRepository, EntityManagerInterface $entityManager): Response
    {
        // Vérifie si un restaurant "complet" existe déjà pour cet utilisateur
        $existingRestaurant = $restaurantRepository->findOneBy(['user' => $this->getUser(), 'hasListing' => true]);
        
        if ($existingRestaurant) {
            $this->addFlash('error', 'Vous avez déjà un restaurant avec une annonce complète.');
            return $this->redirectToRoute('app_restaurant_index');
        }

        // Sinon, récupère un restaurant partiel ou en crée un nouveau
        $restaurant = $restaurantRepository->findOneBy(['user' => $this->getUser()]) ?? new Restaurant();
        
        // Crée le formulaire pour compléter l’annonce
        $form = $this->createForm(RestaurantListingType::class, $restaurant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $restaurant->setUser($this->getUser()); 
            $restaurant->setHasListing(true); // Marque l'annonce comme complète
            $restaurant->setIsApproved(false); // Attend la validation de l'admin
            $entityManager->persist($restaurant);
            $entityManager->flush();

            $this->addFlash('success', 'Restaurant créé avec succès ! En attente de validation.');
            return $this->redirectToRoute('app_restaurant_index');
        }

        return $this->render('restaurant/new.html.twig', [
            'restaurant' => $restaurant,
            'form' => $form->createView(),
        ]);
    }

    // Méthode privée pour construire les données de restaurant avec les prix min et max
    private function getRestaurantData(array $restaurants): array
    {
        $restaurantData = [];
        foreach ($restaurants as $restaurant) {
            $prices = $restaurant->getProducts()->isEmpty() ? ['minPrice' => null, 'maxPrice' => null] : [
                'minPrice' => $restaurant->getProducts()->first()->getPrice(),
                'maxPrice' => $restaurant->getProducts()->first()->getPrice(),
            ];
            foreach ($restaurant->getProducts() as $product) {
                $prices['minPrice'] = min($prices['minPrice'], $product->getPrice());
                $prices['maxPrice'] = max($prices['maxPrice'], $product->getPrice());
            }

            $restaurantData[] = [
                'restaurant' => $restaurant,
                'minPrice' => $prices['minPrice'],
                'maxPrice' => $prices['maxPrice'],
            ];
        }

        return $restaurantData;
    }
}
