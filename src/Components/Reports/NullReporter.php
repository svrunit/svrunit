<?php

namespace SVRUnit\Components\Reports;

class NullReporter implements ReportInterface
{

    /**
     * @param array $suites
     */
    public function generate(array $suites): void
    {

    }

}
