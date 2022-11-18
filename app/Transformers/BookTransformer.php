<?php

namespace App\Transformers;

use App\Models\Book;
use App\Models\Author;
use League\Fractal\TransformerAbstract;

class BookTransformer extends TransformerAbstract
{
    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected array $defaultIncludes = [
        //
    ];
    
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected array $availableIncludes = [
        'author'
    ];
    
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Book $book)
    {
        return [
            'id'    => (int) $book->id,
            'author_id' => $book->author_id,
            'name'  => $book->name,
            'year'  => $book->year,
            'token' => $book->token,
            'author' => $book->author

        ];
    }

    public function includeAuthor(Book $book) {
        $author = $book->author;
        return $this->item($author, new AuthorTransformer);
    }
}
