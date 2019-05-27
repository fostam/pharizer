<?php

namespace Pharizer\Config;

use Pharizer\Exception\ConfigEntryMissingException;
use Pharizer\Exception\ConfigSettingInvalidValueException;
use Pharizer\Exception\ConfigSettingWrongTypeException;

class Builder {
    /**
     * @param array $configDefinition
     * @param array $data
     * @return array
     * @throws ConfigEntryMissingException
     * @throws ConfigSettingWrongTypeException
     * @throws ConfigSettingInvalidValueException
     */
    public static function build(array $configDefinition, array $data): array {
        $config = [];
        foreach($configDefinition as $key => $definition) {
            if (!isset($data[$key])) {
                if (is_null($definition[1])) {
                    throw new ConfigEntryMissingException($key);
                }
                else {
                    $value = $definition[1];
                }
            }
            else {
                if (is_scalar($data[$key])) {
                    if (gettype($data[$key]) !== $definition[0]) {
                        throw new ConfigSettingWrongTypeException("{$key}: {$data[$key]}");
                    }
                    else {
                        if (isset($definition[2]) && !in_array($data[$key], $definition[2])) {
                            throw new ConfigSettingInvalidValueException("{$key}: {$data[$key]}");
                        }
                        $value = $data[$key];
                    }
                }
                else {
                    $value = new $definition[0]($data[$key]);
                }
            }

            $config[$key] = $value;
        }

        return $config;
    }
}