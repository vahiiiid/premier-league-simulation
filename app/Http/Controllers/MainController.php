<?php

namespace App\Http\Controllers;

use App\Repositories\MatchRepository;
use App\Repositories\StandingRepository;

class MainController extends Controller
{
    private $standingRepository;
    private $matchRepository;

    public function __construct(StandingRepository $standingRepository, MatchRepository $matchRepository)
    {
        $this->standingRepository = $standingRepository;
        $this->matchRepository = $matchRepository;
        $this->handleStanding();
    }

    public function handleStanding()
    {
        //check if there is no standing yet, make it
        if ($this->standingRepository->checkStanding()) {
            $this->standingRepository->createStanding();
        }
    }

    public function getStarting()
    {

        $this->matchRepository->createFixture();
        $fixture = $this->matchRepository->getFixture();
        $collection = collect($fixture);
        $matches = $collection->groupBy('week_id');

        return view(
            'landing',
            [
                'standing' => $this->standingRepository->getAll(),
                'weeks'    => $this->matchRepository->getWeeks(),
                'matches'  => $matches->toArray()
            ]);

    }

    public function resetAll()
    {
        $this->matchRepository->truncateMatches();
        $this->standingRepository->truncateStanding();
        $this->matchRepository->createFixture();
    }

    public function getStandings()
    {
        return response()->json($this->standingRepository->getAll());
    }

    public function getFixtures()
    {
        $weeks = $this->matchRepository->getWeeks();
        $fixture = $this->matchRepository->getFixture();
        $collection = collect($fixture);
        $grouped = $collection->groupBy('week_id');
        return response()->json(['weeks' => $weeks, 'items' => $grouped->toArray()]);
    }


}
