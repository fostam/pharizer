<?php

namespace Pharizer\Builder;

use Pharizer\Config\Filter;
use Pharizer\Config\Target;
use RecursiveDirectoryIterator;
use RecursiveFilterIterator;
use RecursiveIterator;
use SplFileInfo;

class FileFilterIterator extends RecursiveFilterIterator {
    private Target $target;

    public function __construct(RecursiveIterator $iterator, Target $target) {
        $this->target = $target;
        parent::__construct($iterator);
    }

    public function accept(): bool {
        /** @var SplFileInfo $fileinfo */
        $fileinfo = $this->current();

        $filename = $fileinfo->getPathname();

        // don't apply filter to directories
        if ($fileinfo->isDir()) {
            return true;
        }

        // strip source-directory from beginning of path
        $filename = preg_replace('#^' . preg_quote($this->target->getSourceDirectory(), '#') . '/?#', '', $filename);

        // apply filters one by one
        foreach($this->target->getFilters()->get() as $filter) {
            $matches = $filter->matches($filename);
            if (!$matches) {
                continue;
            }

            if ($filter->getType() === Filter::TYPE_INCLUDE) {
                return true;
            }

            if ($filter->getType() === Filter::TYPE_EXCLUDE) {
                return false;
            }
        }

        return true;
    }

    public function getChildren(): FileFilterIterator {
        $iterator = $this->getInnerIterator();
        /** @var RecursiveDirectoryIterator $iterator */
        $children = $iterator->getChildren();
        return new self($children, $this->target);
    }
}