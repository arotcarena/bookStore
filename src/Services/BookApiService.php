<?php
namespace App\Services;

use App\Entity\Book;
use DateTimeImmutable;
use Exception;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class BookApiService
{
    public function __construct(
        private HttpClientInterface $client
    )
    {
        
    }

    public function completeBookInfos(Book $book): void
    {
        try 
        {
            $data = $this->client->request('GET', 'https://www.googleapis.com/books/v1/volumes?q=intitle:' . $book->getTitle() . 'inauthor:' . $book->getAuthor());
            $content = json_decode($data->getContent());
            $items = $content->items;

            //si possible on prend un item avec une image
            $correctItem = null;
            foreach($items as $item)
            {
                if(isset($item->volumeInfo->imageLinks) && isset($item->volumeInfo->imageLinks->thumbnail))
                {
                    $correctItem = $item;
                }
            }
            //sinon on prend le premier item
            if(!$correctItem)
            {
                $correctItem = $items[0];
            }

            $infos = $correctItem->volumeInfo;
            //on ajoute les infos à l'objet Book
            if(isset($infos->publishedDate))
            {
                $book->setPublishedAt($infos->publishedDate);
            }
            if(isset($infos->imageLinks) && isset($infos->imageLinks->thumbnail))
            {
                $book->setThumbnail($infos->imageLinks->thumbnail);
            }
        }
        catch(Exception $e)
        {
            //
        }
    }
}