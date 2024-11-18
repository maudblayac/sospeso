<?php

namespace App\Controller;

use App\Entity\UserProfile;
use App\Entity\User;
use App\Enum\Status;
use App\Form\UserProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;


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

   // DELETE
   #[Route('/delete', name: 'delete', methods: ['POST'])]
    public function archiveAccount(Request $request, EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage): Response
    {
        // dd('Méthode archiveAccount appelée');
        /** @var User $user */
        $user = $this->getUser();

        if ($this->isCsrfTokenValid('archive-account', $request->request->get('_token'))) {
            // Marquer l'utilisateur comme archivé en changeant son statut
            $user->setStatus(Status::ARCHIVE);

            // Marquer le profil utilisateur comme archivé si nécessaire
            if ($user->getUserProfile()) {

            }

            // Invalider la session et déconnecter l'utilisateur
            $tokenStorage->setToken(null);
            $request->getSession()->invalidate();

            $entityManager->flush();
            $this->addFlash('success', 'Votre compte a été archivé avec succès.');

            return $this->redirectToRoute('app_home');
        }

        $this->addFlash('error', 'L’archivage du compte a échoué.');
        return $this->redirectToRoute('app_user_profile_index');
    }

}
