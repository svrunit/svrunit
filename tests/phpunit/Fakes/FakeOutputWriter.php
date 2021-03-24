<?php

namespace SVRUnit\Tests\Fakes;

use SVRUnit\Services\OutputWriter\OutputWriterInterface;


class FakeOutputWriter implements OutputWriterInterface
{
    public function debug(string $text): void
    {
        // TODO: Implement debug() method.
    }

    public function info(string $text): void
    {
        // TODO: Implement info() method.
    }

    public function warning(string $text): void
    {
        // TODO: Implement warning() method.
    }

    public function section(string $text): void
    {
        // TODO: Implement section() method.
    }

    public function error(string $text): void
    {
        // TODO: Implement error() method.
    }

    public function success(string $text): void
    {
        // TODO: Implement success() method.
    }

}
