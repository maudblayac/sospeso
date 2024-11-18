<?php
namespace App\Controller\Administration;

use App\Entity\Product;
use App\Entity\Restaurant;
use App\Entity\User;
use App\Enum\Status;
use App\Repository\ProductRepository;
use App\Repository\RestaurantRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/demandes', name: 'admin_demandes_')]
class AdminDemandeController extends AbstractController
{
    #[Route('/', name: 'list')]
    public function list(UserRepository $userRepository, RestaurantRepository $restaurantRepository, ProductRepository $productRepository)
    {
        $pendingUsers = $userRepository->findBy(['status' => Status::EN_ATTENTE]);

        $pendingRestaurants = $restaurantRepository->findBy(['isApproved' => false]);

        $pendingProducts = $productRepository->findBy(['isApproved' => false]);

        return $this->render('admin/demandes/list.html.twig', [
            'pendingUsers' => $pendingUsers,
            'pendingRestaurants' => $pendingRestaurants,
            'pendingProducts' => $pendingProducts,
        ]);
    }
}
