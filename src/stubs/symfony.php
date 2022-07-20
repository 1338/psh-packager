<?php

require __DIR__.'/vendor/autoload.php';

use Symfony\Component\Console\Application;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

$application = new Application('{{PSH_NAME}}', '0.0.1-dev');

$fs = new Filesystem();

$finder = new Finder();

$finder->files()->in(__DIR__ . '/Command');

$toLoad = [];

foreach($finder as $file) {
  $toLoad[] = str_replace('/', '\\', str_replace('.php', '', $file->getRelativePathname()));

}

foreach ($toLoad as $class) {
  $className = "{{NAMESPACE}}Command\\$class";
  $application->add(new $className());
}


try {
    $application->run();
} catch (Exception $e) {
}