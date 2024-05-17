<?php
namespace App\Persister;

use App\Entity\Book;
use App\Repository\BookRepository;
use App\Services\BookApiService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormError;
use Symfony\Component\String\Slugger\SluggerInterface;

class BookPersister
{
    public function __construct(
        private SluggerInterface $slugger,
        private EntityManagerInterface $em,
        private BookRepository $bookRepository,
        private BookApiService $bookApiService,
        private Security $security
    )
    {
        
    }

    public function persist(Book $book, Form $form, bool $doPersist = false): ?Book
    {
        $user = $this->security->getUser();
        if(!$user)
        {
            throw new Exception('Vous devez être connecté');
        }

        //on crée le slug
        $this->configureSlug($book);
        //on vérifie si le slug existe déjà
        if(!$this->validateUniqueSlug($book))
        {
            $form->get('title')->addError((new FormError('Ce livre est déjà dans la bibliothèque')));
            return null;
        }

        if($doPersist)
        {
            $book->setUser($user);
            $this->bookApiService->completeBookInfos($book);
            $this->em->persist($book);
        }
        $this->em->flush();

        return $book;
    }

    private function configureSlug(Book $book)
    {
        $book->setSlug(
            $this->slugger->slug($book->getTitle())->lower()->toString()
        );
    }

    private function validateUniqueSlug(Book $book): bool
    {
        if($existingBook = $this->bookRepository->findOneBySlug($book->getSlug()))
        {
            if($existingBook->getId() !== $book->getId())
            {
                return false;
            }
        }
        return true;
    }

}