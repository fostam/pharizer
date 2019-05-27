<?php

namespace Pharizer\Config;

class Main {
    const TARGET_DIRECTORY = 'target-directory';
    const TARGETS          = 'targets';

    /** @var array */
    private $configDefinition = [
        self::TARGET_DIRECTORY => ['string', '.'],
        self::TARGETS          => [Targets::class, []],
    ];

    /** @var array */
    private $config = [];

    /**
     * Main constructor.
     * @param array $data
     */
    public function __construct(array $data) {
        $this->config = Builder::build($this->configDefinition, $data);
    }

    /**
     * @return string
     */
    public function getTargetDirectory(): string {
        return $this->config[self::TARGET_DIRECTORY];
    }

    /**
     * @return Targets
     */
    public function getTargets(): Targets {
        return $this->config[self::TARGETS];
    }
}