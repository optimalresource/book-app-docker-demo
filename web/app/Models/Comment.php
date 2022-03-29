<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

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
