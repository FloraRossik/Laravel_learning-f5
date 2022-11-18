<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\Author;
//use League\Fractal\Serializer\JsonApiSerializer;

class AuthorTransformer extends TransformerAbstract
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
        //
    ];

    /**
     * A Fractal transformer.
     *
     * @return array
     */

    public function transform(Author $author)
    {
        return [
            'id'    => $author->id,
            'name'  => $author->name,
            'year' => $author->year,
            'books_count' => $author->books_count
        ];
    }
}