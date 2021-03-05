<?php


namespace SVRUnit\Services\OutputWriter;


class ColoredOutputWriter implements OutputWriterInterface
{

    /**
     * @param $text
     * @return mixed|void
     */
    public function writeDebug($text)
    {
        echo $text . PHP_EOL;
    }

    /**
     * @param $text
     * @return mixed|void
     */
    public function writeSuccess($text)
    {
        echo "\e[1;0m \e[42m " . $text . "\e[1;0m \e[0m\n";
    }

    /**
     * @param $text
     * @return mixed
     */
    public function writeInfo($text)
    {
        echo "\e[1;0m \e[33m " . $text . "\e[1;0m \e[0m\n";
    }

    /**
     * @param $text
     * @return mixed|void
     */
    public function writeWarning($text)
    {
        echo "\e[1;0m \e[33m " . $text . "\e[1;0m \e[0m\n";
    }


    /**
     * @param $text
     * @return mixed|void
     */
    public function writeError($text)
    {
        echo "\e[1;0m \e[41m " . $text . "\e[1;0m \e[0m\n";
    }

}
