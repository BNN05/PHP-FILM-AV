<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Film;

class RemoveFilmController extends AbstractController
{
    /**
     * @Route("/remove/film", name="remove_film")
     */
    public function index(): Response
    {

        $id = $_GET['id'];
        $film = $this->getDoctrine()
            ->getRepository(Film::class)
            ->find($id);
        
    
        

        return $this->render('remove_film/index.html.twig', [
            'controller_name' => 'RemoveFilmController',
            'filmName' => $film->getName(),
        ]);
    }

}
