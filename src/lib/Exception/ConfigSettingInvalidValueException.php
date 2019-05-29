<?php

namespace Pharizer\Exception;

use InvalidArgumentException;

class ConfigSettingInvalidValueException extends InvalidArgumentException {
    public function __construct($key, $value) {
        parent::__construct("invalid value '{$value}' for key '{$key}'");
    }
}