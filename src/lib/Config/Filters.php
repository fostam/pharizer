<?php

namespace Pharizer\Config;

use Exception;

class Filters {
    private array $filters = [];

    public function __construct(string $baseDirectory, array $data) {
        foreach($data as $filter => $filterData) {
            $this->filters[$filter] = new Filter($filterData);
        }
    }

    /**
     * @return Filter[]
     */
    public function get(): array {
        return $this->filters;
    }

    /**
     * @throws Exception
     */
    public function getFilter(string $filter): Filter {
        if (!isset($this->filters[$filter])) {
            throw new Exception("filter {$filter} does not exist");
        }
        return $this->filters[$filter];
    }

    /**
     * @param Filter $filter
     */
    public function appendFilter(Filter $filter): void {
        $this->filters[] = $filter;
    }

    /**
     * @param Filter $filter
     */
    public function prependFilter(Filter $filter): void {
        array_unshift($this->filters, $filter);
    }
}