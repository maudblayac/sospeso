<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Restaurant;
use App\Entity\UserProfile;
use App\Enum\UserStatus;
use App\Form\RegistrationFormType;
use App\Form\RegistrationRestaurantFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function registerUser(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setRoles(['ROLE_USER']);
            $user->setStatus(UserStatus::VERIFIE);
            
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            // Création d'un new UserPorfile qu'on associe directement à son utilisateur 
            $userProfile = new UserProfile();
            $userProfile->setUser($user);

            $entityManager->persist($user);
            $entityManager->persist($userProfile);
            $entityManager->flush();

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    #[Route('/register/restaurant', name: 'app_register_restaurant')]
    public function registerRestaurant(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationRestaurantFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setRoles(['ROLE_RESTAURANT']);
            $user->setStatus(UserStatus::EN_ATTENTE);

            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );
            // creation d'un restaurant pour associer le nouvel utilisateur a son id restaurant directement
            $restaurant = new Restaurant();
            $restaurant->setUser($user);

            $entityManager->persist($user);
            $entityManager->persist($restaurant);
            $entityManager->flush();

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationFormRestaurant' => $form,
        ]);
    }
}
