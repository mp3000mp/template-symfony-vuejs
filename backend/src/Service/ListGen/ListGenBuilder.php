<?php

namespace App\Service\ListGen;

use App\Service\ListGen\Column\ColumnInterface;
use App\Service\ListGen\Filter\FilterInterface;

class ListGenBuilder
{
    /**
     * @var ListGenConfig
     */
    private $listGenConfig;

    /**
     * @var ColumnInterface[]
     */
    private $columns = [];

    /**
     * @var FilterInterface[]
     */
    private $filters = [];

    public function __construct()
    {
        $this->listGenConfig = new ListGenConfig();
    }

    public function getListGen(): ListGen
    {
        $listGen = new ListGen($this->listGenConfig);
        foreach ($this->columns as $column) {
            $listGen->addColumn($column);
        }
        foreach ($this->filters as $filter) {
            $listGen->addFilter($filter);
        }

        return $listGen;
    }

    public function setIsAllowedExportFormats(bool $csvExportAllowed): self
    {
        $this->listGenConfig->setIsAllowedExportFormats($csvExportAllowed);

        return $this;
    }

    public function setUrl(string $url): self
    {
        $this->listGenConfig->setUrl($url);

        return $this;
    }

    public function setPage(int $page): self
    {
        $this->listGenConfig->setPage($page);

        return $this;
    }

    public function setNbPerPage(int $nbPerPage): self
    {
        $this->listGenConfig->setNbPerPage($nbPerPage);

        return $this;
    }

    /**
     * @param int[] $nbPerPageOptions
     */
    public function setNbPerPageOptions(array $nbPerPageOptions): self
    {
        $this->listGenConfig->setNbPerPageOptions($nbPerPageOptions);

        return $this;
    }

    public function addColumn(ColumnInterface $column): void
    {
        $this->columns[] = $column;
    }

    public function addFilter(FilterInterface $filter): void
    {
        $this->filters[] = $filter;
    }
}
