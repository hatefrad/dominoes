<?php
declare(strict_types=1);

namespace App;

use App\Tiles;
use App\Player;
use App\Outputter\OutputterInterface;

class Play
{
    /**
     * The instance of Tiles.
     *
     * @var App\Tiles
     */
    protected $tiles;

    /**
     * The instance of Player.
     *
     * @var App\Player
     */
    protected $player;

    /**
     * The underlying ouputter implementation.
     *
     * @var App\Outputter\OutputterInterface
     */
    protected $outputter;

    /**
     * Current state of board.
     *
     * @var array
     */
    protected $bottomPile     = [];

    /**
     * Current ends of the board.
     *
     * @var array
     */
    protected $bottomPileEnds = [];

    public function __construct(
        Tiles $tiles,
        Player $player,
        OutputterInterface $outputter
    )
    {
        $this->tiles = $tiles;
        $this->player = $player;
        $this->outputter = $outputter;
    }

    public function kickStart()
    {
        $firstTile = current($this->tiles->drawATile());
        $this->logEvent('Game starting with first tile: ' . $this->tiles->tileName($firstTile));
        $this->setBottomPileEnds($firstTile);
        $this->bottomPile[]   = $firstTile;
    }

    public function playRound(): bool
    {
        if (count($this->player->getTiles()) == 0) {
            return false;
        }

        if ($this->move()) {
            return true;
        }

        while (!$this->playerIsLockedUp())
        {
            $this->draw();

            if ($this->move()) {
                return true;
            }
        }

        return false;
    }

    public function playerIsLockedUp(): bool
    {
        if (!empty($this->tiles->getTiles())) {
            return false;
        }

        foreach ($this->player->getTiles() as $tile) {
            if ($this->tileIsMatching($tile)) {
                return false;
            }
        }

        return true;
    }

    public function move(): bool
    {
        foreach ($this->player->getTiles() as $key => $tile) {
            if ($this->tileIsMatching($tile)) {
                $this->playTile($tile, $key);
                return true;
            }
        }

        return false;
    }

    public function draw()
    {
        if (!$this->canDraw()) {
            return;
        }

        $draw = $this->tiles->drawATile();

        $this->logEvent(
            $this->player->getName() . ' can\'t play. Drawing the tile ' . $this->tiles->tileName($draw[0])
        );

        $this->player->addTile($draw);
    }

    public function switchTurns(array $players)
    {
        $this->player =
            $this->player->getName() === $players[0]->getName()
            ? $players[1]
            : $players[0];
    }

    public function getPlayer(): Player
    {
        return $this->player;
    }

    public function canDraw(): bool
    {
        if (empty($this->tiles->getTiles())) {
            return false;
        }

        return true;
    }

    public function gameIsLockedUp(array $players): bool
    {
        if (!empty($this->tiles->getTiles())) {
            return false;
        }

        foreach($players as $player)
        {
            foreach ($player->getTiles() as $tile) {
                if ($this->tileIsMatching($tile)) {
                    return false;
                }
            }
        }

        return true;
    }

    public function tileIsMatching(array $tile): array
    {
        return array_intersect($tile, $this->bottomPileEnds);
    }

    public function playTile(array $tile, int $key)
    {
        $matchingTile = $this->getNeighboringTile($tile);
        $this->updateBottomPiles($tile);

        $this->logEvent($this->player->getName() . ' plays ' . $this->tiles->tileName($tile) .
            ' to connect to tile ' . $this->tiles->tileName($matchingTile) . ' on the board');

        $currentBoard = "";

        foreach ($this->bottomPile as $k => $item) {
            $currentBoard .= $this->tiles->tileName($item);
        }

        $this->logEvent('Board is now: ' . $currentBoard);
        $this->player->removeTile($key);
    }

    public function getBottomPile(): array
    {
        return $this->bottomPile;
    }

    public function setBottomPile(array $tile)
    {
        $this->bottomPile = $tile;
    }

    public function getBottomPileEnds(): array
    {
        return $this->bottomPileEnds;
    }

    public function setBottomPileEnds(array $tile)
    {
        $this->bottomPileEnds = $tile;
    }

    public function getNeighboringTile(array $tile): array
    {
        return
            in_array($this->bottomPileEnds[0], $tile)
            ? current($this->bottomPile)
            : end($this->bottomPile);
    }

    public function updateBottomPiles(array $tile)
    {
        if ($tile[1] == $this->bottomPileEnds[0]) {
            $bottomPile     = array_merge([$tile], $this->bottomPile);
            $bottomPileEnds = [$tile[0], $this->bottomPileEnds[1]];
        } elseif ($tile[1] == $this->bottomPileEnds[1]) {
            $bottomPile     = array_merge($this->bottomPile, [array_reverse($tile)]);
            $bottomPileEnds = [$this->bottomPileEnds[0], $tile[0]];
        } elseif ($tile[0] == $this->bottomPileEnds[0]) {
            $bottomPile     = array_merge([array_reverse($tile)], $this->bottomPile);
            $bottomPileEnds = [$tile[1], $this->bottomPileEnds[1]];
        } elseif ($tile[0] == $this->bottomPileEnds[1]) {
            $bottomPile     = array_merge($this->bottomPile, [$tile]);
            $bottomPileEnds = [$this->bottomPileEnds[0], $tile[1]];
        }

        $this->setBottomPile($bottomPile);
        $this->setBottomPileEnds($bottomPileEnds);
    }

    private function logEvent($message)
    {
        $this->outputter->logEvent($message);
    }
}
