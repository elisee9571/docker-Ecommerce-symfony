<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/profile', name: 'app_profile_')]
class ProfileController extends AbstractController
{
    #[Route(name: 'index')]
    public function index(): Response
    {
        return $this->render('profile/index.html.twig', [
            'controller_name' => 'Page de profil',
        ]);
    }

    #[Route('/orders', name: 'orders')]
    public function order(): Response
    {
        return $this->render('profile/index.html.twig', [
            'controller_name' => "Commande de l'utilisateur",
        ]);
    }
}
