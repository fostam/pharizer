<?php

namespace Pharizer\Config;

use Exception;
use Symfony\Component\Yaml\Yaml;

class Loader {
    private static string $defaultConfigFile = 'pharizer.yaml';

    /**
     * @throws Exception
     */
    public static function fromFile(?string $filename): Main {
        if (empty($filename)) {
            $filename = self::$defaultConfigFile;
        }

        if (!preg_match('#^/#', $filename)) {
            $filename = getcwd() . DIRECTORY_SEPARATOR . $filename;
        }

        if (!file_exists($filename)) {
            throw new Exception("config file '{$filename}' does not exist");
        }

        $yaml = file_get_contents($filename);
        if ($yaml === false) {
            throw new Exception("can't read config file '{$filename}'");
        }

        $configData = Yaml::parse($yaml);
        if ($configData === false) {
            throw new Exception("error parsing config YAML");
        }

        $configData[Main::BASE_DIRECTORY] = dirname($filename);

        return new Main($configData);
    }
}