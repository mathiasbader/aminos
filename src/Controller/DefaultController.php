<?php


namespace App\Controller;


use App\Constant\Common;
use App\Entity\Aminoacid;
use App\Entity\User;
use App\Service\AminoService;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Contracts\Translation\TranslatorInterface;

class DefaultController extends AbstractController
{
    /** @Route("", name="index")
     *  @Template */
    public function indexAction(TranslatorInterface $translator, UserService $userService)
    {
        $user = $this->getTheUser($userService);

        return [
            'pageTitle' => $translator->trans('studyThe20ProteinogenicAminoAcids'),
            'user'      => $user,
        ];
    }

    /** @Route("profile", name="profile") @Template */
    public function profileAction(TranslatorInterface $translator, UserService $userService)
    {
        $user = $this->getTheUser($userService);

        return [
            'pageTitle' => $translator->trans('profile'),
            'user'      => $user,
        ];
    }

    /** @Route("/lang/{lang}", name="lang") */
    public function langAction(Request $request, AminoService $aminoService, string $lang)
    {
        if ($aminoService->isValidLanguage($lang)) {
            /* @var $user User */
            $user = $this->get('security.token_storage')->getToken()->getUser();
            $user->setLang($lang);
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            $request->getSession()->set('_lang', $user->getLang());
        }
        return $this->redirectToRoute('profile');
    }

    /** @Route("/overview/{param}", name="overview") @Template */
    public function overviewAction(TranslatorInterface $translator, string $param = '')
    {
        $aminos = $this->getDoctrine()->getRepository(Aminoacid::class)->findAll();
        return [
            'pageTitle' => $translator->trans('studyThe20ProteinogenicAminoAcids'),
            'aminos'    => $aminos,
            'bigger'    => $param === 'b',
        ];
    }

    /** @Route("/i2n", name="testImgToName") @Template */
    public function testImgToNameAction(Request $request, TranslatorInterface $translator, AminoService $aminoService)
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
            'pageTitle'     => $translator->trans('studyThe20ProteinogenicAminoAcids'),
            'amino'         => $amino,
            'answerText'    => $answerText,
            'answerAmino'   => $answerAmino,
            'answerCorrect' => $answerCorrect,
        ];
    }

    /** @Route("/n2i", name="testNameToImg") @Template */
    public function testNameToImgAction(Request $request, TranslatorInterface $translator, AminoService $aminoService)
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
            'pageTitle'     => $translator->trans('studyThe20ProteinogenicAminoAcids'),
            'amino'         => $amino,
            'answerAminos'  => $answerAminos,
            'selectedAmino' => $selectedAmino,
            'answerAmino'   => $answerAmino,
            'answerCorrect' => $answerCorrect,
        ];
    }

    /** @Route("/c2n", name="testCodeToName") @Template */
    public function testCodeToNameAction(Request $request, TranslatorInterface $translator, AminoService $aminoService)
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
            'pageTitle'     => $translator->trans('studyThe20ProteinogenicAminoAcids'),
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

    public function getTheUser(UserService $userService): User {
        $user = $this->getLoggedInUser();
        if ($user instanceof User) return $user;

        return $this->generateNewUser($userService);
    }

    private function getLoggedInUser(): ?User {
        $token = $this->get('security.token_storage')->getToken();
        return $token === null ? null : $token->getUser();
    }

    private function generateNewUser(UserService $userService): User {
        // create new user
        $user = new User();
        $user->setName($userService->generateName());
        $user->setCode($userService->generateCode(22));
        $user->setRoles(['ROLE_USER']);
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        // log in new user
        $firewallName = 'main';
        $token = new UsernamePasswordToken($user, null, $firewallName, $user->getRoles());
        $this->get('security.token_storage')->setToken($token);
        $session = $this->get('session');
        $session->set('_security_' . $firewallName, serialize($token));

        return $user;
    }
}
