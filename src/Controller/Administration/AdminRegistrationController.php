<?php
namespace App\Controller\Administration;

use App\Entity\User;
use App\Entity\Restaurant;
use App\Form\AdminRegisterType;
use App\Form\AdminRestaurateurType;
use App\Enum\Status;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[Route('/admin/users', name: 'admin_register_')]
class AdminRegistrationController extends AbstractController
{
    #[Route('/new', name: 'new')]
    public function new(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $user = new User();
        $restaurant = new Restaurant();
        $user->setRestaurant($restaurant);
        $restaurant->setUser($user);

        $form = $this->createForm(AdminRegisterType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setRoles(['ROLE_RESTAURANT']);
            $user->setStatus(Status::VERIFIE);

            $plainPassword = $form->get('password')->getData();
            if ($plainPassword) {
                $hashedPassword = $userPasswordHasher->hashPassword(
                    $user,
                    $plainPassword
                );
                $user->setPassword($hashedPassword);
            }

            $entityManager->persist($user);
            $entityManager->persist($restaurant);
            $entityManager->flush();

            $this->addFlash('success', 'Restaurateur créé avec succès.');

            return $this->redirectToRoute('admin_restaurateur_show', ['id' => $user->getId()]);
        
        }
        return $this->render('admin/user/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}