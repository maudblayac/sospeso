<?php

namespace App\Controller\Administration;

use App\Entity\Product;
use App\Entity\Restaurant;
use App\Form\AdminProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin', name: 'admin_product_')]
#[IsGranted('ROLE_ADMIN')]
class AdminProductController extends AbstractController
{
    // Liste des produits
    #[Route('/', name: 'list')]
    public function productsList(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findAll();

        return $this->render('admin/product/list.html.twig', [
            'products' => $products,
        ]);
    }

    // Création d'un produit
    #[Route('/restaurant/{id}/product/new', name: 'new')]
    public function newProduct(Restaurant $restaurant, Request $request, EntityManagerInterface $entityManager): Response
    {
        $product = new Product();
        $product->setRestaurant($restaurant); 
    
        $form = $this->createForm(AdminProductType::class, $product);
    
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $product->setIsApproved(true); 
            $entityManager->persist($product);
            $entityManager->flush();
    
            $this->addFlash('success', 'Produit créé avec succès.');
    
            return $this->redirectToRoute('admin_restaurateur_show', ['id' => $restaurant->getUser()->getId()]);
        }
    
        return $this->render('admin/product/new.html.twig', [
            'form' => $form->createView(),
            'restaurant' => $restaurant,
        ]);
    }
    
    
    // Modification d'un produit
    #[Route('/{id}/edit', name: 'edit')]
    public function editProduct(Product $product, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AdminProductType::class, $product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $product->setIsApproved(true);
            $entityManager->flush();

            $this->addFlash('success', 'Produit modifié avec succès.');

            return $this->redirectToRoute('admin_restaurateur_show', ['id' => $product->getRestaurant()->getUser()->getId()]);
        }

        return $this->render('admin/product/edit.html.twig', [
            'form' => $form->createView(),
            'product' => $product,
        ]);
    }

    // Suppression d'un produit
    #[Route('/{id}/delete', name: 'delete', methods: ['POST'])]
    public function deleteProduct(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete-product' . $product->getId(), $request->request->get('_token'))) {
            $entityManager->remove($product);
            $entityManager->flush();

            $this->addFlash('success', 'Produit supprimé avec succès.');
        }

        return $this->redirectToRoute('admin_restaurateur_show', ['id' => $product->getRestaurant()->getUser()->getId()]);
    }

    // Liste des produits en attente d'approbation
    #[Route('/pending', name: 'pending')]
    public function pendingProducts(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findBy(['isApproved' => false]);

        return $this->render('admin/product/pending.html.twig', [
            'products' => $products,
        ]);
    }

    // Méthode pour approuver un produit
    #[Route('/{id}/approve', name: 'approve', methods: ['POST'])]
    public function approveProduct(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('approve-product' . $product->getId(), $request->request->get('_token'))) {
            $product->setIsApproved(true);

            $entityManager->flush();

            $this->addFlash('success', 'Produit approuvé avec succès.');
        }

        return $this->redirectToRoute('admin_products_pending');
    }
}
