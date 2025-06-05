<?php

namespace Pharizer\Builder;

use Phar;
use Pharizer\Config\Main;
use Pharizer\Config\Target;
use Exception;
use SplFileInfo;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Builder {
    private InputInterface $input;
    private OutputInterface $output;
    private Main $config;
    private string $filename;
    private string $filenameFinal;

    public function __construct(InputInterface $input, OutputInterface $output, Main $config) {
        $this->input = $input;
        $this->output = $output;
        $this->config = $config;
    }

    /**
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
        $this->printResult($target);
    }

    /**
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
     * @param Target $target
     */
    public function printResult(Target $target): void {
        if ($this->input->getOption('quiet')) {
            return;
        }

        $size = filesize($this->filenameFinal);
        $this->output->writeln("{$target->getName()} built ({$size} bytes)");
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

    private function buildPharFilename(string $name, bool $forceExtension): string {
        if ($forceExtension && !preg_match('#\.phar$#', $name)) {
            $name .= '.phar';
        }

        return $this->config->getTargetDirectory() . '/' . $name;
    }

    private function buildPharAlias(string $name): string {
        return basename($name);
    }

    /**
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
     * @throws Exception
     */
    private function validateStub(Target $target): void {
        $stubFile = realpath($target->getSourceDirectory() . '/' . $target->getStub());
        if (!file_exists($stubFile)) {
            throw new Exception("stub file '{$stubFile}' does not exist");
        }

        $iterator = FileIterator::create($target);
        foreach ($iterator as $file) {
            /** @var SplFileInfo $file */
            $filename = realpath($file->getPathname());
            if ($filename === $stubFile) {
                return;
            }
        }

        throw new Exception("stub file '{$target->getStub()}' not in file list");
    }
}