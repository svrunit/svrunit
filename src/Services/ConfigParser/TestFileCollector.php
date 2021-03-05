<?php

namespace SVRUnit\Services\ConfigParser;


class TestFileCollector
{

    /**
     * @author Christian Dangl
     * @param $configDirectory
     * @param array $folders
     * @return array
     */
    public function searchTestFiles($configDirectory, array $folders)
    {
        $testFiles = array();

        /** @var string $folder */
        foreach ($folders as $folder) {

            $fullDir = $configDirectory . '/' . $folder;

            if (!file_exists($fullDir)) {
                continue;
            }

            $files = scandir($fullDir);

            foreach ($files as $file) {

                if ($file === '.' || $file === '..') {
                    continue;
                }

                $info = pathinfo($file);

                if ($info['extension'] !== 'yml') {
                    continue;
                }

                $testFiles[] = $fullDir . '/' . $file;
            }
        }

        return $testFiles;
    }
}