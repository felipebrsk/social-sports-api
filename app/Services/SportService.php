<?php

namespace App\Services;

use App\Models\Sport;
use Illuminate\Support\Carbon;
use App\Enums\GameSessionStatusEnum;
use App\Contracts\Services\SportServiceInterface;
use App\Contracts\Repositories\SportRepositoryInterface;
use Illuminate\Database\Eloquent\{
    Builder,
    Collection,
};

/**
 * @extends AbstractService<Sport, SportRepositoryInterface>
 */
class SportService extends AbstractService implements SportServiceInterface
{
    /**
     * Create a new service instance.
     *
     * @param SportRepositoryInterface $repository
     * @return void
     */
    public function __construct(SportRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    /**
     * {@inheritdoc}
     */
    public function all(): Collection
    {
        $now = Carbon::now()->toDateTimeString();

        $invalidStatusIds = [
            GameSessionStatusEnum::FINISHED->value,
            GameSessionStatusEnum::CANCELLED->value,
        ];

        return $this->repository->select([
            'id',
            'name',
            'icon',
        ])->withCount([
            'venues',
            'gameSessions as upcoming_games_count' => function (Builder $query) use ($now, $invalidStatusIds) {
                $query->where('start_time', '>', $now)
                    ->whereNotIn('game_session_status_id', $invalidStatusIds);
            },
            'gameSessions as ongoing_games_count' => function (Builder $query) use ($now, $invalidStatusIds) {
                $query->where('start_time', '<=', $now)
                    ->where('end_time', '>=', $now)
                    ->whereNotIn('game_session_status_id', $invalidStatusIds);
            },
        ])->all();
    }
}
