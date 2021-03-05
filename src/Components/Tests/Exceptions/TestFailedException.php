<?php

namespace SVRUnit\Components\Tests\Exceptions;

use Exception;
use SVRUnit\Components\Tests\TestInterface;
use Throwable;


class TestFailedException extends Exception
{

    /**
     * TestFailedException constructor.
     * @param TestInterface $test
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(TestInterface $test, $code = 0, Throwable $previous = null)
    {
        parent::__construct("Test failed: " . $test->getName(), $code, $previous);
    }

}
