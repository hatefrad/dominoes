<?php
declare(strict_types=1);

namespace App\Outputter;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use App\Outputter\OutputterInterface;

class HtmlOutput implements OutputterInterface
{
    /**
     * Holds every step in the game.
     *
     * @var array
     */
    protected $steps = [];

    public function logEvent($step)
    {
        $this->steps[] = $step;
    }

    public function output()
    {
        $loader = new FilesystemLoader(__DIR__ . '/templates');
        $twig = new Environment($loader);

        echo $twig->render('Output.html', ['steps' => $this->steps]);
    }
}
