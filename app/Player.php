<?php
declare(strict_types=1);

namespace App;

use App\Tiles;

class Player
{
    /**
     * Name of the player.
     *
     * @var string
     */
    protected $name;

    /**
     * Array of tiles player currently has.
     *
     * @var array
     */
    protected $tiles = [];

    public function __construct(string $name, Tiles $tiles)
    {
        $this->name = $name;
        $this->tiles = $tiles->assign();
    }

    public function getTiles(): array
    {
        return $this->tiles;
    }

    public function setTiles(array $tiles)
    {
        $this->tiles = $tiles;
    }

    public function getName()
    {
        return $this->name;
    }

    public function addTile(array $tile)
    {
        $this->tiles = array_merge($this->tiles, $tile);
    }

    public function removeTile(int $key)
    {
        unset($this->tiles[$key]);
    }
}
