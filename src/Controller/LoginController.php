<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Users;
use App\Form\Type\LoginFormType;
use App\Repository\UsersRepository; 
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    private $entityManager;
    private $usersRepository;

    public function __construct(EntityManagerInterface $entityManager, UsersRepository $usersRepository)
    {
        $this->entityManager = $entityManager;
        $this->usersRepository = $usersRepository;
    }
    #[Route('/', name: 'app_login', methods: ['GET', 'POST'])]
    public function index(Request $request): Response
    {
        $user = new Users();
        $form = $this->createForm(LoginFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
       
            $email = $form->getData()->getEmail();
            $password = $form->getData()->getPassword();
       
            $userRepository = $this->entityManager->getRepository(Users::class);
            $user = $userRepository->findOneBy(['email' => $email]);
            $password = $userRepository->findOneBy(['password' => $password]);

            if ($user && $password) {
                
              // The email and password match
              if ($user->getUserType() === 'admin') {
                // Redirect to the admin dashboard
                return $this->redirectToRoute('admin_login');
              } elseif ($user->getUserType() === 'user') {
                // Redirect to the user dashboard
                return $this->redirectToRoute('user_home');
              } else {
                // Show an error message
                $this->addFlash('error', 'Invalid email or password!');
                return $this->redirectToRoute('app_login');
              }
            } else {
              // The email or password is incorrect
              $this->addFlash('error', 'Invalid email or password!');
              return $this->redirectToRoute('app_login');
            }
          }
        return $this->render('login/index.html.twig', [
            'controller_name' => 'LoginController',
            'form' => $form,

        ]);
    }
    #[Route('/admin_login', name: 'admin')]
    public function admin_login(): Response
    {
        return $this->render('home/user_dashboard.html.twig');
    }
    #[Route('/logout', name: 'app_logout')]
    public function logout(): Response
    {
      return $this->redirectToRoute('app_login');
    }
    
}
