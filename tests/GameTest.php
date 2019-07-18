<?php
declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use App\Game;
use App\Tiles;
use App\Player;
use App\Outputter\StdOutput;

/**
 * Test Game.
 */
class TestGame extends TestCase
{
    const PLAYER_0 = 'Alice';
    const PLAYER_1 = 'Bob';

    protected function setUp(): void
    {
        parent::setUp();
        $tiles = new Tiles();
        $outputter = new StdOutput;
        $this->players[] = new Player(static::PLAYER_0, $tiles);
        $this->players[] = new Player(static::PLAYER_1, $tiles);
        $this->game = new Game($tiles, $this->players, $outputter);
    }

    public function testGetTheWinnerWhenOnePlayerRunsOutOfTiles()
    {
        $this->players[0]->setTiles([]);

        $this->assertEquals(
            static::PLAYER_0,
            $this->game->getTheWinner()
        );
    }

    public function testGetTheWinnerWhenGameIsLockedUp()
    {
        $this->players[0]->setTiles([[4,5]]);
        $this->players[1]->setTiles([[1,1],[1,2]]);

        $this->assertEquals(
            static::PLAYER_1,
            $this->game->getTheWinner()
        );
    }

    public function testGameIsADraw()
    {
        $this->players[0]->setTiles([[4,5],[1,2],[1,4]]);
        $this->players[1]->setTiles([[6,6],[0,0],[2,3]]);

        $this->assertEquals(
            $this->game::NO_WINNER,
            $this->game->getTheWinner()
        );
    }

    public function testSomeOfRemainingTiles()
    {
        $tiles1 = [
            [0,0],
        ];

        $tiles2 = [
            [6,6],
            [3,4],
            [2,1],
            [5,5],
        ];

        $this->assertEquals(
            0,
            $this->game->sumOfRemainingTiles($tiles1)
        );

        $this->assertEquals(
            32,
            $this->game->sumOfRemainingTiles($tiles2)
        );

    }
}
