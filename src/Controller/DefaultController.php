<?php


namespace App\Controller;


use App\Constant\Common;
use App\Constant\GroupType;
use App\Constant\Representation;
use App\Constant\TestType;
use App\Entity\Aminoacid;
use App\Entity\Test;
use App\Entity\TestRun;
use App\Entity\User;
use App\Service\AminoService;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Contracts\Translation\TranslatorInterface;

class DefaultController extends AbstractController
{
    /** @Route("", name="index")
     *  @Template */
    function indexAction(TranslatorInterface $translator, Request $request)
    {
        $user = $this->initUser();

        $redirect = $this->updatePresentation($request, $user);
        if ($redirect !== null) return $redirect;

        list($aminoMap, $matrix) = $this->loadAminosForOverview();

        return [
            'pageTitle' => $translator->trans('the20ProteinogenicAminoAcids'),
            'aminoMap'  => $aminoMap,
            'matrix'    => $matrix,
        ];
    }

    /** @Route("profile", name="profile") @Template */
    function profileAction(TranslatorInterface $translator, Request $request, UserPasswordHasherInterface $passwordHasher)
    {
        $user = $this->initUser();

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
                    $user->setEmail($email);
                    $name = trim(preg_replace("/[^a-zA-Z]/", "", $name));
                    $user->setName($name);
                    $user->setPassword($passwordHasher->hashPassword($user, $password));
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($user);
                    $em->flush();
                }
            }
        } elseif ($request->get('action') === 'edit') {
            $email = trim($request->get('email'));
            $name = $request->get('name');
            $name = trim(preg_replace("/[^a-zA-Z]/", "", $name));
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $error = 'invalidEmail';
            if (strlen($name) === 0)                             $error = 'noNameProvided';
            else {
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
    function langAction(Request $request, AminoService $aminoService, string $lang): RedirectResponse
    {
        if ($aminoService->isValidLanguage($lang)) {
            $user = $this->initUser();
            $user->setLang($lang);
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            $request->getSession()->set('_lang', $user->getLang());
        }
        return $this->redirectToRoute('profile');
    }


    /** @Route("/test/overview", name="testOverview") @Template */
    function testOverviewAction(TranslatorInterface $translator): array {
        $user = $this->initUser();
        $activeRuns = $this->getDoctrine()->getRepository(TestRun::class)->findBy(['user' => $user, 'completed' => null]);
        $activeRun = !empty($activeRuns) ? $activeRuns[0] : null;

        return [
            'pageTitle' => $translator->trans('studyThe20ProteinogenicAminoAcids'),
            'activeRun' => $activeRun,
        ];
    }

    /** @Route("/test/start/{group}", name="testStart") */
    function testStartAction(string $group): RedirectResponse {
        $user = $this->initUser();

        $this->stopAllRuns($user);
        $run = $this->initNewTestRun($user, $group);
        if ($run === null) return $this->redirectToRoute('testOverview');

        return $this->redirectToRoute('test', [ 'runId' => $run->getId()]);
    }

    /** @Route("/test/{runId}", name="test") @Template */
    function testAction(int $runId, Request $request, TranslatorInterface $translator): array | RedirectResponse {
        $user = $this->initUser();

        $run = $this->getDoctrine()->getRepository(TestRun::class)->find($runId);
        if ($run === null ||
            $user->getId() !== $run->getUser()->getId()) return $this->redirectToRoute('testOverview');

        if ($request->get('action') === 'stop') {
            $this->stopAllRuns($user);
            return $this->redirectToRoute('testOverview');
        }

        if ($request->get('answer') !== null) {
            $answer       =      $request->get('answer');
            $answerTestId = (int)$request->get('test'  );
            $test = $run->getFirstUncompletedTest();
            if ($test !== null && $test->getId() === $answerTestId) {
                $em = $this->getDoctrine()->getManager();
                $correct = false;
                if ($test->getType() === TestType::TEST_1_NAME_TO_IMAGE) {
                    $answerId = (int)$answer;
                    $test->setAnswerAmino($this->getDoctrine()->getRepository(Aminoacid::class)->find($answerId));
                    $test->setAnswered(new DateTime());
                    $correct = $test->getAmino()->getId() === $answerId;
                    $test->setCorrect($correct);
                } elseif ($test->getType() === TestType::TEST_2_IMAGE_TO_NAME) {
                    $test->setAnswer(htmlentities($answer));
                    $test->setAnswered(new DateTime());
                    $correct = $test->getAmino()->isCorrectAnswer($answer);
                    $test->setCorrect($correct);
                } elseif ($test->getType() === TestType::TEST_3_CODE_TO_NAME) {
                    $test->setAnswer(htmlentities($answer));
                    $test->setAnswered(new DateTime());
                    $correct = $test->getAmino()->isCorrectAnswer($answer);
                    $test->setCorrect($correct);
                }

                // update next test type for this amino acid in current run
                $otherTests = $this->getDoctrine()->getRepository(Test::class)->findBy(['run' => $run->getId(), 'amino' => $test->getAmino(), 'answered' => null]);
                $nextTestType = $test->getType() + (($correct && $test->getType() < 3) ? 1 : 0);
                foreach ($otherTests as $otherTest) {
                    if ($otherTest->getId() === $test->getId()) continue;
                    $otherTest->setType($nextTestType);
                    if ($nextTestType === TestType::TEST_1_NAME_TO_IMAGE) $otherTest->defineChoices($run->getAminos());
                    else                                                  $otherTest->defineChoices(new ArrayCollection());
                    $em->persist($otherTest);
                }

                $em->persist($test);
                $em->flush();

                $run->recalculateCorrectCount();
                if ($run->isFinished()) {
                    $run->setCompleted(new DateTime());
                    $em->persist($run);
                    $em->flush();
                }
            }
        }
        return [
            'pageTitle' => $translator->trans('studyThe20ProteinogenicAminoAcids'),
            'run' => $run
        ];
    }

    /** @Route("/about", name="about") @Template */
    function aboutAction()
    {
        return [
            'pageTitle' => 'About',
        ];
    }

    function initUser(): User {
        $user = $this->getLoggedInUser();
        if ($user instanceof User) return $user;

        return $this->generateNewUser();
    }

    private function getLoggedInUser(): ?User {
        $token = $this->get('security.token_storage')->getToken();
        return $token === null ? null : $token->getUser();
    }

    private function generateNewUser(): User {
        // create new user
        $user = new User();
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

    private function updatePresentation(Request $request, User $user): ?RedirectResponse {
        if (!empty($request->get('representation'))) {
            $representation = $request->get('representation');
            if (in_array($representation, Representation::$all)) {
                $user->setRepresentation($representation);
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();
                return $this->redirectToRoute('index');
            }
        }
        return null;
    }

    private function loadAminosForOverview(): array {
        $aminos = $this->getDoctrine()->getRepository(Aminoacid::class)->findAll();
        $aminoMap = [];
        foreach($aminos as $amino) {
            /* @var $amino Aminoacid  */
            $aminoMap[$amino->getCode1()] = $amino;
        }

        $matrix = [
            'nonPolar1'    => ['G', 'A', 'V', 'L', 'I'],
            'nonPolar2'    => ['M', 'F', 'W', 'P'],
            'polar'        => ['N', 'Q', 'S', 'T', 'C', 'Y'],
            'electrically' => ['D', 'E', 'K', 'R', 'H'],
        ];
        return [$aminoMap, $matrix];
    }

    private function stopAllRuns(User $user): void {
        $em = $this->getDoctrine()->getManager();
        $runs = $this->getDoctrine()->getRepository(TestRun::class)->findBy(['user' => $user]);
        foreach ($runs as $run) {
            $run->setCompleted(new DateTime());
            $em->persist($run);
        }
        $em->flush();
    }

    private function initNewTestRun(User $user, string $group): ?TestRun {
        $activeTests = $this->getDoctrine()->getRepository(TestRun::class)->findBy(['user' => $user, 'completed' => null]);
        if (count($activeTests) > 0) return $activeTests[0];


        $run = new TestRun();
        $run->setUser($user);

        $aminos = [];
        if ($group == GroupType::GROUP_NOT_POLAR_1  ) $aminos = ['g', 'a', 'v', 'l', 'i'];
        if ($group == GroupType::GROUP_NOT_POLAR_2  ) $aminos = ['m', 'f', 'w', 'l', 'p'];
        if ($group == GroupType::GROUP_NOT_POLAR    ) $aminos = ['g', 'a', 'v', 'l', 'i', 'm', 'f', 'w', 'l', 'p'];
        if ($group == GroupType::GROUP_POLAR        ) $aminos = ['n', 'q', 's', 't', 'c', 'y'];
        if ($group == GroupType::GROUP_CHARGED      ) $aminos = ['d', 'e', 'k', 'r', 'h'];
        if ($group == GroupType::GROUP_POLAR_CHARGED) $aminos = ['n', 'q', 's', 't', 'c', 'y', 'd', 'e', 'k', 'r', 'h'];
        if ($group == GroupType::GROUP_ALL          ) $aminos = ['g', 'a', 'v', 'l', 'i', 'm', 'f', 'w', 'l', 'p', 'n', 'q', 's', 't', 'c', 'y', 'd', 'e', 'k', 'r', 'h'];
        if ($aminos === null) return null;

        $aminos1 = $aminos;
        $aminos2 = $aminos;
        $aminos3 = $aminos;
        shuffle($aminos1);
        shuffle($aminos2);
        shuffle($aminos3);
        $this->ensureFirstDoesNotEndEquallyAsSecondStarts($aminos1, $aminos2);
        $this->ensureFirstDoesNotEndEquallyAsSecondStarts($aminos2, $aminos3);

        $run->setAminos($this->resolveAminos($aminos));

        foreach ($this->resolveAminos($aminos1) as $amino) {
            $run->addTest($this->generateTest($run, $amino, TestType::TEST_1_NAME_TO_IMAGE));
        }
        foreach ($this->resolveAminos($aminos2) as $amino) {
            $run->addTest($this->generateTest($run, $amino, TestType::TEST_2_IMAGE_TO_NAME));
        }
        foreach ($this->resolveAminos($aminos3) as $amino) {
            $run->addTest($this->generateTest($run, $amino, TestType::TEST_3_CODE_TO_NAME));
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($run);
        $em->flush();

        return $run;
    }

    private function generateTest(TestRun $run, Aminoacid $amino, int $type): Test {
        $test = new Test();
        $test->setAmino($amino);
        $test->setType($type);
        if ($type === TestType::TEST_1_NAME_TO_IMAGE) $test->defineChoices($run->getAminos());
        return $test;
    }

    private function resolveAminos(array $strings): Collection {
        $aminos = new ArrayCollection();
        foreach ($strings as $string) {
            $amino = $this->getDoctrine()->getRepository(Aminoacid::class)->findOneBy(['code1' => $string]);
            if ($amino instanceof Aminoacid) $aminos[] = $amino;
        }
        return $aminos;
    }

    private function ensureFirstDoesNotEndEquallyAsSecondStarts(array &$a1, array &$a2): void {
        /* @var $a1 string[] */
        /* @var $a2 string[] */

        $end = end($a1);
        $front = $a2[0];
        if ($end === $front) {
            $tmp = $a2[0];
            $a2[0] = $a2[1];
            $a2[1] = $tmp;
        }
    }
}
