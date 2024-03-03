<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends AbstractController
{
    #[Route("/", name: "site_app_admin_redirect")]
    public function adminRedirect(): RedirectResponse
    {
        return $this->redirectToRoute("concert_list");
    }
}