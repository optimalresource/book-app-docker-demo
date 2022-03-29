<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class CommentController extends Controller
{
    public function index(Request $request) {
        try {
            if ($request->query('book_id') === null) {
                return response(['status' => 'error', 'message' => 'Please supply the book_id parameter, it cannot be empty or null'], 404);
            }

            $book_id = strip_tags($request->query('book_id'));
            $comments = Comment::where('book_id', '=', $book_id)->get();

            return response()->json($comments);
        }catch (\Exception $e) {
            return response(['status' =>'failed', 'message' => $e->getMessage()], 500);
        }
    }

    public static function getComments($book_id) {
        try {
            if ($book_id === null) {
                return ['status' => 'error', 'message' => 'Please supply the book_id parameter, it cannot be empty or null'];
            }

            $book_id = strip_tags($book_id);
            $comments = Comment::where('book_id', '=', $book_id)->get();

            return ['status' => 'success', 'response' => $comments];
        }catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function store(Request $request) {
        try{
            if(empty($request->input('book_id')) OR empty($request->input('comment'))){
                return response(['status' => 'error', 'message' => 'Book ID and comment cannot be empty'], 404);
            }

            $comment = strip_tags($request->input('comment'));
            $book_id = strip_tags($request->input('book_id'));
            $comment_parent = $request->input('comment_parent') !== null ? $request->input('comment_parent') : null;

            if($comment_parent !== null) {
                if(!(Comment::where('id', '=', $comment_parent)->exists())){
                    return response(['status' => 'error', 'message' => 'Your cannot comment under a non existing comment'], 404);
                }
            }

            if(strlen($comment) > 500){
                return response(['status' => 'error', 'message' => 'Your comment cannot exceed 500 characters'], 400);
            }

            $client = new Client();
            $res = $client->request('GET', 'https://www.anapioficeandfire.com/api/books/'.$book_id);

            if($res->getStatusCode() == 404) {
                return response(['status' => 'error', 'message' => 'You cannot comment on a book that does not exist'], 404);
            }

            $new_comment = new Comment();
            $new_comment->book_id = $book_id;
            $new_comment->comment = $comment;
            $new_comment->commenter_ip = $request->ip();
            $anonymous_user = $this->generateAnonymousUser($request->ip());

            if($anonymous_user == 'failed') {
                return response(['status' => 'error', 'message' => 'Oops, some error occurred'], 500);
            }

            if($this->checkDuplicateComment($comment, $request->ip(), $book_id)) {
                return response(['status' => 'error', 'message' => 'You cannot comment the same thing twice'], 400);
            }

            $new_comment->anonymous_user = $anonymous_user;
            $new_comment->comment_parent = $comment_parent;

            if($new_comment->save()){
                return response()->json(['status' =>'success', 'message' => 'Comment saved successfully']);
            }
        }catch(\Exception $e){
            return response(['status' =>'failed', 'message' => $e->getMessage()], 500);
        }
    }

    public function generateAnonymousUser($ip) {
        try {
            $check_ip = Comment::where('commenter_ip', '=', \strip_tags($ip))->first();
            if ($check_ip) return $check_ip->anonymous_user;
            return 'user_' . strtolower(Str::random(8));
        }catch (\Exception $e) {
            return "failed";
        }
    }

    public function checkDuplicateComment($comment, $ip, $book_id, $id=null) {
        try {
            if($id === null) {
                $check_comment = Comment::where('commenter_ip', '=', \strip_tags($ip))->where('comment', '=', $comment)->where('book_id', '=', $book_id)->exists();
            }else {
                $check_comment = Comment::where('commenter_ip', '=', \strip_tags($ip))->where('comment', '=', $comment)->where('book_id', '=', $book_id)->where('id', '!=', $id)->exists();
            }
            if ($check_comment) return true;
            return false;
        }catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function update(Request $request, $id) {
        try {
            $comment = Comment::findOrFail($id);

            if (empty($request->input('comment'))) {
                return response(['status' => 'error', 'message' => 'Comment cannot be empty'], 404);
            }

            if ($request->ip() != $comment->commenter_ip) {
                return response(['status' => 'error', 'message' => 'You are unauthorized, cannot edit another user\'s comment'], 401);
            }

            $new_comment = strip_tags($request->input('comment'));

            if (strlen($new_comment) > 500) {
                return response(['status' => 'error', 'message' => 'Your comment cannot exceed 500 characters'], 400);
            }

            $comment->comment = $new_comment;

            if($this->checkDuplicateComment($new_comment, $request->ip(), $comment->book_id, $id)) {
                return response(['status' => 'error', 'message' => 'You cannot comment the same thing twice for one book'], 400);
            }

            if ($comment->save()) {
                return response()->json(['status' => 'success', 'message' => 'Comment edited successfully']);
            }
        }catch (\Exception $e) {
            return response(['status' =>'failed', 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy(Request $request, $id) {
        try {
            $comment = Comment::findOrFail($id);

            if ($request->ip() != $comment->commenter_ip) {
                return response(['status' => 'error', 'message' => 'You are unauthorized, cannot delete another user\'s comment'], 401);
            }

            if ($comment->delete()) {
                return response()->json(['status' => 'success', 'message' => 'Comment deleted successfully']);
            }
        }catch (\Exception $e) {
            return response(['status' =>'failed', 'message' => $e->getMessage()], 500);
        }
    }

    public function show($id) {
        try {
            $comment = Comment::findOrFail($id);
            return response()->json($comment);
        }catch (\Exception $e) {
            return response(['status' =>'failed', 'message' => $e->getMessage()], 500);
        }
    }
}
