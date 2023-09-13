<?php
/**
 * Registration controller.
 */

namespace App\Controller;

use App\Entity\User;
use App\Form\Type\RegistrationFormType;
use App\Service\RegistrationService;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class RegistrationController.
 */
class RegistrationController extends AbstractController
{
    private UserService $userService;
    private RegistrationService $registrationService;

    public function __construct(RegistrationService $registrationService, UserService $userService)
    {
        $this->registrationService = $registrationService;
        $this->userService = $userService;
    }

    #[Route('/register', name: 'app_register', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        // ...
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $user->setNickname($data->getNickname());

            if (null !== $this->userService->findOneByEmail($data->getEmail())) {
                $this->addFlash('danger', 'message_email_already_exists');

                return $this->redirectToRoute('app_register');
            }

            // Przekazujesz tablicę danych do metody register w RegistrationService
            $this->registrationService->register([
                'email' => $data->getEmail(),
                'nickname' => $data->getNickname(),
                'password' => $data->getPassword(), // Upewnij się, że ta metoda jest dostępna w Twoim obiekcie User
            ]);

            $this->addFlash('success', 'message_registered_successfully');

            return $this->redirectToRoute('question_index');
        }
        // ...

        return $this->render(
            'registration/index.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }
}
