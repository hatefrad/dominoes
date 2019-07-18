<?php

namespace App\Outputter;

/**
 * @property string $steps
 */
interface OutputterInterface
{
    public function logEvent($step);

    public function output();
}
