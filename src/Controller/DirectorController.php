<?php

namespace App\Controller;

use App\Entity\Director;
use App\Form\DirectorType;
use App\Repository\DirectorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/director')]
class DirectorController extends AbstractController
{
    #[Route('/', name: 'app_director_index', methods: ['GET'])]
    public function index(DirectorRepository $directorRepository): Response
    {
        return $this->render('director/index.html.twig', [
            'directors' => $directorRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_director_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $director = new Director();
        $form = $this->createForm(DirectorType::class, $director);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $imageName = time() . '_' . uniqid() . '.' . $imageFile->guessExtension();
                if (file_exists($this->getParameter('movie_images_directory') . '/' . $imageName)) {
                    // Handle the case where a file with the same name exists
                    // Example: $imageName = time() . '_' . uniqid() . '.' . $imageFile->guessExtension();
                }
                $imageFile->move(
                    $this->getParameter('movie_images_directory'),
                    $imageName
                );
    
                // Set the image for the director entity
                $director->setImage($imageName);
            }
    
            $entityManager->persist($director);
            $entityManager->flush();

            return $this->redirectToRoute('app_director_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('director/new.html.twig', [
            'director' => $director,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_director_show', methods: ['GET'])]
    public function show(Director $director): Response
    {
        return $this->render('director/show.html.twig', [
            'director' => $director,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_director_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Director $director, EntityManagerInterface $entityManager): Response
    {
        // Store the existing image name
        $existingImage = $director->getImage();
    
        $form = $this->createForm(DirectorType::class, $director);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Check if a new image is uploaded
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                // Generate a unique image name
                $imageName = time() . '_' . uniqid() . '.' . $imageFile->guessExtension();
    
                // Move the uploaded image to the destination directory
                $imageFile->move(
                    $this->getParameter('movie_images_directory/Director'),
                    $imageName
                );
    
                // Set the new image name
                $director->setImage($imageName);
            } else {
                // If no new image is uploaded, keep the existing image name
                $director->setImage($existingImage);
            }
    
            $entityManager->flush();
    
            return $this->redirectToRoute('app_director_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->render('director/edit.html.twig', [
            'director' => $director,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_director_delete', methods: ['POST'])]
    public function delete(Request $request, Director $director, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$director->getId(), $request->request->get('_token'))) {
            $entityManager->remove($director);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_director_index', [], Response::HTTP_SEE_OTHER);
    }
}
