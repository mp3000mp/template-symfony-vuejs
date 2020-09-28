<?php

namespace App\Service\ListGen;

class ListGenConfig
{
    /**
     * @var bool
     */
    private $csvExportAllowed = true;

    /**
     * @var string
     */
    private $url = '';

    /**
     * @var int
     */
    private $page = 0;

    /**
     * @var int
     */
    private $nbPerPage = 30;

    /**
     * @var int[]
     */
    private $nbPerPageOptions = [30, 60, 120];

    public function isAllowedExportFormats(): bool
    {
        return $this->csvExportAllowed;
    }

    public function setIsAllowedExportFormats(bool $csvExportAllowed): ListGenConfig
    {
        $this->csvExportAllowed = $csvExportAllowed;

        return $this;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): ListGenConfig
    {
        $this->url = $url;

        return $this;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function setPage(int $page): ListGenConfig
    {
        $this->page = $page;

        return $this;
    }

    public function getNbPerPage(): int
    {
        return $this->nbPerPage;
    }

    public function setNbPerPage(int $nbPerPage): ListGenConfig
    {
        $this->nbPerPage = $nbPerPage;

        return $this;
    }

    /**
     * @return int[]
     */
    public function getNbPerPageOptions(): array
    {
        return $this->nbPerPageOptions;
    }

    /**
     * @param int[] $nbPerPageOptions
     */
    public function setNbPerPageOptions(array $nbPerPageOptions): ListGenConfig
    {
        $this->nbPerPageOptions = $nbPerPageOptions;

        return $this;
    }
}
