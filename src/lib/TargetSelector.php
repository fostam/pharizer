<?php

namespace Pharizer;

use Exception;
use Pharizer\Config\Main;

class TargetSelector {
    /**
     * @param Main $config
     * @param array $targetNames
     * @return array
     * @throws Exception
     */
    public static function select(Main $config, array $targetNames = []): array {
        $targets = $config->getTargets();
        if (empty($targetNames)) {
            $targetList = $targets->get();
        }
        else {
            $targetList = [];
            foreach($targetNames as $target) {
                $targetList[] = $targets->getTarget($target);
            }
        }

        return $targetList;
    }
}