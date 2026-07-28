<?php

namespace Tests\Traits\Dummy;

use App\Models\Profile;
use Illuminate\Database\Eloquent\Collection;

trait HasDummyProfile
{
    /**
     * Create a dummy profile.
     *
     * @param  array<string, mixed>  $data
     */
    public function createDummyProfile(array $data = []): Profile
    {
        return Profile::factory()->create($data);
    }

    /**
     * Create multiple profiles.
     *
     * @param  array<string, mixed>  $data
     * @return Collection<int, Profile>
     */
    public function createDummyProfiles(int $count, array $data = []): Collection
    {
        return Profile::factory($count)->create($data);
    }

    /**
     * Create a dummy profile to a given user.
     *
     * @param int $userId
     * @param  array<string, mixed>  $data
     * @return Profile
     */
    public function createDummyProfileTo(int $userId, array $data = []): Profile
    {
        return $this->createDummyProfile([
            'user_id' => $userId,
            ...$data,
        ]);
    }
}
