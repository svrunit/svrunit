<?php


namespace SVRUnit\Components\Reports;

use SVRUnit\Components\Tests\Results\RunResult;

interface ReportInterface
{

    /**
     * @param RunResult $result
     */
    public function generate(RunResult $result): void;

}
