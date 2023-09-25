<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Users;
use App\Form\SignUpType;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Repository\UsersRepository; // Import the UserRepository

class SignUpController extends AbstractController
{
    private $entityManager;
    private $usersRepository;

    public function __construct(EntityManagerInterface $entityManager, UsersRepository $usersRepository)
    {
        $this->entityManager = $entityManager;
        $this->usersRepository = $usersRepository;
    }

    #[Route('/register', name: 'register')]
    public function register(Request $request): Response
    {
        $user = new Users();         

        $form = $this->createForm(SignUpType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() ) {
       
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_login');
        }


        return $this->render('sign_up.html.twig', [
            'form' => $form->createView(),
        ]);
    }


}

        
      