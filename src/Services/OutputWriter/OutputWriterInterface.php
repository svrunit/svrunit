<?php

namespace SVRUnit\Services\OutputWriter;


interface OutputWriterInterface
{

    /**
     * @param string $text
     */
    public function debug(string $text): void;

    /**
     * @param string $text
     */
    public function info(string $text): void;

    /**
     * @param string $text
     */
    public function warning(string $text): void;

    /**
     * @param string $text
     */
    public function section(string $text): void;

    /**
     * @param string $text
     */
    public function error(string $text): void;

    /**
     * @param string $text
     */
    public function success(string $text): void;

}