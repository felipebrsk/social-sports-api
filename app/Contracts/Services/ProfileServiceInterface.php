<?php

namespace App\Contracts\Services;

use App\Models\Profile;
use App\DTOs\ProfileUpdate;

/**
 * @extends AbstractServiceInterface<Profile>
 */
interface ProfileServiceInterface extends AbstractServiceInterface
{
    /**
     * Update a profile by the user id.
     *
     * @param ProfileUpdate $data
     * @return void
     */
    public function updateByUserId(ProfileUpdate $data): void;
}
