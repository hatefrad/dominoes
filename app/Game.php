<?php
declare(strict_types=1);

namespace App;

use App\Tiles;
use App\Player;
use App\Outputter\OutputterInterface;

class Game
{
    /**
     * String for when the game is a draw.
     *
     * @var string
     */
    const NO_WINNER = 'draw';

    /**
     * String constants.
     *
     * @var array
     */
    const FINAL_RESULT_STRINGS = [
        'WINNER' => ' has won!',
        'DRAW' => 'It\'s a draw!',
    ];

    /**
     * Instance of Tiles.
     *
     * @var App\Tiles
     */
    protected $tiles;

   /**
     * The players in the game.
     *
     * @var array
     */
    protected $players = [];

    public function __construct(Tiles $tiles, array $players, OutputterInterface $outputter)
    {
        $this->tiles = $tiles;
        $this->players = $players;
        $this->outputter = $outputter;
    }

    public function getTheWinner(): string
    {
        if (empty($this->players[0]->getTiles())) {
            $this->outputter->logEvent(
                $this->players[0]->getName() . static::FINAL_RESULT_STRINGS['WINNER']
            );

            return $this->players[0]->getName();
        }

        if (empty($this->players[1]->getTiles())) {
            $this->outputter->logEvent(
                $this->players[1]->getName() . static::FINAL_RESULT_STRINGS['WINNER']
            );

            return $this->players[1]->getName();
        }

        $remainingTilessFirstPlayer = $this->sumOfRemainingTiles($this->players[0]->getTiles());
        $remainingTilessSecondPlayer = $this->sumOfRemainingTiles($this->players[1]->getTiles());

        if ($remainingTilessFirstPlayer === $remainingTilessSecondPlayer) {
            $this->outputter->logEvent(
                static::FINAL_RESULT_STRINGS['DRAW']
            );

            return static::NO_WINNER;
        }

        $winner = $remainingTilessFirstPlayer > $remainingTilessSecondPlayer
                ? $this->players[1]->getName()
                : $this->players[0]->getName();

        $this->outputter->logEvent(
            $winner . static::FINAL_RESULT_STRINGS['WINNER']
        );

        return $winner;
    }

    public function sumOfRemainingTiles(array $tiles): int
    {
        $sum = 0;

        foreach ($tiles as $tile) {
            $sum += array_sum($tile);
        }

        return $sum;
    }
}
