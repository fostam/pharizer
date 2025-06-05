<?php

namespace Pharizer\Config;

use Pharizer\Exception\ConfigSettingInvalidValueException;
use Pharizer\Exception\ConfigSettingWrongTypeException;

class Filter {
    const TYPE_INCLUDE = 'include';
    const TYPE_EXCLUDE = 'exclude';

    private ?string $type = null;
    private ?string $pattern = null;
    private string $delimiter = '#';

    public function __construct(array $data) {
        foreach($data as $key => $value) {
            if (!is_null($this->type)) {
                throw new ConfigSettingWrongTypeException($key, $value);
            }

            if (!in_array($key, [self::TYPE_EXCLUDE, self::TYPE_INCLUDE])) {
                throw new ConfigSettingWrongTypeException($key, $value);
            }

            $pattern = $this->delimiter . str_replace($this->delimiter, '\\' . $this->delimiter, $value) . $this->delimiter;
            @preg_match($pattern, 'dummy');
            if(preg_last_error() !== PREG_NO_ERROR) {
                throw new ConfigSettingInvalidValueException($key, $value);
            }

            $this->type = $key;
            $this->pattern = $pattern;
        }
    }

    public function getType(): string {
        return $this->type;
    }

    public function matches(string $str): bool {
        return boolval(preg_match($this->pattern, $str));
    }
}