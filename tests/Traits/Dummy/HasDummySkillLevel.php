<?php

namespace Tests\Traits\Dummy;

use App\Models\SkillLevel;
use Illuminate\Database\Eloquent\Collection;

trait HasDummySkillLevel
{
    /**
     * Create a generic venue.
     *
     * @param  array<string, mixed>  $data
     */
    public function createDummySkillLevel(array $data = []): SkillLevel
    {
        return SkillLevel::factory()->create($data);
    }

    /**
     * Create multiple generic venues.
     *
     * @param  array<string, mixed>  $data
     * @return Collection<int, SkillLevel>
     */
    public function createDummySkillLevels(int $count, array $data = []): Collection
    {
        return SkillLevel::factory($count)->create($data);
    }
}
