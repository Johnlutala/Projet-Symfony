<?php

namespace App\Controller;

use App\Repository\RecettesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(
        RecettesRepository $repository
    ): Response
    {
        $recettes = $repository->findPublicRecette(3);
      
        
        return $this->render('pages/home/index.html.twig', [
             'recettes'=> $recettes
            
        ]);
    }
}
