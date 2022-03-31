<?php

namespace App\Transformers;

/**
 * @OA\Schema(
 *     schema="BookResponse",
 *     type="object",
 *     title="BookResponse",
 *     properties={
 *         @OA\Property(property="page", type="integer"),
 *         @OA\Property(property="pageSize", type="integer"),
 *         @OA\Property(property="name", type="string"),
 *         @OA\Property(property="fromReleaseDate", type="string"),
 *         @OA\Property(property="toReleaseDate", type="string")
 *     }
 * )
 */
class PostTransformer extends Transformer
{
    public $type = 'book';

    public function transform($book)
    {
        return [
            'toReleaseDate' => $book->toReleaseDate,
            'fromReleaseDate' => $book->fromReleaseDate,
            'name' => $book->name,
            'pageSize' => $book->pageSize,
            'page' => $book->page,
        ];
    }
}
