<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MovielistController extends AbstractController
{
    #[Route('/movielist', name: 'app_movielist')]
    public function index(): Response
    {
        return $this->render('movielist/index.html.twig', [
            'controller_name' => 'MovielistController',
        ]);
    }
}
