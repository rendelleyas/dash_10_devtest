<?php

namespace App\Contracts;


interface PlayerContract{

    /**
     * Retrieve all player data from the API
     *
     * @param Request $request - request
     * @return view
     */
    public function allPlayers($request);


   
    public function cleanData($players, $stats);

    /**
     * Retrieve player data from the API
     *
     * @param int $id
     * @return \Illuminate\Support\Collection
     */
    public function player($id);


    /**
     * Determine the image for the player based off their name
     *
     * @param string $name
     * @return string filename
     */
    public function image($name);


    /**
     * Determine the image for the player based off their name
     *
     * @param string $team name
     * @return string filename
     */
    public function teamImage($name);


    /**
     * Build stats to feature for this player
     *
     * @param \Illuminate\Support\Collection $player
     * @return \Illuminate\Support\Collection features
     */
    public function feature($player);



    /**
     * Retrieve the players specific data
     *
     * @param Request $request - request
     * @param int $id
     * @return view
     */
    public function show($player, $id);


    /**
     * Retrieve the player specific data: particularly nba player
     *
     * @param Request $request - request
     * @param int $id
     * @return view
     */
    public function getActivePlayerData($player, $id);

}
