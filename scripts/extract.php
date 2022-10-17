<?php

$phar = new Phar(__DIR__ . "/../build/svrunit.phar");

$phar->extractTo(__DIR__ . "/../build/content");
