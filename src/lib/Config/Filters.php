<?php

namespace Pharizer\Config;

use Exception;

class Filters {
    /** @var array Filter */
    private $filters = [];

    /** @var string */
    private $baseDirectory;

    /**
     * Filters constructor.
     * @param string $baseDirectory
     * @param array $data
     */
    public function __construct(string $baseDirectory, array $data) {
        $this->baseDirectory = $baseDirectory;

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
     * @param $filter
     * @return Filter
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
    public function appendFilter(Filter $filter) {
        array_push($this->filters, $filter);
    }

    /**
     * @param Filter $filter
     */
    public function prependFilter(Filter $filter) {
        array_unshift($this->filters, $filter);
    }
}