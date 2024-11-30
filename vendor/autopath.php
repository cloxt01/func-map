<?php

class Autoloader
{
    private string $configPath;
    private bool $debug = false;

    public function __construct(string $configPath)
    {
        $this->configPath = $configPath;
    }

    /**
     * @throws Exception
     */
    public function run(): void
    {
        $this->validateConfigFile();
        $config = $this->parseJsonConfig();

        if (empty($config['autoload']['func-map'])) {
            throw new Exception("Missing 'autoload.func-map' in configuration.");
        }

        foreach ($config['autoload']['func-map'] as $dir) {
            $this->loadDirectory($dir);
        }
    }

    private function validateConfigFile(): void
    {
        if (!file_exists($this->configPath)) {
            throw new Exception("Configuration file {$this->configPath} does not exist.");
        }
    }

    /**
     * @throws Exception
     */
    private function parseJsonConfig(): array
    {
        $jsonContent = file_get_contents($this->configPath);
        $config = json_decode($jsonContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Failed to parse JSON: " . json_last_error_msg());
        }

        return $config;
    }



    private function loadDirectory(string $relativeDir): void
    {
        // Start from the base directory
        $baseDir = dirname($this->configPath);

        // Construct the full directory path
        $fullDir = realpath($baseDir . DIRECTORY_SEPARATOR . $relativeDir);

        if (!$fullDir || !is_dir($fullDir)) {
            throw new Exception("Directory '{$relativeDir}' does not exist or is not a valid directory.");
        }

        $files = scandir($fullDir);
        if ($files === false) {
            throw new Exception("Unable to read directory: {$fullDir}");
        }

        foreach ($files as $file) {
            $filePath = $fullDir . DIRECTORY_SEPARATOR . $file;
            if ($this->isPhpFile($filePath)) {
                $this->autoload($filePath);
            }
        }
        require_once 'autoload.php';
    }
    private function isPhpFile(string $filePath): bool
    {
        return is_file($filePath) && strtolower(pathinfo($filePath, PATHINFO_EXTENSION)) === 'php';
    }

    private function autoload(string $filePath): void
    {
        if ($this->debug) {
            echo "Autoloading " . realpath($filePath) . " ..." . PHP_EOL;
        }
        require_once $filePath;
    }
}

$configPath = __DIR__ . '/../../../../composer.json';
$key = 'func-map';

$configName = basename($configPath);
if (!file_exists($configPath)) {
    echo "File $configName not found at " . $configPath . PHP_EOL;
    exit(255);
}
$configJson = file_get_contents($configPath);
$configArr = json_decode($configJson, true);

if (!isset($configArr['autoload'][$key])) {
    $configArr['autoload'][$key] = [];
    file_put_contents($configPath, json_encode($configArr, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    echo "$key does not exist in autoload, will be set to empty" . PHP_EOL;
}

try {
    $autoloader = new Autoloader($configPath);
    $autoloader->run();
} catch (Exception $e) {
    echo "Autoload error: " . $e->getMessage() . PHP_EOL;
    exit(255);
}