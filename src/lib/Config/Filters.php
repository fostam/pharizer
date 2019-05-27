<?php

namespace Pharizer\Config;

use Exception;

class Filters {
    /** @var array Filter */
    private $filters = [];

    /**
     * Filters constructor.
     * @param $data
     */
    public function __construct($data) {
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
    public function getFilter($filter): Filter {
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