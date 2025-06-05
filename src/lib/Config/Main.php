<?php

namespace Pharizer\Config;

class Main {
    const BASE_DIRECTORY   = 'base-directory';
    const TARGET_DIRECTORY = 'target-directory';
    const TARGETS          = 'targets';

    private array $configDefinition = [
        self::BASE_DIRECTORY   => ['string', '.'],
        self::TARGET_DIRECTORY => ['string', '.'],
        self::TARGETS          => [Targets::class, []],
    ];

    private array $config;

    public function __construct(array $data) {
        $this->config = Builder::build($data[self::BASE_DIRECTORY], $this->configDefinition, $data);
        if (!str_starts_with($this->config[self::TARGET_DIRECTORY], '/')) {
            $this->config[self::TARGET_DIRECTORY] = $this->config[self::BASE_DIRECTORY] . '/' . $this->config[self::TARGET_DIRECTORY];
        }
    }

    public function getBaseDirectory(): string {
        return $this->config[self::BASE_DIRECTORY];
    }

    public function getTargetDirectory(): string {
        return $this->config[self::TARGET_DIRECTORY];
    }

    public function getTargets(): Targets {
        return $this->config[self::TARGETS];
    }
}