<?php

namespace Tests\Traits\Dummy;

use App\Models\Team;
use Illuminate\Database\Eloquent\Collection;

trait HasDummyTeam
{
    /**
     * Create a generic team.
     *
     * @param  array<string, mixed>  $data
     */
    public function createDummyTeam(array $data = []): Team
    {
        return Team::factory()->create($data);
    }

    /**
     * Create multiple generic teams.
     *
     * @param  array<string, mixed>  $data
     * @return Collection<int, Team>
     */
    public function createDummyTeams(int $count, array $data = []): Collection
    {
        return Team::factory($count)->create($data);
    }
}
