<?php
declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use App\Tiles;
use App\Player;

/**
 * Test Tiles..
 */
class TestTiles extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->tiles = new Tiles;
    }

    public function testNumberOfTiles()
    {
        $this->assertCount(
            28,
            $this->tiles->getTiles()
        );
    }

    public function testTilesExist()
    {
        $this->assertTrue(in_array([0,0], $this->tiles->getTiles()));
        $this->assertTrue(in_array([2,4], $this->tiles->getTiles()));
        $this->assertTrue(in_array([6,6], $this->tiles->getTiles()));
    }

    public function testTilesDontExist()
    {
        $this->assertFalse(in_array([4,1], $this->tiles->getTiles()));
        $this->assertFalse(in_array([1,0], $this->tiles->getTiles()));
        $this->assertFalse(in_array([1,7], $this->tiles->getTiles()));
    }

    public function testRemaining()
    {
        new Player('Alice', $this->tiles);

        $this->assertCount(
            21,
            $this->tiles->getTiles()
        );
    }

    public function testTileName()
    {
        $result = $this->tiles->tileName([2,5]);

        $this->assertEquals('<2:5>', $result);
    }
}
