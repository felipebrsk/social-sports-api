<?php

namespace Tests\Unit\Casts;

use Mockery;
use Tests\TestCase;
use App\Models\Profile;
use App\Casts\StorageUrl;
use Mockery\MockInterface;
use App\Enums\MediaTypeEnum;
use App\Contracts\Services\StorageServiceInterface;

class StorageUrlTest extends TestCase
{
    /**
     * The storage service mock.
     *
     * @var StorageServiceInterface&MockInterface
     */
    private StorageServiceInterface&MockInterface $storageService;

    /**
     * The storage url cast.
     *
     * @var StorageUrl
     */
    private StorageUrl $cast;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->storageService = Mockery::mock(StorageServiceInterface::class);

        $this->cast = new StorageUrl($this->storageService);
    }

    /**
     * Test get returns null when value is null, empty or not a string.
     *
     * @return void
     */
    public function test_get_returns_null_for_invalid_or_empty_values(): void
    {
        $model = new Profile();

        $this->assertNull($this->cast->get($model, 'avatar', null, []));
        $this->assertNull($this->cast->get($model, 'avatar', '', []));
        $this->assertNull($this->cast->get($model, 'avatar', 12345, []));
        $this->assertNull($this->cast->get($model, 'avatar', [], []));
    }

    /**
     * Test get returns raw value intact when media_type_id corresponds to LINK_TYPE_ID.
     */
    public function test_get_returns_raw_url_when_media_type_is_external_link(): void
    {
        $model = new Profile();

        $externalUrl = 'https://example.com/external-link.mp4';

        $this->storageService->shouldNotReceive('getUrl');

        $result = $this->cast->get($model, 'path', $externalUrl, [
            'media_type_id' => MediaTypeEnum::LINK_TYPE_ID->value,
        ]);

        $this->assertEquals($externalUrl, $result);
    }

    /**
     * Test get delegates to StorageService when value is valid and not a link.
     */
    public function test_get_resolves_url_using_storage_service(): void
    {
        $model = new Profile();

        $path = 'avatars/user-123.jpg';
        $expectedUrl = 'https://s3.amazonaws.com/bucket/avatars/user-123.jpg';

        $this->storageService
            ->shouldReceive('getUrl')
            ->once()
            ->with($path)
            ->andReturn($expectedUrl);

        $result = $this->cast->get($model, 'avatar', $path, []);

        $this->assertEquals($expectedUrl, $result);
    }

    /**
     * Test get delegates to StorageService when media_type_id is not LINK_TYPE_ID.
     */
    public function test_get_resolves_url_using_storage_service_for_other_media_types(): void
    {
        $model = new Profile();

        $path = 'images/photo.png';
        $expectedUrl = 'https://s3.amazonaws.com/bucket/images/photo.png';

        $this->storageService
            ->shouldReceive('getUrl')
            ->once()
            ->with($path)
            ->andReturn($expectedUrl);

        $result = $this->cast->get($model, 'path', $path, [
            'media_type_id' => MediaTypeEnum::IMAGE_TYPE_ID->value,
        ]);

        $this->assertEquals($expectedUrl, $result);
    }

    /**
     * Test get resolves default dependency via container when instance is initialized without parameters.
     */
    public function test_get_resolves_dependency_from_container_when_constructor_is_empty(): void
    {
        $path = 'documents/file.pdf';
        $expectedUrl = 'http://localhost/storage/documents/file.pdf';

        $mockService = Mockery::mock(StorageServiceInterface::class);
        $mockService
            ->shouldReceive('getUrl')
            ->once()
            ->with($path)
            ->andReturn($expectedUrl);

        $this->app->instance(StorageServiceInterface::class, $mockService);

        $cast = new StorageUrl();

        $result = $cast->get(new Profile(), 'path', $path, []);

        $this->assertEquals($expectedUrl, $result);
    }

    /**
     * Test set method keeps and returns raw value for database persistence.
     */
    public function test_set_returns_raw_value_for_storage(): void
    {
        $model = new Profile();

        $path = 'avatars/user-123.jpg';

        $result = $this->cast->set($model, 'avatar', $path, []);

        $this->assertEquals($path, $result);
    }
}
