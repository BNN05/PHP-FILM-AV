<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Film;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;

class FilmDetailsController extends AbstractController
{

    private string $admin_token;

    public function __construct(string $admin_token)
    {
        $this->admin_token = $admin_token;
    }

    /**
     * @Route("/film/details", name="film_details")
     */
    public function index(Request $request, NotifierInterface $notifier): Response
    {
        $id = $_GET['id'];
        $film = $this->getDoctrine()
            ->getRepository(Film::class)
            ->find($id);

        $form = $this->createFormBuilder()
            ->add('code', TextType::class)
            ->add('supprimer', SubmitType::class)
            ->getForm();
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            if($form->get('code')->getData() == $this->admin_token){
                
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($film);
            $entityManager->flush();
            return $this->redirectToRoute('homepage');
            }else{
                $notifier->send(new Notification('Mauvais code admin !',  ['browser']));
            }
        }

        return $this->render('film_details/index.html.twig', [
            'controller_name' => 'FilmDetailsController',
            'filmId' => $film->getId(),
            'filmName' => $film->getName(),
            'filmDescription' => $film->getDescription(),
            'filmNote' => $film->getNote(),
            'filmNbVotes' => $film->getNbVote(),
            'form' => $form->createView(),
        ]);
    }
}
