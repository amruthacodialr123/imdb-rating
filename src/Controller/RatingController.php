<?php

namespace App\Controller;

use App\Entity\Rating;
use App\Form\RatingType;
use App\Repository\RatingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/rating')]
class RatingController extends AbstractController
{
    #[Route('/', name: 'app_rating_index', methods: ['GET'])]
    public function index(RatingRepository $ratingRepository): Response
    {
        return $this->render('rating/index.html.twig', [
            'ratings' => $ratingRepository->findAll(),
        ]);
    }
    #[Route('/rating/new', name: 'app_rating_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = // Get the authenticated user (you may need to implement this).
        $movie = // Fetch the movie based on the current movie details page.
    
        $rating = new Rating();
        $rating->setUser("Amrutha");
        $rating->setMovie("ABC");
        $rating->setComments($request->request->get('comments'));
        $rating->setRating($request->request->get('rating'));
    
        $entityManager->persist($rating);
        $entityManager->flush();
    
        return $this->redirectToRoute('user_home', [], Response::HTTP_SEE_OTHER);
    }
    

    #[Route('/{id}', name: 'app_rating_show', methods: ['GET'])]
    public function show(Rating $rating): Response
    {
        return $this->render('rating/show.html.twig', [
            'rating' => $rating,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_rating_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Rating $rating, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RatingType::class, $rating);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_rating_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('rating/edit.html.twig', [
            'rating' => $rating,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_rating_delete', methods: ['POST'])]
    public function delete(Request $request, Rating $rating, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$rating->getId(), $request->request->get('_token'))) {
            $entityManager->remove($rating);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_rating_index', [], Response::HTTP_SEE_OTHER);
    }
}
