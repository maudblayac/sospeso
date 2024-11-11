<?php

namespace App\Controller;

use App\Entity\Restaurant;
use App\Entity\FeaturedProduct;
use App\Entity\Product;
use App\Entity\User;
use App\Form\RestaurantListingType;
use App\Repository\RestaurantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use App\Form\RestaurantSearchType;
use App\DTO\RestaurantSearchDto;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/restaurant', name: 'app_restaurant_')]
class RestaurantController extends AbstractController
{
    // Affichage des restaurants au public
    #[Route('/public', name: 'public_index', methods: ['GET', 'POST'])]
    public function publicIndex(Request $request, RestaurantRepository $restaurantRepository): Response
    {
        $searchDto = new RestaurantSearchDto();
        $form = $this->createForm(RestaurantSearchType::class, $searchDto, ['method' => 'GET']);
        $form->handleRequest($request);

        $restaurants = $form->isSubmitted() && $form->isValid()
            ? $restaurantRepository->searchByCriteria($searchDto)
            : $restaurantRepository->findBy(['hasListing' => true]);

        $restaurantData = $this->getRestaurantData($restaurants);

        return $this->render('restaurant/public_index.html.twig', [
            'form' => $form->createView(),
            'restaurants' => $restaurantData,
        ]);
    }

    // Route pour les utilisateurs autorisés
    #[Route('/index', name: 'index', methods: ['GET'])]
    #[IsGranted(new Expression('is_granted("ROLE_RESTAURANT") or is_granted("ROLE_ADMIN")'))]
    public function index(RestaurantRepository $restaurantRepository): Response
    {
        $restaurants = $this->isGranted('ROLE_ADMIN')
            ? $restaurantRepository->findAll()
            : $restaurantRepository->findBy(['user' => $this->getUser()]);

        $restaurantData = $this->getRestaurantData($restaurants);

        return $this->render('restaurant/index.html.twig', [
            'restaurants' => $restaurantData,
        ]);
    }

    // Création d'un restaurant
    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    #[IsGranted(new Expression('is_granted("ROLE_RESTAURANT") or is_granted("ROLE_ADMIN")'))]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $restaurant = new Restaurant();
        $restaurant->setUser($user);

        $form = $this->createForm(RestaurantListingType::class, $restaurant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->handleFeaturedProducts($form, $restaurant, $entityManager);
            $restaurant->setHasListing(true);
            $entityManager->persist($restaurant);
            $entityManager->flush();

            $this->addFlash('success', 'Annonce créée avec succès !');
            return $this->redirectToRoute('app_restaurant_index');
        }

        return $this->render('restaurant/new.html.twig', [
            'form' => $form->createView(),
            'restaurant' => $restaurant,
        ]);
    }

    // Modification d'une annonce de restaurant
    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    #[IsGranted(new Expression('is_granted("ROLE_RESTAURANT") or is_granted("ROLE_ADMIN")'))]
    public function edit(Request $request, Restaurant $restaurant, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RestaurantListingType::class, $restaurant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->handleFeaturedProducts($form, $restaurant, $entityManager);
            $entityManager->flush();

            $this->addFlash('success', 'Annonce mise à jour avec succès !');
            return $this->redirectToRoute('app_dashboard_restaurant_restaurant');
        }

        return $this->render('restaurant/edit.html.twig', [
            'form' => $form->createView(),
            'restaurant' => $restaurant,
        ]);
    }


    // Afficher les détails d'un restaurant
    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Restaurant $restaurant): Response
    {
        return $this->render('restaurant/show.html.twig', [
            'restaurant' => $restaurant,
        ]);
    }

    // Suppression d'un restaurant
    #[Route('/{id}/delete', name: 'delete', methods: ['POST'])]
    #[IsGranted(new Expression('is_granted("ROLE_RESTAURANT") or is_granted("ROLE_ADMIN")'))]
    public function remove(Request $request, Restaurant $restaurant, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $restaurant->getId(), $request->request->get('_token'))) {
            $entityManager->remove($restaurant);
            $entityManager->flush();
    
            $this->addFlash('success', 'Restaurant supprimé avec succès !');
        }
    
        return $this->redirectToRoute('app_restaurant_index');
    }

   // Gérer les produits en vedette
  // src/Controller/RestaurantController.php

// ... (autres use statements)

private function handleFeaturedProducts($form, Restaurant $restaurant, EntityManagerInterface $entityManager): void
{
    // Récupérer les produits sélectionnés depuis le formulaire
    $selectedProducts = $form->get('featuredProducts')->getData(); // Liste d'objets Product
    $selectedProductIds = array_map(
        fn($product) => $product->getId(),
        is_array($selectedProducts) ? $selectedProducts : $selectedProducts->toArray()
    );

    // Récupérer les FeaturedProducts actuels associés au restaurant
    $currentFeaturedProducts = $restaurant->getFeaturedProducts();

    // Supprimer les FeaturedProducts qui ne sont plus sélectionnés
    foreach ($currentFeaturedProducts as $featuredProduct) {
        $productId = $featuredProduct->getProduct()->getId();

        // Si le produit en vedette n'est plus sélectionné, on le retire
        if (!in_array($productId, $selectedProductIds, true)) {
            $restaurant->removeFeaturedProduct($featuredProduct);
            $entityManager->remove($featuredProduct);
        }
    }

    // Ajouter les nouveaux produits sélectionnés comme FeaturedProducts
    foreach ($selectedProducts as $product) {
        // Vérifier si le produit est déjà en vedette
        $isAlreadyFeatured = $currentFeaturedProducts->exists(fn($key, $fp) => $fp->getProduct()->getId() === $product->getId());

        // Ajouter le produit s'il n'est pas déjà en vedette
        if (!$isAlreadyFeatured) {
            $newFeaturedProduct = new FeaturedProduct();
            $newFeaturedProduct->setProduct($product);
            $newFeaturedProduct->setRestaurant($restaurant);
            $restaurant->addFeaturedProduct($newFeaturedProduct);
            $entityManager->persist($newFeaturedProduct);
        }
    }
}

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
