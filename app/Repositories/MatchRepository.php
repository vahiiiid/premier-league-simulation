<?php
/**
 * Created by PhpStorm.
 * User: vahid
 * Date: 3/14/19
 * Time: 2:35 PM
 */

namespace App\Repositories;


use App\Models\Match;
use App\Models\Team;
use App\Models\Week;

class MatchRepository
{

    protected $team;
    protected $match;
    protected $week;

    public function __construct(Team $team, Match $match, Week $week)
    {
        $this->team = $team;
        $this->match = $match;
        $this->week = $week;
    }


    public function getTeamId()
    {
        return $this->team->pluck('id');
    }


    public function getWeeksId()
    {
        return $this->week->pluck('id');
    }


    public function getWeeks()
    {
        return $this->week->get();
    }

    public function createFixture()
    {
        foreach ($this->getWeeksId() as $week) {
            foreach ($this->iterateTeams($this->getTeamId()) as $value) {
                if (0 == $this->checkMatch($week, $value)) {
                    $this->match->create(['home_team' => $value[0], 'away_team' => $value[1], 'week_id' => $week]);
                }

            }

        }
    }

    private function iterateTeams($teams)
    {
        $collection = collect($teams);
        $matrix = $collection->crossJoin($teams);
        $data = $matrix->reject(function ($items) {

            if ($items[0] == $items[1]) {
                return $items;
            }
        })->shuffle();
        return $data->all();
    }

    public function checkMatch($week_id, $teams)
    {
        return $this->match->where('week_id', '=', $week_id)
            ->whereRaw('(home_team IN(' . implode(',', $teams) . ') OR away_team IN(' . implode(',', $teams) . '))')
            ->count();
    }


    /**
     * @return mixed
     */
    public function getFixture()
    {

        return $this->match->select(
            'matches.id',
            'matches.status',
            'matches.week_id',
            'matches.home_team_goal',
            'matches.away_team_goal',
            'week_id',
            'home.name as home_team',
            'home.logo as home_logo',
            'home.shirt as home_shirt',
            'away.logo as away_logo',
            'away.shirt as away_shirt',
            'away.name as away_team')
            ->join('weeks', 'weeks.id', '=', 'matches.week_id')
            ->join('teams as home', 'home.id', '=', 'matches.home_team')
            ->join('teams as away', 'away.id', '=', 'matches.away_team')
            ->orderBy('week_id', 'ASC')
            ->get();
    }

    /**
     * @param $week_id
     * @return mixed
     */
    public function getFixtureByWeekId($week_id)
    {

        return $this->match->select(
            'matches.id',
            'matches.status',
            'matches.week_id',
            'matches.home_team_goal',
            'matches.away_team_goal',
            'week_id',
            'weeks.title',
            'home.logo as home_logo',
            'away.logo as away_logo',
            'home.name as home_team',
            'away.name as away_team')
            ->join('weeks', 'weeks.id', '=', 'matches.week_id')
            ->join('teams as home', 'home.id', '=', 'matches.home_team')
            ->join('teams as away', 'away.id', '=', 'matches.away_team')
            ->where('matches.week_id', '=', $week_id)
            ->orderBy('matches.id', 'ASC')
            ->get();
    }

    /**
     * @param $week
     * @return mixed
     */
    public function getMatchesFromWeek($week)
    {
        return $this->match->where([['week_id', '=', $week], ['status', '=', 0]])->get();
    }


    public function getAllMatches($status = 0)
    {
        return $this->match->where('status', '=', $status)->get();
    }

    /**
     * @param $homeScore
     * @param $awayScore
     * @param $home
     * @param $away
     */
    public function updateMatchScore($homeScore, $awayScore, $home, $away)
    {
        if ($homeScore > $awayScore) {
            $home->won += 1;
            $home->points += 3;
            $home->goal_drawn += ($homeScore - $awayScore);
            $away->lose += 1;
            $away->goal_drawn += ($awayScore - $homeScore);
        } elseif ($awayScore > $homeScore) {
            $away->won += 1;
            $away->points += 3;
            $away->goal_drawn += ($awayScore - $homeScore);
            $home->lose += 1;
            $home->goal_drawn += ($homeScore - $awayScore);
        } else {
            $home->draw += 1;
            $away->draw += 1;
            $home->points += 1;
            $away->points += 1;
        }

        $home->played += 1;
        $away->played += 1;
        $home->save();
        $away->save();
    }


    /**
     *
     */
    public function truncateMatches()
    {
        $this->match->truncate();
    }


    public function generateScore(bool $is_home, int $teamRank)
    {
        //this generator is assuming home team and also current rank to generate result
        return $is_home ? rand(2, 8 - $teamRank) : rand(0, 8 - $teamRank);
    }

    public function resultSaver($match, $homeScore, $awayScore)
    {
        $match->home_team_goal = $homeScore;
        $match->away_team_goal = $awayScore;
        $match->status = 1;
        return $match->save();
    }

}
