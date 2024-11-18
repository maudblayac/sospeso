<?php
namespace App\Security;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use App\Enum\Status;
use App\Entity\User;



class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof User) {
            return;
        }

        // Si l'utilisateur est archivé, l'authentification est bloquée
        if ($user->getStatus() === Status::ARCHIVE) {
            throw new CustomUserMessageAuthenticationException('Votre compte est archivé et ne peut pas se connecter.');
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
        // Aucune vérification supplémentaire n'est nécessaire après l'authentification
    }
}
