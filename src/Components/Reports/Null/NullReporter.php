<?php

namespace SVRUnit\Components\Reports\Null;

use SVRUnit\Components\Reports\ReportInterface;

class NullReporter implements ReportInterface
{

    /**
     * @param array $suites
     */
    public function generate(array $suites): void
    {

    }

}
