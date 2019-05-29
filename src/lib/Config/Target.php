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

    /** @var array */
    private $configDefinition = [
        self::NAME             => ['string', null],
        self::TYPE             => ['string', 'phar'],
        self::SOURCE_DIRECTORY => ['string', '.'],
        self::STUB_FILE        => ['string', null],
        self::SHEBANG          => ['string', '#!/usr/bin/env php'],
        self::COMPRESSION      => ['string', 'none', ['none', 'gz', 'bz2']],
        self::EXCLUDE_PHARIZER => ['boolean', true],
        self::FILTERS          => [Filters::class, null],
    ];

    /** @var string */
    private $pharizerExcludePattern = '\bvendor/fostam/pharizer';

    /** @var array */
    private $config = [];

    /**
     * Target constructor.
     * @param $data
     */
    public function __construct($data) {
        $this->config = Builder::build($this->configDefinition, $data);

        if ($this->config[self::EXCLUDE_PHARIZER]) {
            $filters = $this->config[self::FILTERS];
            /** @var Filters $filters */
            $filters->prependFilter(new Filter([Filter::TYPE_EXCLUDE => $this->pharizerExcludePattern]));
        }
    }

    /**
     * @return string
     */
    public function getName(): string {
        return $this->config[self::NAME];
    }

    /**
     * @return string
     */
    public function getType(): string {
        return $this->config[self::TYPE];
    }

    /**
     * @return string
     */
    public function getSourceDirectory(): string {
        return $this->config[self::SOURCE_DIRECTORY];
    }

    /**
     * @return string
     */
    public function getStub(): string {
        return $this->config[self::STUB_FILE];
    }

    /**
     * @return string
     */
    public function getShebang(): string {
        return $this->config[self::SHEBANG];
    }

    /**
     * @return string
     */
    public function getCompression(): string {
        return $this->config[self::COMPRESSION];
    }

    /**
     * @return bool
     */
    public function getExcludePharizer(): bool {
        return $this->config[self::EXCLUDE_PHARIZER];
    }

    /**
     * @return Filters
     */
    public function getFilters(): Filters {
        return $this->config[self::FILTERS];
    }
}