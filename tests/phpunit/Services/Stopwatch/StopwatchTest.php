<?php

namespace SVRUnit\Tests\Services\Stopwatch;

use PHPUnit\Framework\TestCase;
use SVRUnit\Components\Tests\Results\TestResult;
use SVRUnit\Services\Stopwatch\Stopwatch;
use SVRUnit\Tests\Fakes\FakeTest;

class StopwatchTest extends TestCase
{

    /**
     * This test verifies that our stopwatch
     * does really calculate the approx. correct
     * amount of milliseconds.
     * because the correct time cannot be measured in milliseconds
     * we just verify that we are in a range that is likely to happen
     */
    public function testStopwatch()
    {
        $stopwatch = new Stopwatch();

        $stopwatch->start();

        # wait for 0.5 seconds
        usleep(500000);

        $stopwatch->stop();

        $ms = $stopwatch->getMilliseconds();

        $this->assertEquals(true, ($ms >= 500));
        $this->assertEquals(true, ($ms < 600));
    }

}
