<?php

namespace App\Http\Controllers;

use App\Utilities\AddCommentsToBookObject;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;

class CharacterController extends Controller
{
    private $headers = [
        'Accept' => 'application/vnd.anapioficeandfire+json; version=1',
        'Connection' => 'close'
    ];

    public function index(Request $request) {
        $page = $request->query('page') !== null ? $request->query('page') : 1;
        $pageSize = $request->query('pageSize') !== null ? $request->query('pageSize') > 50 ? 50 : $request->query('pageSize') : 50;
        $name = $request->query('name') !== null ? '&name='.$request->query('name') : '';
        $gender = $request->query('gender') !== null ? '&gender='.$request->query('gender') : '';
        $age = $request->query('age') !== null ? '&born='.$request->query('age') : '';
        $order = $request->query('order') !== null ? $request->query('order') : 'asc';

        try{
            $client = new Client();
            $res = $client->request('GET', 'https://www.anapioficeandfire.com/api/characters?page='.$page.'&pageSize='.$pageSize.$name.$gender.$age, [
                "headers" => $this->headers
            ]);
            $data = $res->getBody()->getContents();
        }catch(GuzzleException $guzzleException) {
            return response("A server error occured", 500);
        }

        try{
            $data = json_decode($data, JSON_UNESCAPED_SLASHES );
            $total = count($data);
            $sortedCharacters = $this->makeSort($data, $order);
            return response(['response' => json_decode($sortedCharacters, JSON_UNESCAPED_SLASHES ), 'total_characters' => $total]);
        }catch(\Exception $e) {
            return response("A server error occured", 500);
        }
    }

    private static function sortCharactersByAsc($character1, $character2)
    {
        if ($character2['name'] < $character1['name'])
            return 1;
        else if ($character2['name'] > $character1['name'])
            return -1;
        else
            return 0;
    }

    private static function sortCharactersByDesc($character1, $character2)
    {
        if ($character1['name'] < $character2['name'])
            return 1;
        else if ($character1['name'] > $character2['name'])
            return -1;
        else
            return 0;
    }

    public function makeSort($arr = [], $order = 'asc') {
        try {
            if($order == 'asc') {
                usort($arr, array($this, 'sortCharactersByAsc'));
            }else {
                usort($arr, array($this, 'sortCharactersByDesc'));
            }
            return stripslashes(json_encode($arr));
        }catch(\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
}
