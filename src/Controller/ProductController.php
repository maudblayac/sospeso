<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\User;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/product', name: 'app_product_')]
#[IsGranted(new Expression('is_granted("ROLE_RESTAURANT") or is_granted("ROLE_ADMIN")'))]
class ProductController extends AbstractController
{
    // Index 
    #[Route('/', name:'index', methods: ['GET'])]
    public function index(ProductRepository $productRepository): Response
    {
        // chercher user qui est relié a son restaurant puis product
        /** @var User $user */
        $user = $this->getUser();
        $restaurant = $user->getRestaurant();
        $products = $productRepository->findBy(['restaurant' => $restaurant]);

        return $this->render('product/index.html.twig', [
            'products' => $products,
        ]);
    }
    
    // Méthode New

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    #[IsGranted(new Expression('is_granted("ROLE_RESTAURANT") or is_granted("ROLE_ADMIN")'))]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            try {
                // Récupération de l'utilisateur connecté et assignation du restaurant
                /** @var User $user */
                $user = $this->getUser();
                $restaurant = $user->getRestaurant();
                $product->setRestaurant($restaurant);

                // Persister le produit dans la base de donnée :)
                $entityManager->persist($product);
                $entityManager->flush();

                // Message de succès
                $this->addFlash('success', [
                    'title' => 'Produit créé ',
                    'message' => "Votre produit a été créé avec succès !",
                ]);

                return $this->redirectToRoute('app_dashboard_restaurant_products');            
            } catch (\Exception $e) {
                // Gestion des erreurs à revoir 
                $this->addFlash('error', 'Une erreur est survenue: ' . $e->getMessage());
            }
        }
        
        return $this->render('product/new.html.twig', [
            'form' => $form,
        ]);
    }

    // Méthode Update 

    #[Route('/update/{id}', name: 'update', methods: ['GET', 'POST'])]
        // ici je récupère spécifiquement le user qui est assigné à son restaurant -> product
    #[IsGranted(
        attribute: new Expression(
            'user === subject.getRestaurant().getUser() or is_granted("ROLE_ADMIN")'
        ),
        subject: 'product'
    )]
    public function update(
        Product $product,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        // Créer le formulaire 
        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', [
                'title' => 'Produit mis à jour',
                'message' => "Votre produit a été mis à jour avec succès !",
            ]);

            return $this->redirectToRoute('app_dashboard_restaurant_products');
        }

        return $this->render('product/update.html.twig', [
            'form' => $form,
            'product' => $product,
        ]);
    }


    //Méthode Show

    #[Route("/show/{id}", name: 'show', methods: ['GET'])]
    #[IsGranted(new Expression('is_granted("ROLE_RESTAURANT") or is_granted("ROLE_ADMIN")'))]
    public function show(Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

    //Méthode delete

    #[Route("/remove/{id}", name: 'delete', methods: ['POST'])]
    // ici je récupère spécifiquement le user qui est assigné à son restaurant -> product
    #[IsGranted(
        attribute: new Expression(
          'user === subject.getRestaurant().getUser() or is_granted("ROLE_ADMIN")'
        ),
        subject: 'product'
    )]
    public function remove(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
       
        if ($this->isCsrfTokenValid('delete-product' . $product->getId(), $request->request->get('_token'))) {
            $entityManager->remove($product);
            $entityManager->flush();
            $this->addFlash('success', 'Produit supprimé avec succès!');
        }

        return $this->redirectToRoute('app_dashboard_restaurant_products');
    }
}
