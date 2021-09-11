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
        return [
            'pageTitle' => 'The 20 proteinogenic amino acids',
        ];
    }

    /** @Route("/overview/{param}", name="overview") @Template */
    public function overviewAction(string $param = '')
    {
        $aminos = $this->getDoctrine()->getRepository(Aminoacid::class)->findAll();
        return [
            'pageTitle' => 'The 20 proteinogenic amino acids',
            'aminos'    => $aminos,
            'bigger'     => $param === 'b',
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
