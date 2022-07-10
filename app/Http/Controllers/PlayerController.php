<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use App\Modules\Player;

use Illuminate\Contracts\Cache\Repository as Cache;
class PlayerController extends Controller
{

    /**
     * Fetch the players 
     *
     * @param string $request
     * @return string view
     */
    public function allPlayers(Request $request, Player $player){
        return $player->allPlayers($request);
    }

    public function show(Request $request, $id, Player $player){
        return $player->show($request, $id);
    }
}
