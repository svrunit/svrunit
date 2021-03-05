<?php

namespace SVRUnit\Services\OutputWriter;


interface OutputWriterInterface
{

    /**
     * @param $text
     * @return mixed
     */
    public function writeDebug($text);

    /**
     * @param $text
     * @return mixed
     */
    public function writeSuccess($text);

    /**
     * @param $text
     * @return mixed
     */
    public function writeError($text);

    /**
     * @param $text
     * @return mixed
     */
    public function writeInfo($text);

    /**
     * @param $text
     * @return mixed
     */
    public function writeWarning($text);

}