<?php

namespace Database\Seeders;

use App\Models\Player;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlayerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $players = [
            [
                'name' => 'Lionel Messi',
                'team' => 'Inter Miami',
                'position' => 'Forward',
                'image' => 'players/messi.jpg',
                'rating' => 5,
            ],
            [
                'name' => 'Cristiano Ronaldo',
                'team' => 'Al Nassr',
                'position' => 'Forward',
                'image' => 'players/ronaldo.jpg',
                'rating' => 5,
            ],
            [
                'name' => 'Erling Haaland',
                'team' => 'Manchester City',
                'position' => 'Forward',
                'image' => 'players/haaland.jpg',
                'rating' => 5,
            ],
            [
                'name' => 'Kevin De Bruyne',
                'team' => 'Manchester City',
                'position' => 'Midfielder',
                'image' => 'players/debruyne.jpg',
                'rating' => 5,
            ],
            [
                'name' => 'Mohamed Salah',
                'team' => 'Liverpool',
                'position' => 'Forward',
                'image' => 'players/salah.jpg',
                'rating' => 4,
            ],
            [
                'name' => 'Luka Modrić',
                'team' => 'Real Madrid',
                'position' => 'Midfielder',
                'image' => 'players/modric.jpg',
                'rating' => 4,
            ],
            [
                'name' => 'Virgil van Dijk',
                'team' => 'Liverpool',
                'position' => 'Defender',
                'image' => 'players/vandijk.jpg',
                'rating' => 4,
            ],
            [
                'name' => 'Achraf Hakimi',
                'team' => 'Paris Saint-Germain',
                'position' => 'Defender',
                'image' => 'players/hakimi.jpg',
                'rating' => 4,
            ],
            [
                'name' => 'Jude Bellingham',
                'team' => 'Real Madrid',
                'position' => 'Midfielder',
                'image' => 'players/bellingham.jpg',
                'rating' => 4,
            ],
            [
                'name' => 'Riyad Mahrez',
                'team' => 'Al Ahli',
                'position' => 'Midfielder',
                'image' => 'players/mahrez.jpg',
                'rating' => 3,
            ],
            [
                'name' => 'Álvaro Morata',
                'team' => 'Atlético Madrid',
                'position' => 'Forward',
                'image' => 'players/morata.jpg',
                'rating' => 3,
            ],
            [
                'name' => 'Luke Shaw',
                'team' => 'Manchester United',
                'position' => 'Defender',
                'image' => 'players/shaw.jpg',
                'rating' => 3,
            ],
            [
                'name' => 'Dominic Calvert-Lewin',
                'team' => 'Everton',
                'position' => 'Forward',
                'image' => 'players/calvert-lewin.jpg',
                'rating' => 2,
            ],
            [
                'name' => 'James Ward-Prowse',
                'team' => 'West Ham United',
                'position' => 'Midfielder',
                'image' => 'players/ward-prowse.jpg',
                'rating' => 2,
            ],
            [
                'name' => 'Ben White',
                'team' => 'Arsenal',
                'position' => 'Defender',
                'image' => 'players/white.jpg',
                'rating' => 2,
            ],
            [
                'name' => 'Odion Ighalo',
                'team' => 'Al Wehda',
                'position' => 'Forward',
                'image' => 'players/ighalo.jpg',
                'rating' => 2,
            ],
            [
                'name' => 'Taiwo Awoniyi',
                'team' => 'Nottingham Forest',
                'position' => 'Forward',
                'image' => 'players/awoniyi.jpg',
                'rating' => 2,
            ],
            [
                'name' => 'Joe Aribo',
                'team' => 'Southampton',
                'position' => 'Midfielder',
                'image' => 'players/aribo.jpg',
                'rating' => 2,
            ],
            [
                'name' => 'Semi Ajayi',
                'team' => 'West Bromwich Albion',
                'position' => 'Defender',
                'image' => 'players/ajayi.jpg',
                'rating' => 1,
            ],
            [
                'name' => 'Kenneth Omeruo',
                'team' => 'Kasımpaşa',
                'position' => 'Defender',
                'image' => 'players/omeruo.jpg',
                'rating' => 1,
            ]
        ];

        foreach ($players as $player) {
            Player::updateOrCreate(['name' => $player['name']], $player);
        }
    }
}
