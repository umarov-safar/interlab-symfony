<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

// Openapi Specification Controller(Swagger)
class SwaggerController extends AbstractController
{
    #[Route('/docs/oas', name: 'oas')]
    public function index(): Response
    {
        return $this->render('swagger/index.html.twig');
    }
}
