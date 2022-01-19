<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Film;


class NoteChartController extends AbstractController
{
    /**
     * @Route("/note/chart", name="note_chart")
     */
    public function index(): Response
    {
        $repo = $this->getDoctrine()->getRepository(Film::class);
        $films = $repo->findAll();
        $scores = [];

        for ($i = 1; $i <= 10; $i++) {
          $filtredArray = array_filter($films, fn($film) => intval($film->getNote()) === intval($i));
          $countScores = count($filtredArray);
          $scores[] = $countScores;
        }
          
        
        return $this->render('note_chart/index.html.twig', [
            'scores' => $scores
        ]);
    }
}
