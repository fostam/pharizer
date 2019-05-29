<?php

namespace Pharizer\Config;

use Exception;

class Targets {
    /** @var array Target */
    private $targets = [];

    /**
     * Targets constructor.
     * @param string $baseDirectory
     * @param array $data
     */
    public function __construct(string $baseDirectory, array $data) {
        foreach($data as $target => $targetData) {
            $targetData[Target::NAME] = $target;
            $this->targets[$target] = new Target($baseDirectory, $targetData);
        }
    }

    /**
     * @return Target[]
     */
    public function get(): array {
        return $this->targets;
    }

    /**
     * @param $target
     * @return Target
     * @throws Exception
     */
    public function getTarget($target): Target {
        if (!isset($this->targets[$target])) {
            throw new Exception("target {$target} does not exist");
        }
        return $this->targets[$target];
    }
}