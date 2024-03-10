<?php

namespace App\Controller\Site;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

class AppController extends AbstractController
{
    #[Route("/", name: "index")]
    public function index()
    {
        return $this->render('site/app/index.html.twig');
    }
}