<?php
/**
 * Answer controller.
 */

namespace App\Controller;

use App\Entity\Answer;
use App\Form\Type\AnswerType;
use App\Service\AnswerService;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class AnswerController.
 */
#[Route('/answer')]
class AnswerController extends AbstractController
{
    private AnswerService $answerService;

    public function __construct(AnswerService $answerService, TranslatorInterface $translator)
    {
        $this->answerService = $answerService;
        $this->translator = $translator;
    }

    private TranslatorInterface $translator;

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/{id}/edit', name: 'answer_edit', requirements: ['id' => '[1-9]\d*'], methods: 'GET|PUT')]
    public function edit(Request $request, Answer $answer): Response
    {
        $form = $this->createForm(AnswerType::class, $answer, [
            'method' => 'PUT',
            'action' => $this->generateUrl('answer_edit', ['id' => $answer->getId()]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->answerService->save($answer);

            $this->addFlash(
                'success',
                $this->translator->trans('message.edited_successfully')
            );

            return $this->redirectToRoute('question_show', ['id' => $answer->getQuestion()->getId()]);
        }

        return $this->render('answer/edit.html.twig', [
            'form' => $form->createView(),
            'answer' => $answer,
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/{id}/delete', name: 'answer_delete', requirements: ['id' => '[1-9]\d*'], methods: 'GET|DELETE')]
    public function delete(Request $request, Answer $answer): Response
    {
        $id = $answer->getQuestion()->getId();
        $form = $this->createForm(FormType::class, $answer, [
            'method' => 'DELETE',
            'action' => $this->generateUrl('answer_delete', ['id' => $answer->getId()]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->answerService->delete($answer);
            } catch (OptimisticLockException|ORMException) {
            }

            $this->addFlash(
                'success',
                $this->translator->trans('message.deleted_successfully')
            );

            return $this->redirectToRoute('question_show', ['id' => $id]);
        }

        return $this->render('answer/delete.html.twig', [
            'form' => $form->createView(),
            'answer' => $answer,
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/mark-best-answer/{id}', name: 'mark_best_answer')]
    public function markBestAnswer(Answer $answer): RedirectResponse
    {
        $entityManager = $this->getDoctrine()->getManager();

        if ($answer->getIsBest()) {
            // Oznaczenie jako najlepsza odpowiedź jest już ustawione, więc odznaczamy
            $answer->setIsBest(false);
        } else {
            // Oznaczamy odpowiedź jako najlepsza
            $answer->setIsBest(true);

            // Odznaczamy inne odpowiedzi jako najlepsze, jeśli takie istnieją
            $this->clearOtherBestAnswers($answer);
        }

        $entityManager->persist($answer);
        $entityManager->flush();

        return $this->redirectToRoute('question_show', ['id' => $answer->getQuestion()->getId()]);
    }

    private function clearOtherBestAnswers(Answer $selectedAnswer): void
    {
        $question = $selectedAnswer->getQuestion();
        foreach ($question->getAnswer() as $answer) {
            if ($answer !== $selectedAnswer && $answer->getIsBest()) {
                $answer->setIsBest(false);
            }
        }
    }
}
