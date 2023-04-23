<?php

declare(strict_types=1);

namespace App\Utils\Console;

use yii\helpers\Console;

class ProgressBar
{
    public function __construct(private int $current, private int $total, private bool $autoEnd = true)
    {
    }

    public function begin(): void
    {
        Console::startProgress($this->current, $this->total);
    }

    public function next(int $next = null): void
    {
        ++$this->current;

        if ($next !== null) {
            $this->current = $next;
        }

        if ($this->current > $this->total && $this->autoEnd === true) {
            $this->done();

            return;
        }

        Console::updateProgress($this->current, $this->total);
    }

    public function done(): void
    {
        Console::endProgress('Done!');
    }
}
