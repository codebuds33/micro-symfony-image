<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class Probe
{
    #[Route(path: '/probe', name: 'probe')]
    public function probe() : Response
    {
        return new Response('Alive');
    }
}
