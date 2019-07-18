<?php
declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use App\Play;
use App\Tiles;
use App\Player;
use App\Outputter\StdOutput;

/**
 * Test Play.
 */
class TestPlay extends TestCase
{
    const PLAYER_1 = 'Alice';
    const PLAYER_2 = 'Bob';

    protected function setUp(): void
    {
        parent::setUp();
        $this->tiles = new Tiles;
        $outputter = new StdOutput;
        $this->player = new Player(static::PLAYER_1, $this->tiles);
        $this->play = new Play($this->tiles, $this->player, $outputter);
    }

    public function testKickStart()
    {
        $currentBottomPile = current($this->tiles->getTiles());

        $this->assertCount(
            0,
            $this->play->getBottomPile()
        );

        $this->play->kickStart();

        $this->assertCount(
            1,
            $this->play->getBottomPile()
        );

        $this->assertEqualsCanonicalizing(
            $currentBottomPile,
            current($this->play->getBottomPile())
        );
    }

    public function testPlayerCantPlayARound()
    {
        $this->play->kickStart();
        $this->player->setTiles([]);

        $this->assertFalse($this->play->playRound());
    }

    public function testPlayerPlaysARound()
    {
        $this->play->kickStart();

        $this->assertTrue($this->play->playRound());
    }

    public function testPlayerIsNotLockedUp()
    {
        $this->assertFalse($this->play->playerIsLockedUp());
    }

    public function testPlayerIsLockedUp()
    {
        $this->play->setBottomPileEnds([0, 6]);
        $this->player->setTiles([[1,2]]);
        $this->tiles->setTiles([]);

        $this->assertTrue($this->play->playerIsLockedUp());
    }

    public function testGameIsNotLockedUp()
    {
        $this->assertFalse(
            $this->play->gameIsLockedUp([])
        );
    }

    /**
     * [testGameIsLockedUp:
     *  there are no more tiles and players tiles don't match
     *  with the pile ends.]
     */
    public function testGameIsLockedUp()
    {
        $player2 = new Player(static::PLAYER_2, $this->tiles);
        $player2->setTiles([[1,2]]);
        $this->tiles->setTiles([]);
        $this->play->setBottomPileEnds([0, 6]);
        $this->player->setTiles([[2,3]]);

        $this->assertTrue(
            $this->play->gameIsLockedUp(
                [$this->player, $player2]
            )
        );
    }

    public function testGetNeighboringTile()
    {
        $this->play->setBottomPile([[0, 6],[0, 5],[2, 3]]);
        $this->play->setBottomPileEnds([0, 3]);

        $this->assertEquals(
            [2,3],
            $this->play->getNeighboringTile([3,6])
        );
    }

    public function testSwitchTurns()
    {
        $player2 = new Player(static::PLAYER_2, $this->tiles);
        $currentPlayer = $this->play->getPlayer();
        $this->play->switchTurns([$this->player, $player2]);
        $newPlayer = $this->play->getPlayer();

        $this->assertTrue($currentPlayer->getName() !== $newPlayer->getName());
    }

    public function testBottomPilesGetsUpdated()
    {
        $this->play->setBottomPile([[0, 6],[0, 5],[2, 3]]);
        $this->play->setBottomPileEnds([0, 3]);
        $this->play->updateBottomPiles([3,4]);

        $this->assertEqualsCanonicalizing(
            [[0, 6],[0, 5],[2, 3],[3, 4]],
            $this->play->getBottomPile()
        );

        $this->assertEqualsCanonicalizing(
            [0,4],
            $this->play->getBottomPileEnds()
        );
    }

    public function testPlayerDrawsATile()
    {
        $currentTiles = count($this->player->getTiles());
        $this->play->draw();
        $newTiles = count($this->player->getTiles());

        $this->assertTrue($newTiles === $currentTiles+1);
    }

    public function testPlayerCantDrawATile()
    {
        $this->tiles->setTiles([]);
        $currentTiles = count($this->player->getTiles());
        $this->play->draw();
        $newTiles = count($this->player->getTiles());

        $this->assertTrue($newTiles === $currentTiles);
    }

    public function testPlayerCanMove()
    {
        $this->play->setBottomPile([[0,6]]);
        $this->play->setBottomPileEnds([0, 6]);
        $this->player->setTiles([[2,6]]);

        $this->assertTrue($this->play->move());
    }

    public function testPlayerCantMove()
    {
        $this->play->setBottomPileEnds([0, 6]);
        $this->player->setTiles([[1,2]]);

        $this->assertFalse($this->play->move());
    }

    public function testPlayerPlays()
    {
        $this->play->kickStart();
        $bottomPile = current($this->play->getBottomPile());

        // make sure player has a matching tile
        $matchingTile = ($bottomPile[0] !== 0) ? [0, $bottomPile[0]] : [0, 1];

        // now player has 8 tiles in total
        $this->player->addTile([$matchingTile]);

        // let's use that tile to play
        $this->play->playTile($matchingTile, 7);

        // now pile has 2 tiles, and player has 7
        $this->assertCount(2, $this->play->getBottomPile());;
        $this->assertCount(7, $this->player->getTiles());
    }

    public function testTileIsMatching()
    {
        $this->play->setBottomPileEnds([0, 6]);
        $result = $this->play->tileIsMatching([6, 1]);

        $this->assertEquals([6], $result);
    }

    public function testTileIsNotMatching()
    {
        $this->play->setBottomPileEnds([0, 6]);
        $result = $this->play->tileIsMatching([2, 1]);

        $this->assertEmpty($result);
    }
}
