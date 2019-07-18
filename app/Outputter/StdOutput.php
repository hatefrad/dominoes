<?php
declare(strict_types=1);

namespace App\Outputter;

use App\Outputter\OutputterInterface;

class StdOutput implements OutputterInterface
{
    /**
     * Holds every step in the game.
     *
     * @var array
     */
    protected $step = [];

    public function logEvent($step)
    {
        $this->steps[] = $step;
    }

    public function output()
    {
        print_r($this->steps);
    }
}
