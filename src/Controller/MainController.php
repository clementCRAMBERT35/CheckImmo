<?php

namespace App\Controller;

use App\Repository\AnnoncesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="app_index")
     */
    public function index(AnnoncesRepository $annonceRepository)
    {
        $currentUser = $this->getUser();
        if ($currentUser)
            return $this->render('main/index.html.twig', [
                'controller_name' => 'HomeController',
                'annonces' => $annonceRepository->findByUser($this->getUser())
            ]);
        else
            return $this->render('main/index.html.twig', [
                'controller_name' => 'HomeController',
            ]);
    }
}
