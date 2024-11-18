<?php
namespace App\Controller\Administration;

use App\Entity\Restaurant;
use App\Entity\FeaturedProduct;
use App\Form\AdminRestaurantListingType;
use App\Repository\RestaurantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/restaurants', name: 'admin_restaurant_')]
class AdminListingRestaurantController extends AbstractController
{
    // Liste des annonces de restaurants
    #[Route('/', name: 'list')]
    public function list(RestaurantRepository $restaurantRepository): Response
    {
        $restaurants = $restaurantRepository->findAll();

        return $this->render('admin/restaurant/list.html.twig', [
            'restaurants' => $restaurants,
        ]);
    }

    // Afficher une annonce de restaurant
    #[Route('/{id}', name: 'show')]
    public function show(Restaurant $restaurant): Response
    {
        return $this->render('admin/restaurant/show.html.twig', [
            'restaurant' => $restaurant,
        ]);
    }

  // Modifier une annonce de restaurant
    #[Route('/{id}/edit', name: 'edit')]
    public function edit(Request $request, Restaurant $restaurant, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AdminRestaurantListingType::class, $restaurant, [
            'restaurant' => $restaurant,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->handleFeaturedProducts($form, $restaurant, $entityManager);
            $entityManager->flush();

            $this->addFlash('success', 'Annonce mise à jour avec succès !');
            return $this->redirectToRoute('admin_restaurateur_show', ['id' => $restaurant->getUser()->getId()]);
        }

        return $this->render('restaurant/edit.html.twig', [
            'form' => $form->createView(),
            'restaurant' => $restaurant,
        ]);
    }


    // Créer une nouvelle annonce de restaurant
    #[Route('/new', name: 'new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $restaurant = new Restaurant();

        $form = $this->createForm(AdminRestaurantListingType::class, $restaurant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($restaurant);
            $entityManager->flush();

            $this->addFlash('success', 'Annonce du restaurant créée avec succès.');

            return $this->redirectToRoute('admin_restaurant_list');
        }

        return $this->render('admin/profile/show.html.twig', [
            'form' => $form->createView(),
        ]);
    }
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
}
