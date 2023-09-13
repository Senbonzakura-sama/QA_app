<?php
/**
 * Question controller.
 */

namespace App\Controller;

use App\Entity\Answer;
use App\Service\AnswerService;
use App\Entity\Question;
use App\Form\Type\AnswerType;
use App\Form\Type\QuestionType;
use App\Service\QuestionServiceInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[\AllowDynamicProperties] #[Route('/question')]
class QuestionController extends AbstractController
{
    /**
     * Question serivce.
     */
    private QuestionServiceInterface $questionService;

    /**
     * Translator.
     */
    private TranslatorInterface $translator;

    /**
     * Constructor.
     */
    public function __construct(QuestionServiceInterface $questionService, AnswerService $answerService, TranslatorInterface $translator)
    {
        $this->questionService = $questionService;
        $this->answerService = $answerService;
        $this->translator = $translator;
    }

    /**
     * Index action.
     */
    #[Route(name: 'question_index', methods: 'GET')]
    public function index(Request $request): Response
    {
        $pagination = $this->questionService->getPaginatedList(
            $request->query->getInt('page', 1)
        );

        return $this->render('question/index.html.twig', ['pagination' => $pagination]);
    }

    /**
     * Show action.
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    #[Route('/{id}', name: 'question_show', requirements: ['id' => '[1-9]\d*'], methods: 'GET')]
    #[IsGranted('ROLE_USER', subject: 'question')]
    public function show(Request $request, Question $question, AnswerService $answerService): Response
    {
        $answer = new Answer();
        // $answer->setIsBest(false);

        $form = $this->createForm(AnswerType::class, $answer);
        $form->handleRequest($request);
        $id = $question->getId();

        if ($form->isSubmitted() && $form->isValid()) {
            $answer->setQuestion($question);
            $this->answerService->save($answer);
            $this->addFlash(
                'success',
                $this->translator->trans('message.added_answer')
            );

            return $this->redirectToRoute('question_show', ['id' => $id]);
        }

        return $this->render(
            'question/show.html.twig',
            ['question' => $question,
                'form' => $form->createView(),
                    'answers' => $answerService->findBy(['question' => $question])]
        );
    }

    /**
     * Create action.
     */
    #[Route('/create', name: 'question_create', methods: 'GET|POST', )]
    #[IsGranted('ROLE_USER')] // Dodaj tę adnotację
    public function create(Request $request): Response
    {
        $user = $this->getUser();
        $question = new Question();

        $question->setAuthor($user);
        $question->setEmail($user);
        $question->setNickname($user);
        $form = $this->createForm(
            QuestionType::class,
            $question,
            ['action' => $this->generateUrl('question_create')]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->questionService->save($question);

            $this->addFlash(
                'success',
                $this->translator->trans('message.created_successfully')
            );

            return $this->redirectToRoute('question_index');
        }

        return $this->render('question/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * Edit action.
     */
    #[Route('/{id}/edit', name: 'question_edit', requirements: ['id' => '[1-9]\d*'], methods: 'GET|PUT')]
    // #[IsGranted('ROLE_ADMIN', subject: 'question')]
    public function edit(Request $request, Question $question): Response
    {
        if (!$this->isGranted('ROLE_USER', $question) && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Nie masz dostępu do tej akcji.');
        }
        $form = $this->createForm(
            QuestionType::class,
            $question,
            [
                'method' => 'PUT',
                'action' => $this->generateUrl('question_edit', ['id' => $question->getId()]),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->questionService->save($question);

            $this->addFlash(
                'success',
                $this->translator->trans('message.edited_successfully')
            );

            return $this->redirectToRoute('question_index');
        }

        return $this->render(
            'question/edit.html.twig',
            [
                'form' => $form->createView(),
                'question' => $question,
            ]
        );
    }

    /**
     * Delete action.
     */
    #[Route('/{id}/delete', name: 'question_delete', requirements: ['id' => '[1-9]\d*'], methods: 'GET|DELETE')]
    // #[IsGranted('ROLE_ADMIN', subject: 'question')]
    public function delete(Request $request, Question $question): Response
    {
        if (!$this->isGranted('ROLE_USER', $question) && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Nie masz dostępu do tej akcji.');
        }
        $form = $this->createForm(
            FormType::class,
            $question,
            [
                'method' => 'DELETE',
                'action' => $this->generateUrl('question_delete', ['id' => $question->getId()]),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->questionService->delete($question);

            $this->addFlash(
                'success',
                $this->translator->trans('message.deleted_successfully')
            );

            return $this->redirectToRoute('question_index');
        }

        return $this->render(
            'question/delete.html.twig',
            [
                'form' => $form->createView(),
                'question' => $question,
            ]
        );
    }

    /**
     * Answer action.
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    #[Route('/{id}/answer', name: 'question_answer', requirements: ['id' => '[1-9]\d*'], methods: 'GET|POST')]
    #[IsGranted('ROLE_USER')]
    public function answer(Request $request, Question $question, AnswerService $answerService): Response
    {
        $answer = new Answer();
        $form = $this->createForm(AnswerType::class, $answer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $answer->setQuestion($question);
            $answer->setAuthor($this->getUser());
            $answer->setCreatedAt(new \DateTime());
            $answer->setUpdatedAt(new \DateTime());
            $answerService->save($answer);

            $this->addFlash(
                'success',
                $this->translator->trans('message_added_successfully')
            );

            return $this->redirectToRoute('question_show', ['id' => $question->getId()]);
        }

        return $this->render(
            'question/answer.html.twig',
            [
                'form' => $form->createView(),
                'question' => $question,
                'answer' => $answer,
            ]
        );
    }
}
