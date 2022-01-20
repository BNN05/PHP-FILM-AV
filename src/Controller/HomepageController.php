<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Film;

class HomepageController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function index(): Response
    {
        $repo = $this->getDoctrine()->getRepository(Film::class);
        $films = $repo->findAll();
        usort($films, function($a, $b) {
            if ($a->getNote() == $b->getNote()) {
                return strcmp($a->getName(), $b->getName());
            }
            return $a->getNote() < $b->getNote() ? 1 : -1;
        });
        

        return $this->render('homepage/index.html.twig', [
            'controller_name' => 'HomepageController',
            'films' => $films,
        ]);
    }
}
