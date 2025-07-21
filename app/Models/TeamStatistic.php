 <?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeamStatistic extends Model
{
    protected $fillable = [
        'fixture_id',
        'team_id',
        'shots_on_goal',
        'shots_off_goal',
        'total_shots',
        'blocked_shots',
        'shots_insidebox',
        'shots_outsidebox',
        'fouls',
        'corner_kicks',
        'offsides',
        'ball_possession',
        'yellow_cards',
        'red_cards',
        'goalkeeper_saves',
        'total_passes',
        'passes_accurate',
        'passes_pct',
    ];
}
