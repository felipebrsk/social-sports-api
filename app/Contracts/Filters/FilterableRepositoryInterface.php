<?php

namespace App\Contracts\Filters;

interface FilterableRepositoryInterface
{
    /**
     * Get the allowed filters list for given repository.
     *
     * @return list<string>
     */
    public function getAllowedFilters(): array;

    /**
     * Get the allowed sort list for given repository.
     *
     * @return list<string>
     */
    public function getAllowedSorts(): array;

    /**
     * Get the default sort by filter for repository.
     *
     * @return string
     */
    public function getDefaultSortBy(): string;

    /**
     * Get the default sort order filter for repository.
     *
     * @return string
     */
    public function getDefaultSortOrder(): string;

    /**
     * Get the default per page filter for repository.
     *
     * @return int
     */
    public function getDefaultPerPage(): int;

    /**
     * Get the default limit filter for repository.
     *
     * @return int|null
     */
    public function getDefaultLimit(): ?int;
}
