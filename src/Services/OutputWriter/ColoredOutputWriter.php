<?php


namespace SVRUnit\Services\OutputWriter;


class ColoredOutputWriter implements OutputWriterInterface
{


    /**
     * @param string $text
     */
    public function debug(string $text): void
    {
        echo "\033[39m$text \033[0m\n";
    }

    /**
     * @param string $text
     */
    public function info(string $text): void
    {
        echo "\033[97m$text \033[0m\n";
    }

    /**
     * @param string $text
     */
    public function section(string $text): void
    {
        echo "\033[36m$text \033[0m\n";
    }

    /**
     * @param string $text
     */
    public function warning(string $text): void
    {
        echo "\033[33m$text \033[0m\n";
    }

    /**
     * @param string $text
     */
    public function success(string $text): void
    {
        echo "\e[1;0m \e[42m " . $text . "\e[1;0m \e[0m\n";
    }

    /**
     * @param string $text
     */
    public function error(string $text): void
    {
        echo "\e[1;0m \e[41m " . $text . "\e[1;0m \e[0m\n";
    }

}
