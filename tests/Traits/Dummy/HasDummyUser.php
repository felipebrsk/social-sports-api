<?php

namespace Tests\Traits\Dummy;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

trait HasDummyUser
{
    /**
     * Create a generic user.
     *
     * @param  array<string, mixed>  $data
     */
    public function createDummyUser(array $data = []): User
    {
        return User::factory()->create($data);
    }

    /**
     * Create multiple generic users.
     *
     * @param  array<string, mixed>  $data
     * @return Collection<int, User>
     */
    public function createDummyUsers(int $count, array $data = []): Collection
    {
        return User::factory($count)->create($data);
    }

    /**
     * Act as dummy user.
     *
     * @param array<string, mixed> $data
     * @return User
     */
    public function actingAsDummyUser(array $data = []): User
    {
        $user = $this->createDummyUser($data);

        $this->actingAs($user);

        return $user;
    }
}
