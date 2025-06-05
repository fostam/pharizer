<?php

namespace Pharizer\Builder;

use Pharizer\Config\Target;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use FilesystemIterator;
use Traversable;

class FileIterator extends RecursiveIteratorIterator {
    protected function __construct(Traversable $iterator, $mode = self::LEAVES_ONLY, $flags = 0) {
        parent::__construct($iterator, $mode, $flags);
    }

    public static function create(Target $target): FileIterator {
        $directoryIterator = new RecursiveDirectoryIterator($target->getSourceDirectory(), FilesystemIterator::SKIP_DOTS | FilesystemIterator::FOLLOW_SYMLINKS);
        $filterIterator = new FileFilterIterator($directoryIterator, $target);
        return new self($filterIterator);
    }
}