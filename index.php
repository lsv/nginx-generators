<?php
require 'vendor/autoload.php';

use Lsv\NginxGenerator\Command\HtmlCommand;
use Lsv\NginxGenerator\Command\SymfonyCommand;
use Symfony\Component\Console\Application;

$app = new Application();
$app->add(new HtmlCommand());
$app->add(new SymfonyCommand());
$app->run();
