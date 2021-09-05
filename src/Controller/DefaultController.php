<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /** @Route("")
     *  @Template */
    public function index()
    {
        return [
            'pageTitle' => 'Die 20 proteinogenen Aminosäuren',
        ];
    }
}
