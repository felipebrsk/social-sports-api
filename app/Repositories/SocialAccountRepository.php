<?php

namespace App\Repositories;

use App\Models\SocialAccount;
use App\Contracts\Repositories\SocialAccountRepositoryInterface;

/**
 * @extends AbstractRepository<SocialAccount>
 */
class SocialAccountRepository extends AbstractRepository implements SocialAccountRepositoryInterface
{
    /**
     * {@inheritDoc}
     */
    protected array $allowedFilters = [];

    /**
     * {@inheritDoc}
     */
    protected array $allowedSorts = [];

    /**
     * Create a new repository instance.
     *
     * @param SocialAccount $model
     * @return void
     */
    public function __construct(SocialAccount $model)
    {
        parent::__construct($model);
    }
}
