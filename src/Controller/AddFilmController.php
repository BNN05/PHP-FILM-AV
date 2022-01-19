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

class AddFilmController extends AbstractController
{
    /**
     * @Route("/add/film", name="add_film")
     */
    public function new(Request $request, filmApiRequest $filmApiRequest,HttpClientInterface $client,ManagerRegistry $doctrine, NotifierInterface $notifier): Response
    {
        $film = new Film();
        $form = $this->createForm(FilmType::class, $film);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $filmSrc = $form->getData();
            $filmSrc = $filmSrc->getName();
            $requestApi = $filmApiRequest->getFilmApi($filmSrc,$client,$doctrine);
            if($requestApi != null){  
                $requestApi->setNote($form->getData()->getNote());
                $entityManager = $this->getDoctrine()->getManager();
                $filmName = $requestApi->getName();
                $requestApi->setEmail($form->getData()->getEmail());
            
                if($doctrine->getRepository(Film::class)->findOneBy(['name' => $filmName]) == null){
                    $entityManager->persist($requestApi);
                    $entityManager->flush();
                    return $this->redirectToRoute('homepage');
                }else{
                        $notifier->send(new Notification('Erreur : Le film existe deja !',  ['browser']));
                    }
            }else{
                $notifier->send(new Notification('Film non trouvé',  ['browser']));
            }
            
            
        }

        return $this->renderForm('add_film/index.html.twig', [
            'controller_name' => 'AddFilmController',
            'form' => $form,
        ]);
    }
}
