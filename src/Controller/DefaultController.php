<?php


namespace App\Controller;


use App\Constant\Common;
use App\Entity\Aminoacid;
use App\Service\AminoService;
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
            'pageTitle' => Common::PAGE_TITLE,
        ];
    }

    /** @Route("/overview/{param}", name="overview") @Template */
    public function overviewAction(string $param = '')
    {
        $aminos = $this->getDoctrine()->getRepository(Aminoacid::class)->findAll();
        $aminoMap = [];
        foreach($aminos as $amino) {
            /* @var $amino Aminoacid  */
            $aminoMap[$amino->getId()] = $amino;
        }
        return [
            'pageTitle' => Common::PAGE_TITLE,
            'aminos'    => $aminoMap,
            'bigger'    => $param === 'b',
        ];
    }

    /** @Route("/i2n", name="testImgToName") @Template */
    public function testImgToNameAction(Request $request, AminoService $aminoService)
    {
        $answerText    = $request->get('answer');
        $answerAminoId = $request->get('amino');

        $answerCorrect = null;
        $answerAmino   = null;
        if (!empty($answerText) && $answerAminoId !== null && is_numeric($answerAminoId)) {
            /* @var $answerAmino Aminoacid */
            $answerAmino = $this->getDoctrine()->getRepository(Aminoacid::class)->find($answerAminoId);
            $answerCorrect = $aminoService->isCorrectAnswer($answerText, $answerAmino);
        }

        $amino = $this->getDoctrine()->getRepository(Aminoacid::class)->find(rand(1, Common::AMINOS_COUNT));
        return [
            'pageTitle'     => Common::PAGE_TITLE,
            'amino'         => $amino,
            'answerText'    => $answerText,
            'answerAmino'   => $answerAmino,
            'answerCorrect' => $answerCorrect,
        ];
    }

    /** @Route("/n2i", name="testNameToImg") @Template */
    public function testNameToImgAction(Request $request, AminoService $aminoService)
    {
        $selectedAminoId = $request->get('answer');
        $answerAminoId   = $request->get('amino');

        $selectedAmino = null;
        $answerAmino   = null;
        $answerCorrect = null;
        if ($answerAminoId !== null && is_numeric($answerAminoId)) {
            /* @var $answerAmino Aminoacid */
            $selectedAmino = $this->getDoctrine()->getRepository(Aminoacid::class)->find($selectedAminoId);
            $answerAmino   = $this->getDoctrine()->getRepository(Aminoacid::class)->find($answerAminoId);
            $answerCorrect = $selectedAmino->getId() === $answerAmino->getId();
        }

        $amino = $this->getDoctrine()->getRepository(Aminoacid::class)->find(rand(1, Common::AMINOS_COUNT));
        $otherAminoIds = $aminoService->getOtherAminoIds($amino->getId());
        $answerAminos = $this->getDoctrine()->getRepository(Aminoacid::class)->findBy(['id' => $otherAminoIds]);
        array_splice($answerAminos, mt_rand(0, 4), 0, [$amino]);

        return [
            'pageTitle'     => Common::PAGE_TITLE,
            'amino'         => $amino,
            'answerAminos'  => $answerAminos,
            'selectedAmino' => $selectedAmino,
            'answerAmino'   => $answerAmino,
            'answerCorrect' => $answerCorrect,
        ];
    }

    /** @Route("/c2n", name="testCodeToName") @Template */
    public function testCodeToNameAction(Request $request, AminoService $aminoService)
    {
        $answerText    = $request->get('answer');
        $answerAminoId = $request->get('amino');

        $answerAmino   = null;
        $answerCorrect = null;
        if (!empty($answerText) && $answerAminoId !== null && is_numeric($answerAminoId)) {
            /* @var $answerAmino Aminoacid */
            $answerAmino   = $this->getDoctrine()->getRepository(Aminoacid::class)->find($answerAminoId);
            $answerCorrect = $aminoService->isCorrectAnswer($answerText, $answerAmino, false);
        }

        $amino = $this->getDoctrine()->getRepository(Aminoacid::class)->find(rand(1, Common::AMINOS_COUNT));

        return [
            'pageTitle'     => Common::PAGE_TITLE,
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
            'pageTitle' => 'About',
        ];
    }
}
