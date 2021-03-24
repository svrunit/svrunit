<?php


namespace SVRUnit\Components\Reports;


use SVRUnit\Components\Tests\TestSuiteResultInterface;

interface ReportInterface
{

    /**
     * @param TestSuiteResultInterface[] $suites
     */
    public function generate(array $suites): void;

}