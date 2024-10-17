<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HelloController 
{
    #[Route(
        path:'/test',
        methods:['GET'],
        format:'json'
    )]
    public function test (): Response
    {
        return new Response(
            'Hello, this is a test response!',
            Response::HTTP_OK,
            ['Content-Type' => 'text/plain'] // Ajoute un header Content-Type pour du texte
        );    }
}