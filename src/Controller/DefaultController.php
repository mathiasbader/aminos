<?php


namespace App\Controller;


use App\Entity\Aminoacid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /** @Route("")
     *  @Template */
    public function index()
    {
        $aminos = $this->getDoctrine()->getRepository(Aminoacid::class)->findAll();
        return [
            'pageTitle' => 'The ' . count($aminos) . ' proteinogenic amino acids',
            'aminos'    => $aminos,
        ];
    }
}
