<?php

namespace Pharizer\Config;

class Target {
    const NAME             = 'name';
    const TYPE             = 'type';
    const SOURCE_DIRECTORY = 'source-directory';
    const STUB_FILE        = 'stub-file';
    const SHEBANG          = 'shebang';
    const COMPRESSION      = 'compression';
    const EXCLUDE_PHARIZER = 'exclude-pharizer';
    const FILTERS          = 'filters';

    private array $configDefinition = [
        self::NAME             => ['string', null],
        self::TYPE             => ['string', 'phar'],
        self::SOURCE_DIRECTORY => ['string', '.'],
        self::STUB_FILE        => ['string', null],
        self::SHEBANG          => ['string', '#!/usr/bin/env php'],
        self::COMPRESSION      => ['string', 'none', ['none', 'gz', 'bz2']],
        self::EXCLUDE_PHARIZER => ['boolean', true],
        self::FILTERS          => [Filters::class, null],
    ];

    private string $pharizerExcludePattern = '\bvendor/(fostam|bin)/pharizer';
    private array $config;
    private string $baseDirectory;

    public function __construct(string $baseDirectory, array $data) {
        $this->config = Builder::build($baseDirectory, $this->configDefinition, $data);
        $this->baseDirectory = $baseDirectory;

        if ($this->config[self::EXCLUDE_PHARIZER]) {
            $filters = $this->config[self::FILTERS];
            /** @var Filters $filters */
            $filters->prependFilter(new Filter([Filter::TYPE_EXCLUDE => $this->pharizerExcludePattern]));
        }
    }

    public function getName(): string {
        return $this->config[self::NAME];
    }

    public function getType(): string {
        return $this->config[self::TYPE];
    }

    public function getSourceDirectory(): string {
        return $this->baseDirectory . '/' . $this->config[self::SOURCE_DIRECTORY];
    }

    public function getStub(): string {
        return $this->config[self::STUB_FILE];
    }

    public function getShebang(): string {
        return $this->config[self::SHEBANG];
    }

    public function getCompression(): string {
        return $this->config[self::COMPRESSION];
    }

    public function getExcludePharizer(): bool {
        return $this->config[self::EXCLUDE_PHARIZER];
    }

    public function getFilters(): Filters {
        return $this->config[self::FILTERS];
    }
}