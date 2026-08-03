<?php

namespace App\Services;

use App\Models\SocialAccount;
use App\Contracts\Services\SocialAccountServiceInterface;
use App\Contracts\Repositories\SocialAccountRepositoryInterface;

/**
 * @extends AbstractService<SocialAccount, SocialAccountRepositoryInterface>
 */
class SocialAccountService extends AbstractService implements SocialAccountServiceInterface
{
    /**
     * Create a new service instance.
     *
     * @param SocialAccountRepositoryInterface $repository
     * @return void
     */
    public function __construct(SocialAccountRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
