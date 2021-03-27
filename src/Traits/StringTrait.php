<?php

namespace SVRUnit\Traits;

trait StringTrait
{

    /**
     * @param string $expected
     * @param string $text
     * @return bool
     */
    protected function stringContains(string $expected, string $text): bool
    {
        if (strpos($text, $expected) !== false) {
            return true;
        }
        return false;
    }

}