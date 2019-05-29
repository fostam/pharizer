<?php

namespace Pharizer\Builder;

use Phar;
use Pharizer\Config\Main;
use Pharizer\Config\Target;
use Exception;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Builder {
    /** @var InputInterface */
    private $input;
    /** @var OutputInterface */
    private $output;
    /** @var Main */
    private $config;

    /**
     * Builder constructor.
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param Main $config
     */
    public function __construct(InputInterface $input, OutputInterface $output, Main $config) {
        $this->input = $input;
        $this->output = $output;
        $this->config = $config;
    }

    /**
     * @param Target $target
     * @throws Exception
     */
    public function build(Target $target): void {
        self::checkSettings();
        $this->createPhar($target);
    }

    /**
     * @param Target $target
     * @throws Exception
     */
    private function createPhar(Target $target): void {
        $iterator = FileIterator::create($target);

        // prepare names
        $filename = $this->buildPharFilename($target->getName(), true);
        $alias = $this->buildPharAlias($target->getName());

        // create destination directory
        $this->createDestinationPath($filename);

        // build phar
        $phar = new Phar($filename, 0, $alias);
        $phar->buildFromIterator($iterator, $target->getSourceDirectory());
        $stub = $phar->createDefaultStub($target->getStub());
        $stub = $target->getShebang() . "\n" . $stub;
        $phar->setStub($stub);

        // rename to final name (without forced extension)
        $filenameFinal = $this->buildPharFilename($target->getName(), false);
        if ($filenameFinal !== $filename) {
            if (!rename($filename, $filenameFinal)) {
                throw new Exception("can't rename '{$filename}' to '{$filenameFinal}'");
            }
        }
    }

    /**
     * @throws Exception
     */
    public static function checkSettings(): void {
        $pharReadonly = boolval(ini_get('phar.readonly'));
        if ($pharReadonly === true) {
            throw new Exception("phar creation has been disabled. Change 'phar.readonly' to 'false' in " . php_ini_loaded_file());
        }
    }

    /**
     * @param string $name
     * @param bool $forceExtension
     * @return string
     */
    private function buildPharFilename(string $name, bool $forceExtension): string {
        if ($forceExtension && !preg_match('#\.phar$#', $name)) {
            $name .= '.phar';
        }

        return $this->config->getTargetDirectory() . '/' . $name;
    }

    /**
     * @param string $name
     * @return string
     */
    private function buildPharAlias(string $name): string {
        return basename($name);
    }

    /**
     * @param string $filename
     * @throws Exception
     */
    private function createDestinationPath(string $filename): void {
        $path = dirname($filename);

        if (is_dir($path)) {
            return;
        }

        if (!mkdir($path, 0777, true)) {
            throw new Exception("can't create destination directory '{$path}'");
        }
    }
}