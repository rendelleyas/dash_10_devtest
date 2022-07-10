<?php

namespace App\Http\Controllers;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class PlayerController extends Controller
{

    public $allBlacks;
    public function __construct()
    {
        $this->allBlacks =  $this->fetchAllBlack();
    }

    public function test($id = null){
        $id = $id ?? 1;
        return $this->firstAndLastPage($id);
    }
    /**
     * Show a player profile
     *
     * @param  int $id
     * @return \Illuminate\View\View
     */
    public function show($id = null)
    {
        $id = $id ?? 1;

        $player = $this->player($id);

        // split first & last name
        $names = collect(preg_split('/\s+/', $player->get('name')));
        $player->put('last_name', $names->pop());
        $player->put('first_name', $names->join(' '));

        // determine the image filename from the name
        $player->put('image', $this->image($player->get('name')));

        // stats to feature
        $player->put('featured', $this->feature($player));
        $player->put('pagination', $this->firstAndLastPage($id));
        
        // return $player;

        return view('player', $player);
    }

    /**
     * Retrieve player data from the API
     *
     * @param int $id
     * @return \Illuminate\Support\Collection
     */
    protected function player(int $id): Collection
    {
        // return collect([
        //     "tries" => 21,
        //     "games" => 102,
        //     "number" => 9,
        //     "position" => "Halfback",
        //     "points" => 107,
        //     "name" => "Aaron Smith",
        //     "height" => 173,
        //     "age" => 33,
        //     "conversions" => 1,
        //     "weight" => 83,
        //     "penalties" => 0,
        //     "id" => "1",
        // ]);

        // $baseEndpoint = 'https://www.zeald.com/developer-tests-api/x_endpoint/allblacks';
        $baseEndpoint = config('api.endpoint');

        $json = Http::get("$baseEndpoint/allblacks/id/$id", [
            'API_KEY' => config('api.key'),
        ])->json();

        return collect(array_shift($json));
    }

    /**
     * Determine the image for the player based off their name
     *
     * @param string $name
     * @return string filename
     */
    protected function image(string $name): string
    {
        return preg_replace('/\W+/', '-', strtolower($name)) . '.png';
    }

    /**
     * Build stats to feature for this player
     *
     * @param \Illuminate\Support\Collection $player
     * @return \Illuminate\Support\Collection features
     */
    protected function feature(Collection $player): Collection
    {
        return collect([
            ['label' => 'Points', 'value' => $player->get('points')],
            ['label' => 'Games', 'value' => $player->get('games')],
            ['label' => 'Tries', 'value' => $player->get('tries')],
        ]);
    }

     /**
     * Fetch all of the rugby player data
     *
     * @return Array
     */
    public function fetchAllBlack(){
        $baseEndpoint = config('api.endpoint');
        return Http::get("$baseEndpoint/allblacks?API_KEY=" . config('api.key'), [
            'API_KEY' => config('api.key'),
        ])->json();
    }


    /**
     * Process the pagination. next and prev links
     *
     * @param $currentKey - active id
     */
    public function firstAndLastPage($currentKey){
        $currentKey = (int) $currentKey;

        $json = $this->allBlacks;

        $startKey = $json[0]['id'];
        $lastKey = $json[count($json) - 1]['id'];

        // process next button
        if($lastKey == $currentKey){
            $nextLink =  $json[0]['id']; //start at first 
            $nexButton =  [
                'link' => 'allblacks/'.  $nextLink,
                'name' => $json[$nextLink - 1]['name']
            ];
        }else{
            $nextLink =  $currentKey + 1;
            $nexButton =  [
                'link' => 'allblacks/'.  $nextLink,
                'name' => $json[$nextLink - 1]['name']
            ];
        }   

        //process prev button
        if($startKey == $currentKey){
            $prevLink =  $json[$lastKey - 1]['id']; //prev is at end item
            $prevButton =  [
                'link' =>  'allblacks/'.  $prevLink,
                'name' => $json[$prevLink - 1]['name'],
            ];
        }else{
            $prevLink =  $currentKey - 1;
            $prevButton =  [
                'link' => 'allblacks/'. $prevLink,
                'name' => $json[$prevLink - 1]['name'],
            ];
        }  

        // process current button
        $currentButton = [
            'link' => 'allblacks/'.  $currentKey,
            'name' => $json[$currentKey - 1]['name'],
        ];

        return [
            'start' => $json[0]['id'],
            'end' => $json[count($json) - 1]['id'],
            'current' => $currentButton,
            'prev' => $prevButton,
            'next' => $nexButton
        ];
    }
}
