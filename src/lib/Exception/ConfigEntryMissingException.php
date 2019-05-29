<?php

namespace Pharizer\Exception;

use InvalidArgumentException;

class ConfigEntryMissingException extends InvalidArgumentException {
    public function __construct($key) {
        parent::__construct("invalid key '{$key}'");
    }
}