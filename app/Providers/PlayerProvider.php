<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Contracts\PlayerContract;
use App\Modules\Player;

class PlayerProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(PlayerContract::class, Player::class);    

        $this->app->bind(
            PlayerContract::class, 
            Player::class
        ); 
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
