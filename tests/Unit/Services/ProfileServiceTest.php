<?php

namespace Tests\Unit\Services;

use Mockery;
use Tests\TestCase;
use App\Models\Profile;
use Mockery\MockInterface;
use App\DTOs\ProfileUpdate;
use App\Services\ProfileService;
use Illuminate\Http\UploadedFile;
use App\Contracts\Repositories\ProfileRepositoryInterface;
use App\Contracts\Services\{
    ProfileServiceInterface,
    StorageServiceInterface,
};

class ProfileServiceTest extends TestCase
{
    /**
     * The storage service mock.
     *
     * @var StorageServiceInterface&MockInterface
     */
    private StorageServiceInterface&MockInterface $storageService;

    /**
     * The profile repository mock.
     *
     * @var ProfileRepositoryInterface&MockInterface
     */
    private ProfileRepositoryInterface&MockInterface $profileRepository;

    /**
     * The profile service.
     *
     * @var ProfileServiceInterface
     */
    private ProfileServiceInterface $profileService;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->storageService = Mockery::mock(StorageServiceInterface::class);
        $this->profileRepository = Mockery::mock(ProfileRepositoryInterface::class);

        $this->profileService = new ProfileService(
            $this->profileRepository,
            $this->storageService,
        );
    }

    /**
     * Test if the service is instantiated correctly.
     *
     * @return void
     */
    public function test_if_service_is_instantiated_correctly(): void
    {
        $this->assertInstanceOf(ProfileService::class, $this->profileService);
    }

    /**
     * Test if can update a profile without avatar.
     *
     * @return void
     */
    public function test_if_can_udpate_a_profile_without_avatar(): void
    {
        $dto = new ProfileUpdate(
            uid: 1,
            avatar: null,
            bio: 'Minha nova bio',
            whatsapp: '11999999999',
            instagram: '@dev_user',
        );

        $profile = Mockery::mock(Profile::class);
        $profile
            ->shouldReceive('getAttribute')
            ->with('avatar')
            ->andReturn('profiles/avatar_antigo.png');

        $this->storageService->shouldNotReceive('storeContentAs');

        $this->profileRepository
            ->shouldReceive('select')
            ->once()
            ->with([
                'id',
                'avatar',
            ])->andReturnSelf();

        $this->profileRepository
            ->shouldReceive('findOrFailBy')
            ->once()
            ->with('user_id', $dto->uid)
            ->andReturn($profile);

        $profile
            ->shouldReceive('update')
            ->once()
            ->with([
                'avatar' => 'profiles/avatar_antigo.png',
                'bio' => 'Minha nova bio',
                'whatsapp' => '11999999999',
                'instagram' => '@dev_user',
            ])->andReturnTrue();

        $this->profileService->updateByUserId($dto);
    }

    /**
     * Test if can update a profile with new avatar.
     *
     * @return void
     */
    public function test_if_can_update_a_profile_with_new_avatar(): void
    {
        $file = UploadedFile::fake()->create('novo_avatar.jpg', 100, 'image/jpeg');

        $dto = new ProfileUpdate(
            uid: 1,
            avatar: $file,
            bio: 'Bio com foto nova',
            whatsapp: '11999999999',
            instagram: '@dev_user',
        );

        $profile = Mockery::mock(Profile::class);
        $profile
            ->shouldReceive('getAttribute')
            ->with('avatar')
            ->andReturn('profiles/avatar_antigo.png');

        $this->profileRepository
            ->shouldReceive('select')
            ->once()
            ->with([
                'id',
                'avatar',
            ])->andReturnSelf();

        $this->profileRepository
            ->shouldReceive('findOrFailBy')
            ->once()
            ->with('user_id', $dto->uid)
            ->andReturn($profile);

        $this->storageService
            ->shouldReceive('storeContentAs')
            ->once()
            ->with(
                $file,
                'profiles',
                Mockery::pattern('/^avatar_1_[a-f0-9\-]+\.jpg$/'), // avatar_{uid}_{uuid}.jpg
                'profiles/avatar_antigo.png',
            )->andReturn('profiles/avatar_1_uuid_fake.jpg');

        $profile
            ->shouldReceive('update')
            ->once()
            ->with([
                'avatar' => 'profiles/avatar_1_uuid_fake.jpg',
                'bio' => 'Bio com foto nova',
                'whatsapp' => '11999999999',
                'instagram' => '@dev_user',
            ])->andReturnTrue();

        $this->profileService->updateByUserId($dto);
    }

    /**
     * Test if ignores null values on update payload.
     *
     * @return void
     */
    public function test_if_ignores_null_values_on_update_payload(): void
    {
        $dto = new ProfileUpdate(
            uid: 1,
            avatar: null,
            bio: 'Apenas a bio mudou',
            whatsapp: null,
            instagram: null,
        );

        $profile = Mockery::mock(Profile::class);
        $profile
            ->shouldReceive('getAttribute')
            ->with('avatar')
            ->andReturn('profiles/avatar_antigo.png');

        $this->profileRepository
            ->shouldReceive('select')
            ->once()
            ->with([
                'id',
                'avatar',
            ])->andReturnSelf();

        $this->profileRepository
            ->shouldReceive('findOrFailBy')
            ->once()
            ->with('user_id', $dto->uid)
            ->andReturn($profile);

        $profile
            ->shouldReceive('update')
            ->once()
            ->with([
                'avatar' => 'profiles/avatar_antigo.png',
                'bio' => 'Apenas a bio mudou',
            ])->andReturnTrue();

        $this->profileService->updateByUserId($dto);
    }
}
