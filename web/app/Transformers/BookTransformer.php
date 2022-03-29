<?php

namespace App\Transformers;

/**
 * @OA\Schema(
 *     schema="BookResponse",
 *     type="object",
 *     title="BookResponse",
 *     properties={
 *         @OA\Property(property="id", type="integer"),
 *         @OA\Property(property="attributes", type="object", properties={
 *             @OA\Property(property="title", type="string"),
 *             @OA\Property(property="body", type="string")
 *         }),
 *         @OA\Property(property="relationships", type="array", @OA\Items({
 *
 *         })),
 *     }
 * )
 */
class PostTransformer extends Transformer
{
    public $type = 'book';

    protected $availableIncludes = ['user'];

    public function transform($book)
    {
        return [
            'id' => $book->id,
            'title' => $book->title,
            'body' => $book->body,
        ];
    }
}
