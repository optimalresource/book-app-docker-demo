<?php

namespace App\Utilities;

use App\Http\Controllers\CommentController;

class AddCommentsToBookObject
{
    public $mergedArray = [];
    public function getMerged($arr = []) {
        try {
            if(!array_key_exists ( 'url', $arr)){
                foreach($arr as $book) {
                    $comments = CommentController::getComments(substr($book['url'], 44));
                    if($comments['status'] == 'success') {
                        $book['comments_count'] = count($comments['response']);
                    }else {
                        $book['comments_count'] = 0;
                    }
                    $this->mergedArray[] = $book;
                }
                usort($this->mergedArray, array($this, 'compareBooksByTimeStamp'));
            }else {
                $comments = CommentController::getComments(substr($arr['url'], 44));
                if($comments['status'] == 'success') {
                    $arr['comments_count'] = count($comments['response']);
                }else {
                    $arr['comments_count'] = 0;
                }
                $this->mergedArray = $arr;
            }
            return stripslashes(json_encode($this->mergedArray));
        }catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    private static function compareBooksByTimeStamp($book1, $book2)
    {
        if (strtotime($book2['released']) < strtotime($book1['released']))
            return 1;
        else if (strtotime($book2['released']) > strtotime($book1['released']))
            return -1;
        else
            return 0;
    }
}
