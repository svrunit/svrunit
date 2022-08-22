<?php

namespace SVRUnit\Services\Stopwatch;

class Stopwatch
{

    /**
     * @var float
     */
    private $startTime;

    /**
     * @var float
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
        return (int)round(($this->endTime - $this->startTime) * 1000, 2);
    }

}
