<?php
namespace App\Controller\Administration;

use App\Entity\User;
use App\Entity\Restaurant;
use App\Repository\RestaurantRepository;
use App\Enum\UserStatus;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin', name: 'admin_')]
class AdminController extends AbstractController
{
    #[Route('/dashboard', name: 'dashboard')]
    public function dashboard(UserRepository $userRepository, RestaurantRepository $restaurantRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        // Récupérer les utilisateurs avec le rôle ROLE_RESTAURANT
        $users = $userRepository->findByRole('ROLE_RESTAURANT');

        // Récupérer les restaurants pour l'affichage des annonces à valider
        $restaurants = $restaurantRepository->findAll();

        $userStatusValues = UserStatus::cases();

        return $this->render('admin/dashboard.html.twig', [
            'users' => $users,
            'restaurants' => $restaurants,
            'userStatusValues' => $userStatusValues
        ]);
    }
    // Méthode pour changer le statut d'un utilisateur
    #[Route('/user/{id}/change-status', name: 'user_change_status', methods: ['POST'])]
    public function changeUserStatus(User $user, Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $newStatus = $request->request->get('status');

        if (!in_array('ROLE_RESTAURANT', $user->getRoles(), true)) {
            $this->addFlash('error', 'Statut invalide.');
            return $this->redirectToRoute('admin_dashboard');
        }

        $user->setStatus(UserStatus::from($newStatus));
        $entityManager->flush();

        $this->addFlash('success', 'Le statut de l\'utilisateur a été mis à jour.');

        return $this->redirectToRoute('admin_dashboard');
    }
    #[Route('/restaurant/{id}/approve', name: 'restaurant_approve', methods: ['POST'])]
    public function approveRestaurant(Restaurant $restaurant, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $restaurant->setIsApproved(true);
        $entityManager->flush();

        $this->addFlash('success', 'Le restaurant a été approuvé.');

        return $this->redirectToRoute('admin_dashboard');
    }
  
}
