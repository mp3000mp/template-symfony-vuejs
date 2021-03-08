<?php

namespace App\Service\ListGen;

use App\Service\ListGen\Column\ColumnInterface;
use App\Service\ListGen\Filter\FilterInterface;

class ListGen implements ListGenInterface
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

    public function __construct(ListGenConfig $listGenConfig)
    {
        $this->listGenConfig = $listGenConfig;
    }

    public function isAllowedExportFormats(): bool
    {
        return $this->listGenConfig->isAllowedExportFormats();
    }

    public function getUrl(): string
    {
        return $this->listGenConfig->getUrl();
    }

    public function getPage(): int
    {
        return $this->listGenConfig->getPage();
    }

    public function getNbPerPage(): int
    {
        return $this->listGenConfig->getNbPerPage();
    }

    /**
     * @return int[]
     */
    public function getNbPerPageOptions(): array
    {
        return $this->listGenConfig->getNbPerPageOptions();
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
