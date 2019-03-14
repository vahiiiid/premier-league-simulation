<?php
/**
 * Created by PhpStorm.
 * User: vahid
 * Date: 3/14/19
 * Time: 9:56 PM
 */

namespace App\Services\Simulator;

use App\Repositories\MatchRepository;
use App\Repositories\StandingRepository;

class MatchSimulator implements ResultSimulatorInterface
{

    protected $standingRepository;
    protected $matchRepository;

    public function __construct(StandingRepository $standingRepository, MatchRepository $matchRepository)
    {
        $this->standingRepository = $standingRepository;
        $this->matchRepository = $matchRepository;
    }

    public function simulate($match)
    {
        $home = $this->standingRepository->getStandingByTeamId($match->home_team);
        $away = $this->standingRepository->getStandingByTeamId($match->away_team);
        dd($home);
        $homeScore = $this->matchRepository->generateScore(true, $home->id);
        $awayScore = $this->matchRepository->generateScore(false, $away->id);

        $this->updateMatchScore($homeScore, $awayScore, $home, $away);
        return $this->matchRepository->resultSaver($match, $homeScore, $awayScore);

    }

    public function bulkSimulate($matches)
    {
        foreach ($matches as $match) {
            $this->simulate($match);
        }
    }

    public function updateMatchScore($homeScore, $awayScore, $home, $away)
    {
        $this->matchRepository->updateMatchScore($homeScore, $awayScore, $home, $away);
    }

}
