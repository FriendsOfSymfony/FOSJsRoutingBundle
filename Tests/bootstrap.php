<?php

require_once __DIR__.'/../Fixtures/testProject/app/autoload.php';

$filesystem = new \Symfony\Component\HttpKernel\Util\Filesystem();
$filesystem->remove(__DIR__.'/../Fixtures/testProject/app/cache');
$filesystem->remove(__DIR__.'/../Fixtures/testProject/app/logs');
$filesystem->mkdir(__DIR__.'/../Fixtures/testProject/app/cache');
$filesystem->mkdir(__DIR__.'/../Fixtures/testProject/app/logs');
