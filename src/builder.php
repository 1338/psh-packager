#!/usr/bin/env php
<?php
if(isset($argv[1]) === false || isset($argv[2]) === false || isset($argv[3]) === false) {
    echo "Usage: <stub type> <psh name> <location>\n";
    exit(1);
}

list($file, $type, $pshName, $location) = $argv;

// The php.ini setting phar.readonly must be set to 0
$pharFile = $pshName.'.phar';

// clean up
if (file_exists($pharFile)) {
    unlink($pharFile);
}

// create phar
$p = new Phar($pharFile);

// pointing main file which requires all classes
if($type === 'symfony') {
    $stubData = file_get_contents(__DIR__ . "/stubs/symfony.php");
    // Give template name of application
    $stubData = preg_replace("/\{\{PSH_NAME}}/", $pshName, $stubData);
    file_put_contents("$location/$pshName", $stubData);
} else {
    echo "Unknown stub type: $type\n";
    exit(1);
}
// creating our library using whole directory  
$p->buildFromDirectory("$location/");


$defaultStub = $p::createDefaultStub("/$pshName", "/$pshName");

// Create a custom stub to add the shebang
$stub = "#!/usr/bin/env php \n".$defaultStub;

// Add the stub
$p->setStub($stub);

chmod(__DIR__ . "/../$pharFile",0775);

unlink("$location/$pshName");
rename(__DIR__ . "/../$pharFile", $pshName);
echo "$pshName successfully created";
