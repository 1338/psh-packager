#!/usr/bin/env php
<?php
if(isset($argv[1]) === false || isset($argv[2]) === false || isset($argv[3]) === false) {
    echo "Usage: <stub type> <psh name> <location>\n";
    exit(1);
}

list($file, $type, $pshName, $location) = $argv;

class builder
{
    /**
     * @var string
     */
    private $stubType;

    /**
     * @var string
     */
    private $pshName;

    /**
     * @var string
     */
    private $location;

    /**
     * @var string
     */
    private $pharFile;

    /**
     * @var Phar
     */
    private $p;

    /**
     * @param string $stubType
     * @param string $pshName
     * @param string $location
     */
    public function __construct($stubType, $pshName, $location)
    {
        $this->stubType = $stubType;
        $this->pshName = $pshName;
        $this->pharFile = $pshName.'.phar';
        $this->location = $location;
    }

    public function build() {
        $this->clean();
        $this->buildStub();
        $this->buildPhar();
    }

    private function buildPhar() {
        $this->p = new Phar($this->pharFile);
        $this->p->buildFromDirectory("$this->location/");
        $defaultStub = Phar::createDefaultStub("/$this->pshName", "/$this->pshName");
        $stub = "#!/usr/bin/env php \n".$defaultStub;
        $this->p->setStub($stub);
        chmod(__DIR__ . "/../$this->pharFile",0775);

        unlink("$this->location/$this->pshName");
        rename(__DIR__ . "/../$this->pharFile", $this->pshName);
        echo "$this->pshName successfully created";
    }

    private function clean() {
        if (file_exists($this->pharFile)) {
            unlink($this->pharFile);
        }
    }

    private function buildStub() {
        // pointing main file which requires all classes
        if($this->stubType === 'symfony') {
            $stubData = file_get_contents(__DIR__ . "/stubs/symfony.php");
            // Give template name of application
            $stubData = preg_replace("/\{\{PSH_NAME}}/", $this->pshName, $stubData);
            $autoLoad = $this->getProjectAutoLoad();
            $stubData = preg_replace("/\{\{NAMESPACE}}/", $autoLoad['namespace'], $stubData);
            $stubData = preg_replace("/\{\{NAMESPACEDIR}}/", $autoLoad['dir'], $stubData);
            file_put_contents("$this->location/$this->pshName", $stubData);
        } else {
            echo "Unknown stub type: $this->stubType\n";
            exit(1);
        }

    }

    private function getProjectAutoLoad() {
        $namespace = [
            'namespace' => '',
            'dir' => ''
        ];
        // Get PSR-4 namespace from from composer
        if(file_exists("$this->location/composer.json")) {
            $composer = json_decode(file_get_contents("$this->location/composer.json"), true);
            if(isset($composer['autoload']['psr-4']) && empty($composer['autoload']['psr-4']) === false) {
                reset($composer['autoload']['psr-4']);
                $autoLoadKey = key($composer['autoload']['psr-4']);
                $dir = $composer['autoload']['psr-4'][$autoLoadKey];
                $namespace = [
                        'namespace' => preg_replace('/\\\\/','\\\\\\\\', $autoLoadKey),
                        'dir' => $dir
                ];
            }
        }
        return $namespace;
    }
}

$builder = new builder($type, $pshName, $location);
$builder->build();
