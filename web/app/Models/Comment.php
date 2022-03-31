<?php

namespace App\Models;

/**
 * Class PostViewModel
 * @property integer $book_id
 * @property string $commenter_ip
 * @property string $anonymous_user
 * @property string $comment
 * @property string $comment_parent
 * @package App\ViewModels
 * @OA\Schema(
 *     schema="CommentRequest",
 *     type="object",
 *     title="CommentRequest",
 *     required={"comment"},
 *     properties={
 *         @OA\Property(property="book_id", type="integer"),
 *         @OA\Property(property="commenter_ip", type="string")
 *         @OA\Property(property="anonymous_user", type="string")
 *         @OA\Property(property="comment", type="string")
 *         @OA\Property(property="comment_parent", type="string")
 *     }
 * )
 */

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $mappings = [
        'book_id' => 'book_id',
        'commenter_ip' => 'commenter_ip',
        'anonymous_user' => 'anonymous_user',
        'comment' => 'comment',
        'comment_parent' => 'comment_parent',
    ];

    protected $fillable = [
        'book_id',
        'commenter_ip',
        'anonymous_user',
        'comment',
        'comment_parent'
    ];

    protected $hidden = [
        'commenter_ip'
    ];

//    protected $dateFormat = 'U';
}
