<?php

use App\Game;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run() {
         $this->call(GameTableSeeder::class);
    }
}

class GameTableSeeder extends Seeder {

    public function run() {
        DB::table(DB_GAME_TABLE)->delete();

        Game::create([
            GAME_ID => 1,
            GAME_NAME => 'Pro Evolution Soccer 2019',
            GAME_IMAGE => 'https://steamcdn-a.akamaihd.net/steam/apps/770240/header.jpg?t=1535587388',
            PLAYING_TYPE => '["knock_out"]',
            PLATFORMS => '["playstation","xbox","pc"]',
            DEVELOPER => 'konami',
            GAME_TYPE => 'sport/football'
        ]);
        Game::create([
            GAME_ID => 2,
            GAME_NAME => 'FIFA 2019',
            GAME_IMAGE => 'https://s3.eu-central-1.amazonaws.com/images.gamesatis.com/products/images/000/023/869/big/fifa-19.jpg',
            PLAYING_TYPE => '["knock_out"]',
            PLATFORMS => '["playstation","xbox","pc"]',
            DEVELOPER => 'ea games',
            GAME_TYPE => 'sport/football'
        ]);
    }

}
