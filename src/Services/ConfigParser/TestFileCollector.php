<?php

namespace SVRUnit\Services\ConfigParser;


class TestFileCollector
{

    /**
     * @param string $configDirectory
     * @param array<mixed> $folders
     * @return array<mixed>
     */
    public function searchTestFiles(string $configDirectory, array $folders): array
    {
        $testFiles = [];

        /** @var string $folder */
        foreach ($folders as $folder) {

            $fullDir = $configDirectory . '/' . $folder;

            if (!file_exists($fullDir)) {
                continue;
            }

            /** @var array<mixed> $files */
            $files = scandir($fullDir);

            /** @var string $file */
            foreach ($files as $file) {

                if ($file === '.' || $file === '..') {
                    continue;
                }

                /** @var array<mixed> $info */
                $info = pathinfo($file);

                if ((string)$info['extension'] !== 'yml') {
                    continue;
                }

                $testFiles[] = $fullDir . '/' . $file;
            }
        }

        return $testFiles;
    }

}
