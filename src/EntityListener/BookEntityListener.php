<?php
namespace App\EntityListener;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\String\Slugger\SluggerInterface;

#[AsEntityListener(event: Events::prePersist, entity: Book::class, method: 'configureSlug')]
class BookEntityListener
{
    public function __construct(
        private SluggerInterface $slugger
    )
    {
        
    }

    public function configureSlug(Book $book, PrePersistEventArgs $args)
    {
        $book->setSlug(
            $this->slugger->slug($book->getTitle())->lower()->toString()
        );
    }
}