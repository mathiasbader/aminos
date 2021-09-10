<?php


namespace App\Controller;


use App\Entity\Aminoacid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /** @Route("", name="index")
     *  @Template */
    public function indexAction()
    {
        $aminos = $this->getDoctrine()->getRepository(Aminoacid::class)->findAll();
        return [
            'pageTitle' => 'The 20 proteinogenic amino acids',
            'aminos'    => $aminos,
        ];
    }

    /** @Route("/about", name="about") @Template */
    public function aboutAction()
    {
        return [
            'pageTitle' => 'The 20 proteinogenic amino acids',
        ];
    }
}
