<?php
/**
 * Created by PhpStorm.
 * User: vahid
 * Date: 3/15/19
 * Time: 8:33 PM
 */

namespace App\Services\FixtureDraw;


class HomeAndAwayDraw implements FixtureDrawInterface
{

    protected $teams;

    public function __construct(array $teams)
    {
        $this->teams = $teams;
    }

    public function getFixturesPlan()
    {
        $numberOfWeeks = $this->getNumberOfWeeks(count($this->teams));
        $numberOfWeeklyFixtures = $this->getNumberOfWeeklyFixtures(count($this->teams));
        $allFixtures = $this->makeAllFixtures();
        $weeklyFixtures = $this->makeWeeklyFixtures($numberOfWeeks, $numberOfWeeklyFixtures, $allFixtures);
        return $weeklyFixtures;

    }

    public function makeWeeklyFixtures(int $numberOfWeeks, int $numberOfWeeklyFixtures, array $allFixtures)
    {

        $generatedWeeklyFixtures = [];
        for ($i = 1; $i <= $numberOfWeeks; $i++) {
            for ($j = 1; $j <= $numberOfWeeklyFixtures; $j++) {
                foreach ($allFixtures as $fixture) {

                    if (count($generatedWeeklyFixtures) === 0) {
                        $generatedWeeklyFixtures[] = [
                            'home'   => $fixture['home'],
                            'away'   => $fixture['away'],
                            'week'   => $i,
                            'status' => 0
                        ];
                        continue;
                    }
                    foreach ($generatedWeeklyFixtures as $f) {
                        if (($f['home'] === $fixture['home'] || $f['away'] === $fixture['away']) && $f['week'] === $i) {
                            dd('injaaaaaaaa');
                            continue;
                        }

                        $generatedWeeklyFixtures[] = [
                            'home'   => $fixture['home'],
                            'away'   => $fixture['away'],
                            'week'   => $i,
                            'status' => 0
                        ];
                        dd('iaalaanaaaaa', $generatedWeeklyFixtures);
                    }
                }
            }
        }

        dd($generatedWeeklyFixtures);

    }

    public function getNumberOfWeeks($teamCount)
    {
        if ($teamCount % 2 == 0) {
            return ($teamCount - 1) * 2;
        }

        return ($teamCount - 1) / 2;

    }

    public function getNumberOfWeeklyFixtures($teamCount)
    {
        return $teamCount / 2;
    }


    public function makeAllFixtures()
    {
        $fixtures = [];
        foreach ($this->teams as $homeTeam) {
            foreach ($this->teams as $awayTeam) {
                if ($homeTeam === $awayTeam) {
                    continue;
                }
                $fixtures[] = [
                    'home' => $homeTeam,
                    'away' => $awayTeam
                ];
            }
        }
        return $fixtures;

    }

}
