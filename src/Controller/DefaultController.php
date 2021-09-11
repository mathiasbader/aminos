<?php


namespace App\Controller;


use App\Constant\Common;
use App\Entity\Aminoacid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /** @Route("", name="index")
     *  @Template */
    public function indexAction()
    {
        return [
            'pageTitle' => 'The ' . Common::AMINOS_COUNT . ' proteinogenic amino acids',
        ];
    }

    /** @Route("/overview/{param}", name="overview") @Template */
    public function overviewAction(string $param = '')
    {
        $aminos = $this->getDoctrine()->getRepository(Aminoacid::class)->findAll();
        return [
            'pageTitle' => 'The ' . Common::AMINOS_COUNT . ' proteinogenic amino acids',
            'aminos'    => $aminos,
            'bigger'     => $param === 'b',
        ];
    }

    /** @Route("/i2n", name="imgToName") @Template */
    public function imgToNameAction(Request $request)
    {
        $answerText    = $request->get('answer');
        $answerAminoId = $request->get('amino');

        $answerCorrect = null;
        $answerAmino   = null;
        if (!empty($answerText) && $answerAminoId !== null && is_numeric($answerAminoId)) {
            /* @var $answerAmino Aminoacid */
            $answerAmino = $this->getDoctrine()->getRepository(Aminoacid::class)->find($answerAminoId);

            $answerLower = mb_strtolower($answerText);
            $answerCorrect =
                $answerLower === mb_strtolower($answerAmino->getCode1 ()) ||
                $answerLower === mb_strtolower($answerAmino->getCode3 ()) ||
                $answerLower === mb_strtolower($answerAmino->getNameEn()) ||
                $answerLower === mb_strtolower($answerAmino->getNameDe());
        }

        $amino = $this->getDoctrine()->getRepository(Aminoacid::class)->find(rand(1, Common::AMINOS_COUNT));
        return [
            'pageTitle'     => 'Image to name - The ' . Common::AMINOS_COUNT . ' proteinogenic amino acids',
            'amino'         => $amino,
            'answerText'    => $answerText,
            'answerAmino'   => $answerAmino,
            'answerCorrect' => $answerCorrect,
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
