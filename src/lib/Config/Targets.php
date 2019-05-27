<?php

namespace Pharizer\Config;

use Exception;

class Targets {
    /** @var array Target */
    private $targets = [];

    /**
     * Targets constructor.
     * @param $data
     */
    public function __construct($data) {
        foreach($data as $target => $targetData) {
            $targetData[Target::NAME] = $target;
            $this->targets[$target] = new Target($targetData);
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