<?php

namespace App\Controller;

use App\Entity\UserProfile;
use App\Entity\User;
use App\Form\UserProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/userProfile', name: 'app_user_profile_')]
#[IsGranted(new Expression('is_granted("ROLE_USER") or is_granted("ROLE_ADMIN")'))]
class UserProfileController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET', 'POST'])]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Récupéreration de l'utilisateur connecté et son profil associé
        /** @var User $user */
        $user = $this->getUser();
        $userProfile = $user->getUserProfile();

        // Si l'utilisateur n'a pas encore de profil, on crée un profil vide
        if (!$userProfile) {
            $userProfile = new UserProfile();
            $userProfile->setUser($user);
            $entityManager->persist($userProfile);
            $entityManager->flush();
        }

        // Créer le formulaire de mise à jour
        $form = $this->createForm(UserProfileType::class, $userProfile);
        $form->handleRequest($request);

        // Si le formulaire est soumis et valide, on met à jour le profil
        if ($form->isSubmitted() && $form->isValid()) {
            $userProfile->setUpdateAt(new \DateTimeImmutable()); 
            $entityManager->flush();
            $this->addFlash('success', 'Votre profil a été mis à jour avec succès.');
        }

        return $this->render('userProfile/index.html.twig', [
            'form' => $form->createView(),
            'userProfile' => $userProfile,
        ]);
    }

    //DELETE
    #[Route('/delete', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, EntityManagerInterface $entityManager): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $userProfile = $user->getUserProfile();

        if ($userProfile && $this->isCsrfTokenValid('delete-profile', $request->request->get('_token'))) {
            $entityManager->remove($userProfile);
            $entityManager->flush();

            $this->addFlash('success', 'Votre profil a été supprimé avec succès.');
        }

        return $this->redirectToRoute('app_home'); 
    }
}
