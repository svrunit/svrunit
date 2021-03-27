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
        $this->startTime = (int)microtime(true);
    }

    /**
     *
     */
    public function stop(): void
    {
        $this->endTime = (int)microtime(true);
    }

    /**
     * @return int
     */
    public function getMilliseconds(): int
    {
        return (int)round(($this->endTime - $this->startTime) * 1000, 2);
    }

}
