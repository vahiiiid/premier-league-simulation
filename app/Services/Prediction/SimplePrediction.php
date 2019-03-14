<?php
/**
 * Created by PhpStorm.
 * User: vahid
 * Date: 3/14/19
 * Time: 9:01 PM
 */

namespace App\Services\Prediction;

use App\Repositories\MatchRepository;
use App\Repositories\StandingRepository;

class SimplePrediction implements PredictionInterface
{

    protected $standingRepository;
    protected $matchRepository;
    protected $predictions = [];

    public function __construct(StandingRepository $standingRepository, MatchRepository $matchRepository)
    {
        $this->standingRepository = $standingRepository;
        $this->matchRepository = $matchRepository;
    }

    public function getPrediction(): array
    {

        $finished = $this->standingRepository->getAll();
        $this->collectionPredictions($finished);

        $matches = $this->matchRepository->getAllMatches();
        $this->combinePredictions($matches);

        $collection = collect($this->predictions);
        $multiplied = $collection->map(function ($item) {
            if (! $sum = $this->sumPoints()) {
                return 0;
            }
            return round((($item['points'] / $sum) * 100), 2);
        });

        $combine = $multiplied->all();

        //reset keys after combine
        $values = $collection->values();

        $chart = [];
        foreach ($values->all() as $key => $val) {
            array_push($chart, [$val['name'], $combine[$val['team_id']]]);
        }

        return $chart;
    }

    private function combinePredictions($matches)
    {
        foreach ($matches as $match) {
            $home = $this->standingRepository->getStandingByTeamId($match->home_team);
            $away = $this->standingRepository->getStandingByTeamId($match->away_team);
            $homeScore = $this->matchRepository->generateScore(true, $home->id);
            $awayScore = $this->matchRepository->generateScore(false, $away->id);

            $points = $this->calculateScoreForChart($homeScore, $awayScore);
            if (isset($points['away_team'])) {
                foreach ($points['away_team'] as $key => $value) {
                    $this->predictions[$match->away_team][$key] += $points['away_team'][$key];
                }
            }
            if (isset($points['home_team'])) {
                foreach ($points['home_team'] as $key => $value) {
                    $this->predictions[$match->home_team][$key] += $points['home_team'][$key];
                }
            }
        }
    }

    private function collectionPredictions($data)
    {
        $collection = collect($data);
        $collection->each(function ($item) {
            $this->predictions[$item->team_id]['points'] = $item->points;
            $this->predictions[$item->team_id]['name'] = $item->name;
            $this->predictions[$item->team_id]['team_id'] = $item->team_id;
        });
    }

    public function calculateScoreForChart($homeScore, $awayScore)
    {
        $points = [];
        if ($homeScore > $awayScore) {
            $points['home']['points'] = 3;
        } elseif ($awayScore > $homeScore) {
            $points['away']['points'] = 3;
        } else {
            $points['home']['points'] = 1;
            $points['away']['points'] = 1;
        }
        return $points;
    }

    private function sumPoints()
    {
        return array_sum(array_map(function ($item) {
            return $item['points'];
        }, $this->predictions));
    }
}
