<?php

namespace App\Http\Controllers;

use App\Http\Controllers\CommentController;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use App\Utilities\AddCommentsToBookObject;

class BookController extends Controller
{
    private $headers = [
        'Accept' => 'application/vnd.anapioficeandfire+json; version=1',
        'Connection' => 'close'
    ]; 

    // private $headers = [
    //     "http" => [
    //         "method" => "GET",
    //         "header" => "Accept: application/vnd.anapioficeandfire+json; version=1" .
    //             "Connection: close"
    //     ]
    // ];

    public function index(Request $request) {
        $page = $request->query('page') !== null ? $request->query('page') : 1;
        $pageSize = $request->query('pageSize') !== null ? $request->query('pageSize') > 50 ? 50 : $request->query('pageSize') : 50;
        $name = $request->query('name') !== null ? '&name='.$request->query('name') : '';
        $fromReleaseDate = $request->query('fromReleaseDate') !== null ? '&fromReleaseDate='.$request->query('fromReleaseDate') : '';
        $toReleaseDate = $request->query('toReleaseDate') !== null ? '&toReleaseDate='.$request->query('toReleaseDate') : '';

        try{
            $client = new Client();
            $res = $client->request('GET', env('ICE_AND_FIRE_BASE_URL').'/books?page='.$page.'&pageSize='.$pageSize.$name.$fromReleaseDate.$toReleaseDate,[
                "headers" => $this->headers
            ]);
            $data = $res->getBody()->getContents();
        }catch(GuzzleException $guzzleException) {
            return response(['status' => 'error', 'message' => $guzzleException->getMessage()], 500);
        }

        try{
            // $context = stream_context_create($this->headers);
            // $data = file_get_contents(env('ICE_AND_FIRE_BASE_URL').'/books?page='.$page.'&pageSize='.$pageSize.$name.$fromReleaseDate.$toReleaseDate, false, $context);
            $data = json_decode($data, JSON_UNESCAPED_SLASHES );
            $addComments = new AddCommentsToBookObject();
            $mergedArray = $addComments->getMerged($data);
            return json_decode($mergedArray, JSON_UNESCAPED_SLASHES );
        }catch(\Exception $e) {
            return response(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function show($id) {
        try {
            if ($id === null OR is_nan($id)) {
                return ['status' => 'error', 'message' => 'Please supply the book id, integer is required'];
            }

            try{
                $client = new Client();
                $res = $client->request('GET', env('ICE_AND_FIRE_BASE_URL').'/books/'.$id,[
                    "headers" => $this->headers
                ]);
                $data = $res->getBody()->getContents();
            }catch(GuzzleException $guzzleException) {
                return response(['status' => 'error', 'message' => $guzzleException->getMessage()], 500);
            }

            // $context = stream_context_create($this->headers);
            // $data = file_get_contents( env('ICE_AND_FIRE_BASE_URL').'/books/'.$id, false, $context);
            $data = json_decode($data, JSON_UNESCAPED_SLASHES );
            $addComments = new AddCommentsToBookObject();
            $mergedArray = $addComments->getMerged($data);
            return json_decode($mergedArray, JSON_UNESCAPED_SLASHES );
        }catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
}

