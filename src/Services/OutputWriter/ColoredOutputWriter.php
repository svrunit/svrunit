<?php


namespace SVRUnit\Services\OutputWriter;


class ColoredOutputWriter implements OutputWriterInterface
{

    /**
     * @var bool
     */
    private $colors;


    /**
     *
     */
    public function __construct()
    {
        # no colors when piped into files or CI logs that do not
        # support it, or if the user opted out (https://no-color.org)
        $this->colors = getenv('NO_COLOR') === false && stream_isatty(STDOUT);
    }


    /**
     * @param string $text
     */
    public function debug(string $text): void
    {
        $this->write("\033[90m", $text);
    }

    /**
     * @param string $text
     */
    public function info(string $text): void
    {
        $this->write("\033[0m", $text);
    }

    /**
     * @param string $text
     */
    public function section(string $text): void
    {
        $this->write("\033[1;36m", $text);
    }

    /**
     * @param string $text
     */
    public function warning(string $text): void
    {
        $this->write("\033[33m", $text);
    }

    /**
     * @param string $text
     */
    public function success(string $text): void
    {
        $this->write("\033[32m", $text);
    }

    /**
     * @param string $text
     */
    public function error(string $text): void
    {
        $this->write("\033[31m", $text);
    }

    /**
     * @param string $color
     * @param string $text
     */
    private function write(string $color, string $text): void
    {
        if ($this->colors) {
            $text = $color . $text . "\033[0m";
        }

        echo $text . PHP_EOL;
    }

}
