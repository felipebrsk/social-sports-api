<?php

namespace App\Services;

use App\Models\Profile;
use App\DTOs\ProfileUpdate;
use Illuminate\Support\Str;
use App\Contracts\Repositories\ProfileRepositoryInterface;
use App\Contracts\Services\{
    StorageServiceInterface,
    ProfileServiceInterface,
};

use function sprintf;

/**
 * @extends AbstractService<Profile, ProfileRepositoryInterface>
 */
class ProfileService extends AbstractService implements ProfileServiceInterface
{
    /**
     * Create a new service instance.
     *
     * @param ProfileRepositoryInterface $repository
     * @param StorageServiceInterface $storageService
     * @return void
     */
    public function __construct(
        ProfileRepositoryInterface $repository,
        private readonly StorageServiceInterface $storageService,
    ) {
        parent::__construct($repository);
    }

    /**
     * {@inheritDoc}
     */
    public function updateByUserId(ProfileUpdate $data): void
    {
        $profile = $this->repository->select([
            'id',
            'avatar',
        ])->findOrFailBy('user_id', $data->uid);

        /** @var string|null $oldAvatar */
        $oldAvatar = $profile->getRawOriginal('avatar');

        $path = $oldAvatar;

        if ($data->avatar) {
            $extension = $data->avatar->getClientOriginalExtension() ?: 'png';
            $filename = sprintf('avatar_%s_%s.%s', $data->uid, Str::uuid(), $extension);

            $path = $this->storageService->storeContentAs(
                $data->avatar,
                'profiles',
                $filename,
                $oldAvatar,
            );
        }

        $payload = array_filter([
            'avatar' => $path,
            'bio' => $data->bio,
            'whatsapp' => $data->whatsapp,
            'instagram' => $data->instagram,
        ], fn (mixed $value) => $value !== null);

        $profile->update($payload);
    }
}
