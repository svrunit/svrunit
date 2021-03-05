<?php

namespace SVRUnit\Components\Tests;

interface TestResultInterface
{

    /**
     * @return bool
     */
    public function isSuccess(): bool;

    /**
     * @return string
     */
    public function getOutput(): string;

    /**
     * @return TestInterface
     */
    public function getTest(): TestInterface;

    /**
     * @return string
     */
    public function getExpected(): string;

}
