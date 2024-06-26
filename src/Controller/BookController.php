<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookType;
use App\Persister\BookPersister;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class BookController extends AbstractController
{
    public function __construct(
        private BookPersister $bookPersister
    )
    {
        
    }

    #[Route('/', name: 'app_book_index', methods: ['GET'])]
    public function index(BookRepository $bookRepository, Request $request): Response
    {
        $pagination = $bookRepository->findPaginated($request);

        return $this->render('book/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/book/new', name: 'app_book_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) 
        {
            if($this->bookPersister->persist($book, $form, true))
            {
                return $this->redirectToRoute('app_book_index', [], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->render('book/new.html.twig', [
            'book' => $book,
            'form' => $form,
        ]);
    }

    #[Route('/book/{slug}', name: 'app_book_show', methods: ['GET'])]
    public function show(Book $book): Response
    {
        return $this->render('book/show.html.twig', [
            'book' => $book,
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/book/{slug}/edit', name: 'app_book_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Book $book): Response
    {
        $form = $this->createForm(BookType::class, $book, [
            'isEditing' => true
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) 
        {
            if($this->bookPersister->persist($book, $form))
            {
                return $this->redirectToRoute('app_book_index', [], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->render('book/edit.html.twig', [
            'book' => $book,
            'form' => $form,
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/book/{id}', name: 'app_book_delete', methods: ['POST'])]
    public function delete(Request $request, Book $book, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$book->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($book);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_book_index', [], Response::HTTP_SEE_OTHER);
    }
}
