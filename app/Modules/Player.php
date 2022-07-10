<?php

namespace App\Modules;
use App\Contracts\PlayerContract;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class Player implements PlayerContract{

    public $type;
    
     /**
     * Retrieve all player data from the API
     *
     * @param Request $request - request
     * @return view
     */
    public function allPlayers($request){
        $this->type = strpos($request->path(), 'nba') !== false? 'nba' : 'rugby';

        $baseEndpoint = config('api.endpoint');
        if($this->type == 'nba'){

            $nbaPlayers = Http::get("$baseEndpoint/nba.players?API_KEY=" . config('api.key'), [
                'API_KEY' => config('api.key'),
            ])->json();
    
            $stats = Http::get("$baseEndpoint/nba.stats?API_KEY=" . config('api.key'), [
                'API_KEY' => config('api.key'),
            ])->json();

            $players = $this->cleanData($nbaPlayers, $stats);
        }else{

            $rugbyPlayers = Http::get("$baseEndpoint/allblacks/?API_KEY=" . config('api.key'), [
                'API_KEY' => config('api.key'),
            ])->json();

            $players = $this->cleanData($rugbyPlayers, null);
        }

        if(!$players){
            return response()->json(['error' => 'No available data'], 201);
        }
        return view('player', compact('players'));  
    }

    /**
     * Format the data of players 
     *
     * @param string $players - list of players
     * @param string $stats - list of stats connected to players
     * @return string view
     */
    public function cleanData($players, $stats){
        $hold = [];
        if($this->type == 'nba'){
            foreach($players as $player){

                $filteredPlayer = collect($player); //convert to collect instance

                $playerID = $filteredPlayer->get('id');
                $filteredPlayer->put('name', $filteredPlayer->get('first_name') ." ". $filteredPlayer->get('last_name'));
                $filteredPlayer->put('image', $this->image($filteredPlayer->get('name')));
                $filteredPlayer->put('teamImage', $this->teamImage($filteredPlayer->get('current_team')));
                $filteredPlayer->put('height', $filteredPlayer->get('inches') . ' Inches');
                $filteredPlayer->put('color', '#0033cc');

                foreach($stats as $playerStat){
                    if($playerStat['player_id'] == $playerID){
                        $filteredPlayer->put('stats', $playerStat);
                    }
                }
                $filteredPlayer->put('featured', $this->feature($filteredPlayer));
                array_push($hold, $filteredPlayer);
            }
        }else{
            foreach($players as $player){

                $id = $player['id'];
                $filteredPlayer = $this->player($id);

                if(count($filteredPlayer) > 0){
                    // split first & last name
                    $names = collect(preg_split('/\s+/', $filteredPlayer->get('name')));
                    $filteredPlayer->put('last_name', $names->pop());
                    $filteredPlayer->put('first_name', $names->join(' '));
                    $filteredPlayer->put('color', '#000');
                    $filteredPlayer->put('teamImage', 'allblacks.png');

                    // determine the image filename from the name
                    $filteredPlayer->put('image', $this->image($filteredPlayer->get('name')));

                    // stats to feature
                    $filteredPlayer->put('featured', $this->feature($filteredPlayer));
                    // $filteredPlayer->put('pagination', $this->firstAndLastPage($id));
                }

                array_push($hold, $filteredPlayer);
            }
        }
        
        return $hold;

    }

    /**
     * Retrieve player data from the API
     *
     * @param int $id
     * @return \Illuminate\Support\Collection
     */
    public function player($id){
        // $baseEndpoint = 'https://www.zeald.com/developer-tests-api/x_endpoint';
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
    public function image($name){
        if($this->type == 'nba'){
            return 'nba/'. preg_replace('/\W+/', '-', strtolower($name)) . '.png';
        }
        return 'allblacks/'. preg_replace('/\W+/', '-', strtolower($name)) . '.png';
    }

    /**
     * Determine the image for the player based off their name
     *
     * @param string $team name
     * @return string filename
     */
    public function teamImage($name){
        return strtolower($name). '.png';
    }

     /**
     * Build stats to feature for this player
     *
     * @param \Illuminate\Support\Collection $player
     * @return \Illuminate\Support\Collection features
     */
    public function feature($player){

        if(!$player){
            return null;
        }

        if($this->type == 'nba'){
            $stats = $player->get('stats');
            $gamePlayed = $stats['games']; //count of game played
            
            return collect([
                ['label' => 'Rebound Per Game', 'value' => number_format((float) $stats['rebounds'] / $gamePlayed , 2, '.', '')],
                ['label' => 'Points Per Game', 'value' => number_format((float) $stats['points'] / $gamePlayed , 2, '.', '')],
                ['label' => 'Assist Per Game', 'value' => number_format((float) $stats['assists'] / $gamePlayed , 2, '.', '')],
            ]);
        }

        return collect([
            ['label' => 'Points', 'value' => $player->get('points')],
            ['label' => 'Games', 'value' => $player->get('games')],
            ['label' => 'Tries', 'value' => $player->get('tries')],
        ]);
    }

    public function show($request, $id){
        $this->type = strpos($request->path(), 'nba') !== false? 'nba' : 'rugby';

        $baseEndpoint = config('api.endpoint');
        if($this->type == 'nba'){
            $nbaPlayers = Http::get("$baseEndpoint/nba.players?API_KEY=" . config('api.key'), [
                'API_KEY' => config('api.key'),
            ])->json();
    
            $stats = Http::get("$baseEndpoint/nba.stats?API_KEY=" . config('api.key'), [
                'API_KEY' => config('api.key'),
            ])->json();

            $players = $this->cleanData($nbaPlayers, $stats);

            $activePlayer = $this->getActivePlayerData($players, $id);

        }else{

            $activePlayer = $this->player($id);

            if(count($activePlayer) > 0){
                    // split first & last name
                $names = collect(preg_split('/\s+/', $activePlayer->get('name')));
                $activePlayer->put('last_name', $names->pop());
                $activePlayer->put('first_name', $names->join(' '));
                $activePlayer->put('color', '#000');
                $activePlayer->put('teamImage', 'allblacks.png');

                // determine the image filename from the name
                $activePlayer->put('image', $this->image($activePlayer->get('name')));

                // stats to feature
                $activePlayer->put('featured', $this->feature($activePlayer));
            }

            $rugbyPlayers = Http::get("$baseEndpoint/allblacks/?API_KEY=" . config('api.key'), [
                'API_KEY' => config('api.key'),
            ])->json();
            
            $players = $this->cleanData($rugbyPlayers, null);
        }

        if(!$activePlayer || count($activePlayer) < 1){
            return response()->json(['error' => 'No existing Player data'], 201);
        }

        return view('player', compact('players', 'activePlayer'));  
    }

    public function getActivePlayerData($players, $id){

        foreach($players as $player){
            if($player['id'] == $id){
                return $player;
            }
        }

        return null;
    }
}