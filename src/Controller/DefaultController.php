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
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Contracts\Translation\TranslatorInterface;

class DefaultController extends AbstractController
{
    /** @Route("", name="index")
     *  @Template */
    public function indexAction(TranslatorInterface $translator, UserService $userService)
    {
        $this->initUser($userService);

        return [
            'pageTitle' => $translator->trans('studyThe20ProteinogenicAminoAcids'),
        ];
    }

    /** @Route("profile", name="profile") @Template */
    public function profileAction(TranslatorInterface $translator, Request $request, UserPasswordHasherInterface $passwordHasher, UserService $userService)
    {
        $this->initUser($userService);

        $error = '';
        $email = '';
        $name = '';
        if ($request->get('action') === 'registration') {
            $email     = $request->get('email');
            $name      = $request->get('name');
            $password  = $request->get('password');
            $password2 = $request->get('password2');
            if (mb_strlen($password) < Common::MIN_PASSWORD_LENGTH) $error = 'passwordTooShort';
            elseif ($password !== $password2) $error = 'passwordsDoNotMatch';
            elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $error = 'invalidEmail';
            else {

                /* @var $userDb User */
                $userDb = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $email]);
                if ($userDb instanceof User) $error = 'emailIsAlreadyInDb';
                else {
                    /* @var $user User */
                    $user = $this->get('security.token_storage')->getToken()->getUser();
                    $user->setEmail($email);
                    $name = trim(preg_replace("/[^a-zA-Z]/", "", $name));
                    $user->setName($name);
                    $user->setPassword($passwordHasher->hashPassword($user, $password));
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($user);
                    $em->flush();
                }
            }
        }
        elseif ($request->get('action') === 'edit') {
            $email = trim($request->get('email'));
            $name = $request->get('name');
            $name = trim(preg_replace("/[^a-zA-Z]/", "", $name));
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $error = 'invalidEmail';
            if (strlen($name) === 0)                             $error = 'noNameProvided';
            else {
                $user = $this->get('security.token_storage')->getToken()->getUser();
                $user->setEmail($email);
                $user->setName($name);
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();
            }
        }

        return [
            'pageTitle'  => $translator->trans('profile'),
            'error'      => $error,
            'email'      => $email,
            'name'       => $name,
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

    /** @Route("/overview", name="overview") @Template */
    public function overviewAction(Request $request, TranslatorInterface $translator)
    {
        $aminos = $this->getDoctrine()->getRepository(Aminoacid::class)->findAll();
        $aminoMap = [];
        foreach($aminos as $amino) {
            /* @var $amino Aminoacid  */
            $aminoMap[$amino->getCode1()] = $amino;
        }

        $matrix = [
            'nonPolar1' => [
                'height' => 200,
                'aminos' => ['G', 'A', 'V', 'L', 'I'],
            ],
            'nonPolar2' => [
                'height' => 235,
                'aminos' => ['M', 'F', 'W', 'P'],
            ],
            'polar' => [
                'height' => 230,
                'aminos' => ['S', 'T', 'C', 'Y', 'N', 'Q'],
            ],
            'electrically' => [
                'height' => 285,
                'aminos' => ['D', 'E', 'K', 'R', 'H'],
            ]
        ];

        return [
            'pageTitle' => $translator->trans('studyThe20ProteinogenicAminoAcids'),
            'aminoMap'  => $aminoMap,
            'matrix'    => $matrix,
            'action'    => $request->get('a'),
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
            $answerCorrect = $aminoService->isCorrectAnswer($translator, $answerText, $answerAmino);
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
        $answerText = htmlentities($answerText);
        $answerAminoId = $request->get('amino');

        $answerAmino   = null;
        $answerCorrect = null;
        if (!empty($answerText) && $answerAminoId !== null && is_numeric($answerAminoId)) {
            /* @var $answerAmino Aminoacid */
            $answerAmino   = $this->getDoctrine()->getRepository(Aminoacid::class)->find($answerAminoId);
            $answerCorrect = $aminoService->isCorrectAnswer($translator, $answerText, $answerAmino, false);
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

    public function initUser(UserService $userService): User {
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

        // Todo: Remove user name generation
        $user->setName($userService->generateName());

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
