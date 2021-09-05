<?php


namespace App\Controller;


use App\Service\NameService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /** @Route("") */
    public function index(NameService $names): Response {
        return $this->render('lucky/number.html.twig', [
            'name' => $names->getName(),
        ]);    }
}
