<?php

namespace App\Controller;

use App\Entity\Movies;
use App\Form\MoviesType;
use App\Repository\MoviesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/movies')]
class MoviesController extends AbstractController
{
    private $entityManager;
    private $moviesRepository;

    public function __construct(EntityManagerInterface $entityManager, MoviesRepository $moviesRepository)
    {
        $this->entityManager = $entityManager;
        $this->moviesRepository = $moviesRepository;
    }
    #[Route('/', name: 'app_movies_index', methods: ['GET'])]
    public function index(MoviesRepository $moviesRepository): Response
    {
        return $this->render('movies/index.html.twig', [
            'movies' => $moviesRepository->findAll(),
        ]);
        
    }

    #[Route('/new', name: 'app_movies_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $movie = new Movies();
        $form = $this->createForm(MoviesType::class, $movie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $imageName = uniqid().'.'.$imageFile->guessExtension();
                $imageFile->move(
                    $this->getParameter('movie_images_directory'), // Use the parameter for the image directory
                    $imageName
                );
                $movie->setImage($imageName);
            }
            

        // Handle video upload
       /* $videoFile = $form->get('video')->getData();
        if ($videoFile) {
            $videoName = uniqid().'.'.$videoFile->guessExtension();
            $videoFile->move(
                $this->getParameter('public/assets/images/video'), // Specify the directory where videos should be stored
                $videoName
            );
            $movie->setVideo($videoName);
        }*/

            $entityManager->persist($movie);
            $entityManager->flush();

            return $this->redirectToRoute('app_movies_index');
        }

        return $this->render('movies/new.html.twig', [
            'movie' => $movie,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_movies_show', methods: ['GET'])]
    public function show(Movies $movie): Response
    {
        return $this->render('movies/show.html.twig', [
            'movie' => $movie,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_movies_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Movies $movie, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MoviesType::class, $movie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_movies_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('movies/edit.html.twig', [
            'movie' => $movie,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_movies_delete', methods: ['POST'])]
    public function delete(Request $request, Movies $movie, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$movie->getId(), $request->request->get('_token'))) {
            $entityManager->remove($movie);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_movies_index', [], Response::HTTP_SEE_OTHER);
    }


    #[Route('/userhome', name: 'user_home', methods: ['GET'])]
    public function userHome(MoviesRepository $moviesRepository): Response
    {
      
        return $this->render('userhome/index.html.twig', [
            'movies' => $moviesRepository->findAll(),
        ]);
        
    }
 
 
    #[Route('/userhome/{id}', name: 'app_movie_details', methods: ['GET'])]
    public function details(Movies $movie): Response
    {
        return $this->render('userhome/movie_details.html.twig', [
            'movie' => $movie,
        ]);
    }
    

}
