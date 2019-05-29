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
    /** @var string */
    private $filename;
    /** @var string */
    private $filenameFinal;

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

        // prepare names
        $this->filename = $this->buildPharFilename($target->getName(), true);
        $this->filenameFinal = $this->buildPharFilename($target->getName(), false);

        $this->validateStub($target);
        $this->createDestinationPath($this->filename);
        $this->createPhar($target);
        $this->rename();
        $this->makeExecutable();
        $this->printResult();
    }

    /**
     * @param Target $target
     * @throws Exception
     */
    private function createPhar(Target $target): void {
        $iterator = FileIterator::create($target);
        $alias = $this->buildPharAlias($target->getName());
        $phar = new Phar($this->filename, 0, $alias);
        $phar->buildFromIterator($iterator, $target->getSourceDirectory());
        $stub = $phar->createDefaultStub($target->getStub());
        $stub = $target->getShebang() . "\n" . $stub;
        $phar->setStub($stub);
    }

    /**
     * @throws Exception
     */
    private function rename(): void {
        // rename to final name (without forced extension)
        if ($this->filenameFinal !== $this->filename) {
            if (!rename($this->filename, $this->filenameFinal)) {
                throw new Exception("can't rename '{$this->filename}' to '{$this->filenameFinal}'");
            }
        }
    }

    /**
     *
     */
    private function makeExecutable(): void {
        // ignore errors
        chmod($this->filenameFinal, 0755);
    }

    /**
     *
     */
    public function printResult(): void {
        if ($this->input->getOption('quiet')) {
            return;
        }

        $size = filesize($this->filenameFinal);
        $this->output->writeln("{$this->filenameFinal} built ({$size} bytes)");
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

    /**
     * @param Target $target
     * @throws Exception
     */
    private function validateStub(Target $target): void {
        $stubFile = $target->getSourceDirectory() . '/' . $target->getStub();
        if (!file_exists($stubFile)) {
            throw new Exception("stub file '{$stubFile}' does not exist");
        }
    }
}