<?php

namespace SVRUnit\Services\Stopwatch;

class Stopwatch
{

    /**
     * @var int
     */
    private $startTime;

    /**
     * @var int
     */
    private $endTime;


    /**
     *
     */
    public function start(): void
    {
        $this->startTime = microtime(true);
    }

    /**
     *
     */
    public function stop(): void
    {
        $this->endTime = microtime(true);
    }

    /**
     * @return int
     */
    public function getMilliseconds(): int
    {
        return round(($this->endTime - $this->startTime) * 1000, 2);
    }

}
