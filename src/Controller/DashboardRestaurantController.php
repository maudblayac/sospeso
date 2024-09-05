<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardRestaurantController extends AbstractController
{
    #[Route('/dashboard/restaurant', name: 'app_dashboard_restaurant')]
    public function index(): Response
    {
        return $this->render('dashboard_restaurant/index.html.twig', [
            'controller_name' => 'DashboardRestaurantController',
        ]);
    }
}
