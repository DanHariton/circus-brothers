<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route("/concert", name: "concert_")]
class ConcertController extends AbstractController
{
    #[Route("/", name: "list")]
    public function list(): Response
    {
        return $this->render('admin/actions/concert/list.html.twig');
    }
}