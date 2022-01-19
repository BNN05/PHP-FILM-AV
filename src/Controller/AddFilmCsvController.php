<?php

namespace App\Controller;

use App\Service\filmApiRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Film;
use App\Form\FilmType;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;

class AddFilmCsvController extends AbstractController
{
    /**
     * @Route("/add/film/csv", name="add_film_csv")
     */
    public function index(Request $request, filmApiRequest $filmApiRequest,HttpClientInterface $client,ManagerRegistry $doctrine, NotifierInterface $notifier): Response
    {
        $form = $this->createFormBuilder()
            ->add('file', \Symfony\Component\Form\Extension\Core\Type\FileType::class)
            ->add('submit', \Symfony\Component\Form\Extension\Core\Type\SubmitType::class)
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('file')->getData();
            $fileName = $file->getClientOriginalName();
            
            $file = fopen($file, 'r');
            $em = $this->getDoctrine()->getManager();
            while (($line = fgetcsv($file)) !== false) {
                if($doctrine->getRepository(Film::class)->findOneBy(['name' => $line[0]]) == null){
                    $film = new Film();
                    $film->setName($line[0]);
                    $film->setDescription($line[1]);
                    $film->setNote($line[2]);
                    $film->setNbVote($line[3]);
                    $em->persist($film);
                }
            }
            fclose($file);
            $em->flush();
            return $this->redirectToRoute('homepage');
        }
        return $this->render('add_film_csv/index.html.twig', [
            'controller_name' => 'AddFilmCsvController',
            'form' => $form->createView(),
        ]);
    }
}
