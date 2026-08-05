<?php

namespace Tests\Traits\Dummy;

use App\Models\SocialLink;
use Illuminate\Database\Eloquent\Collection;

trait HasDummySocialLink
{
    /**
     * Create a generic social link.
     *
     * @param  array<string, mixed>  $data
     */
    public function createDummySocialLink(array $data = []): SocialLink
    {
        return SocialLink::factory()->create($data);
    }

    /**
     * Create multiple generic social links.
     *
     * @param  array<string, mixed>  $data
     * @return Collection<int, SocialLink>
     */
    public function createDummySocialLinks(int $count, array $data = []): Collection
    {
        return SocialLink::factory($count)->create($data);
    }
}
