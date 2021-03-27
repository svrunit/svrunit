<?php

namespace SVRUnit\Components\Reports\Null;

use SVRUnit\Components\Reports\ReportInterface;
use SVRUnit\Components\Tests\Results\RunResult;

class NullReporter implements ReportInterface
{

    /**
     * @param RunResult $result
     */
    public function generate(RunResult $result): void
    {

    }

    /**
     *
     */
    public function clear(): void
    {
    }

}
