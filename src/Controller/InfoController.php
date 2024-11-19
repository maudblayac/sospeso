<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class InfoController extends AbstractController
{
    public function aboutUs(): Response
    {
        return $this->render('_partials/about_us.html.twig');
    }

    public function privacyPolicy(): Response
    {
        return $this->render('_partials/privacy_policy.html.twig');
    }

    public function legalMentions(): Response
    {
        return $this->render('_partials/legal_mentions.html.twig');
    }

    public function termsOfUse(): Response
    {
        return $this->render('_partials/terms_of_use.html.twig');
    }

    public function termsOfSale(): Response
    {
        return $this->render('_partials/terms_of_sale.html.twig');
    }
}
