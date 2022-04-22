#!/usr/bin/env php
<?php
if(isset($argv[1]) === false || isset($argv[2]) === false) {
    echo "Usage: <stub type> <psh name>\n";
    exit(1);
}

$type = $argv[1];
$pshName = $argv[2];


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
    copy("stubs/symfony.php", "project/" . $pshName);
} else {
    echo "Unknown stub type: $type\n";
    exit(1);
}

// creating our library using whole directory  
$p->buildFromDirectory('project/');


$defaultStub = $p->createDefaultStub("/$pshName", "/$pshName");

// Create a custom stub to add the shebang
$stub = "#!/usr/bin/env php \n".$defaultStub;

// Add the stub
$p->setStub($stub);

chmod(__DIR__ . "/$pharFile",0775);

unlink("project/$pshName");
rename("$pharFile", $pshName);
echo "$pshName successfully created";
