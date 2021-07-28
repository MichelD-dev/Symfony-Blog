<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class IndexController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        // Peut executer plusieurs instructions avant de renvoyer une réponse via le return
        return $this->render('index/index.html.twig', [
            'controller_name' => [
                1 => 'Michel',
                2 => 'Julie',
                3 => 'Matthieu'
            ]
        ]);
    }

    #[Route('/cheatsheet', name: 'index_cheatsheet')]
    public function cheatsheet(): Response
    {
        return $this->render('index/cheatsheet.html.twig');
    }

    #[Route('/square/{response}', name: 'square')]
    public function squareResponse(Request $request, $response = "carré"): Response
    {
        // La valeur par défaut "carré" fait en sorte que $Response possède toujjours une valeur. Ainsi, nous pouvons à présent accéder à notre fonction squareResponse même si nous ne marquons rien après "/square"
        switch ($response) {
            case 'carré':
                $response = 'square';
                $br = 0;
                break;
            case 'cercle':
                $response = 'circle';
                $br = 50;
                break;
        }

        $i = rand(1, 6);
        $i2 = rand(1, 6);
        $colors = [
            1 => str_replace("'", "", 'red'),
            2 => str_replace("'", "", 'blue'),
            3 => str_replace("'", "", 'green'),
            4 => str_replace("'", "", 'yellow'),
            5 => str_replace("'", "", 'violet'),
            6 => str_replace("'", "", 'purple'),
        ];
        // Nous retournons un carré coloré en réponse
        return new Response(
            "<div style='display: flex; justify-content: space-evenly; margin-top: 10%'>
            <div>
            <h1>This really is quite a <span style='color: {$colors[$i]}'>{$colors[$i]}</span> {$response}!</h1>
            <div style='width: 300px; height: 300px; border-radius: {$br}%; background-color: {$colors[$i]};'></div>
            </div>
            <div>
            <h1>This one is more <span style='color: {$colors[$i2]}'>{$colors[$i2]}</span>-ish...</h1>
            <div style='width: 300px; height: 300px; border-radius: {$br}%; background-color: {$colors[$i2]};'></div>
            </div>
            </div>"
        );
    }

    #[Route('/square-display/display', name: 'square_display')]
    public function squareDisplay(): Response
    {
        // Attention: il faut toujours prendre soin de nommer ses fonctions différemment des fonctions à segment dynamique d'URL. Nommer l'URL (adresse) de notre route ici "square/display" activera la fonction squareResponse à la place du fait du même début d'adresse et la partie différente "display" sera simplement enregistrée comme valeur de {response} au dessus. IL faut toujours faire attention à bien nommer ses URL, et choisir un début unique et caractéristique pour chaque route possédant un segment dynamique d'URL.
        return new Response("Display");
    }

    #[Route('/square-color/{color}', name: 'square_color')]
    public function squareColor(Request $request, $color = false): Response
    {
        switch ($color) {
            case 'rouge':
                $color = 'red';
                break;
            case 'vert':
                $color = 'green';
                break;
            case 'jaune':
                $color = 'yellow';
                break;
            case 'bleu':
                $color = 'blue';
                break;
            case false:
                $color = 'gray';
                break;
            default:
                $color = 'black';
                break;
        };
        return new Response(
            "<div style='width: 300px; height: 300px; background-color: {$color};'></div>"
        );
    }
}
