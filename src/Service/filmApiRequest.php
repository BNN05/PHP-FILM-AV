<?php
namespace App\Service;

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

class filmApiRequest
{

    public function getFilmApi(String $filmName,HttpClientInterface $client)
    {
        $film = new Film();
        $response = $client->request('GET', 'https://www.omdbapi.com/?apikey=4eb53ca7&t=' .$filmName, [
            'headers' => [
                'Accept' => 'application/json',
            ],]);
        
        $data = $response->toArray();
        if ($data['Response']==="True"){ 
            
            $film->setName($data['Title']);
            $film->setDescription($data['Plot']);
            $film->setNbVote(intval(preg_replace('/[^0-9]/', '', $data['imdbVotes'])));
        }else{
            return null;
        }

        return $film;
    }
}
?>