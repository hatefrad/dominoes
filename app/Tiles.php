<?php
declare(strict_types=1);

namespace App;

class Tiles
{
    /**
     * Minimum possible number on a tile.
     *
     * @var integer
     */
    const MIN_TILE_NUMBER = 0;

    /**
     * Maximum possible number on a tile.
     *
     * @var integer
     */
    const MAX_TILE_NUMBER = 6;

    /**
     * All of the available tiles.
     *
     * @var array
     */
    protected $tiles = [];

    public function __construct()
    {
        for ($i = static::MIN_TILE_NUMBER; $i <= static::MAX_TILE_NUMBER; $i++) {
            for ($j = $i; $j <= static::MAX_TILE_NUMBER; $j++) {
                $this->tiles[] = [$i, $j];
            }
        }

        shuffle($this->tiles);
    }

    public function getTiles(): array
    {
        return $this->tiles;
    }

    public function setTiles(array $tiles)
    {
        $this->tiles = $tiles;
    }

    public function drawATile()
    {
        return array_splice($this->tiles, 0, 1);
    }

    public function assign()
    {
        return array_splice($this->tiles, 0, 7);
    }

    public function tileName(array $tile)
    {
        return '<' . current($tile) . ':' . end($tile) . '>';
    }
}
