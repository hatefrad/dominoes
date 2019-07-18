<?php
declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use App\Tiles;
use App\Player;

/**
 * Test Player.
 */
class TestPlayer extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->player = new Player('Alice', new Tiles);
    }

    public function testPlayerGetsSevenTiles()
    {
        $this->assertCount(7, $this->player->getTiles());
    }

    public function testPlayerName()
    {
        $this->assertEquals('Alice', $this->player->getName());
    }
}
