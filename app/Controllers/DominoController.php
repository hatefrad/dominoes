<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Tiles;
use App\Game;
use App\Play;

class DominoController
{
    /**
     * The instance of Tiles.
     *
     * @var App\Tiles
     */
    protected $tiles;

    /**
     * Array of players.
     *
     * @var array
     */
    protected $players = [];

    /**
     * The instance of Game.
     *
     * @var App\Game
     */
    protected $game;

    /**
     * The instance of Play.
     * @var App\Play
     */
    protected $play;

    public function __construct(
        Tiles $tiles,
        array $players,
        Game $game,
        Play $play
    )
    {
        $this->tiles = $tiles;
        $this->players = $players;
        $this->game = $game;
        $this->play = $play;
        $this->play->kickStart();
    }

    public function play()
    {
        while (!$this->play->gameIsLockedUp($this->players)) {
            $this->play->playRound();

            if (count($this->play->getPlayer()->getTiles()) === 0) {
                break;
            }

            $this->play->switchTurns($this->players);
        }

        $this->game->getTheWinner();
    }
}
