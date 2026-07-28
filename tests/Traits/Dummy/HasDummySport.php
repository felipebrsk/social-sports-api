<?php

namespace Tests\Traits\Dummy;

use App\Models\Sport;
use Illuminate\Database\Eloquent\Collection;

trait HasDummySport
{
    /**
     * Create a generic sport.
     *
     * @param  array<string, mixed>  $data
     */
    public function createDummySport(array $data = []): Sport
    {
        return Sport::factory()->create($data);
    }

    /**
     * Create multiple generic sports.
     *
     * @param  array<string, mixed>  $data
     * @return Collection<int, Sport>
     */
    public function createDummySports(int $count, array $data = []): Collection
    {
        return Sport::factory($count)->create($data);
    }
}
