<?php

namespace Pharizer\Exception;

use InvalidArgumentException;

class ConfigSettingWrongTypeException extends InvalidArgumentException {
    public function __construct($key, $value) {
        parent::__construct("invalid value '{$value}' for key '{$key}'");
    }
}