<?php

namespace SVRUnit\Components\Tests;

interface TestSuiteResultInterface
{

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return TestResultInterface[]
     */
    public function getResults(): array;

    /**
     * @return bool
     */
    public function hasErrors(): bool;

}
