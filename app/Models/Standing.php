<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Standing extends Model
{
    protected $fillable = [
        'team_id',
        'points',
        'won',
        'lose',
        'draw',
        'played',
        'goal_drawn'
    ];
}
