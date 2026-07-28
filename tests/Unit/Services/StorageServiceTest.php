<?php

namespace Tests\Unit\Services;

use Mockery;
use Tests\TestCase;
use App\Services\StorageService;
use Illuminate\Http\UploadedFile;
use App\Contracts\Services\StorageServiceInterface;
use Illuminate\Support\Facades\{
    Config,
    Storage,
};

class StorageServiceTest extends TestCase
{
    /**
     * The local storage disk.
     *
     * @var string
     */
    private const string LOCAL_STORAGE_DISK = 'fakeAws';

    /**
     * The local storage service.
     *
     * @var StorageServiceInterface
     */
    private StorageServiceInterface $localStorageService;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake(self::LOCAL_STORAGE_DISK);

        $this->localStorageService = new StorageService();
    }

    /**
     * Test if can get correctly disk for local storage.
     *
     * @return void
     */
    public function test_if_can_get_correctly_disk_for_local_storage(): void
    {
        Config::set('filesystems.default', self::LOCAL_STORAGE_DISK);

        $result = $this->localStorageService->getDisk();

        $this->assertEquals(self::LOCAL_STORAGE_DISK, $result);
    }

    /**
     * Test if can store a file with local storage.
     *
     * @return void
     */
    public function test_if_can_store_a_file_with_local_storage(): void
    {
        $path = fake()->imageUrl();

        $directory = 'fakes/files';

        $file = Mockery::mock(UploadedFile::class);
        $file
            ->shouldReceive('store')
            ->once()
            ->with($directory, self::LOCAL_STORAGE_DISK)
            ->andReturn($path);

        $result = $this->localStorageService->store($file, $directory);

        $this->assertEquals($path, $result);
    }

    /**
     * Test if can store a file with local storage and remove old path if given.
     *
     * @return void
     */
    public function test_if_can_store_a_file_with_local_storage_and_remove_old_path_if_given(): void
    {
        $oldPath = 'fakes/files/old-image.jpg';
        Storage::disk(self::LOCAL_STORAGE_DISK)->put($oldPath, 'conteudo_antigo');

        Storage::disk(self::LOCAL_STORAGE_DISK)->assertExists($oldPath);

        $directory = 'fakes/files';

        $file = UploadedFile::fake()->create('nova_foto.png', 100, 'text/plain');

        /** @var string $result */
        $result = $this->localStorageService->store($file, $directory, $oldPath);

        Storage::disk(self::LOCAL_STORAGE_DISK)->assertMissing($oldPath);

        Storage::disk(self::LOCAL_STORAGE_DISK)->assertExists($result);
    }

    /**
     * Test if can store content as filename with local storage.
     *
     * @return void
     */
    public function test_if_can_store_content_as_filename_with_local_storage(): void
    {
        $directory = 'fakes/files';

        $filename = 'documento_customizado.txt';
        $file = UploadedFile::fake()->createWithContent('documento.txt', 'texto');

        $result = $this->localStorageService->storeContentAs($file, $directory, $filename);

        $expectedPath = "{$directory}/{$filename}";

        $this->assertEquals($expectedPath, $result);
        Storage::disk(self::LOCAL_STORAGE_DISK)->assertExists($expectedPath);
    }

    /**
     * Test if can store content as filename and remove old path if given.
     *
     * @return void
     */
    public function test_if_can_store_content_as_filename_and_remove_old_path_if_given(): void
    {
        $oldPath = 'fakes/files/foto_velha.png';

        Storage::disk(self::LOCAL_STORAGE_DISK)->put($oldPath, 'conteudo_antigo');
        Storage::disk(self::LOCAL_STORAGE_DISK)->assertExists($oldPath);

        $directory = 'fakes/files';
        $filename = 'nova_foto_perfil.png';

        $file = UploadedFile::fake()->create('nova_foto.png', 100, 'text/plain');

        $result = $this->localStorageService->storeContentAs($file, $directory, $filename, $oldPath);

        $expectedNewPath = "{$directory}/{$filename}";

        $this->assertEquals($expectedNewPath, $result);

        Storage::disk(self::LOCAL_STORAGE_DISK)->assertMissing($oldPath);

        Storage::disk(self::LOCAL_STORAGE_DISK)->assertExists($expectedNewPath);
    }

    /**
     * Test if can delete a file when it exists.
     *
     * @return void
     */
    public function test_if_can_delete_a_file_when_it_exists(): void
    {
        $filePath = 'fakes/files/deleteme.txt';

        Storage::disk(self::LOCAL_STORAGE_DISK)->put($filePath, 'conteudo');
        Storage::disk(self::LOCAL_STORAGE_DISK)->assertExists($filePath);

        $this->localStorageService->delete($filePath);

        Storage::disk(self::LOCAL_STORAGE_DISK)->assertMissing($filePath);
    }

    /**
     * Test if delete handles non existing or null file path gracefully.
     *
     * @return void
     */
    public function test_if_delete_handles_non_existing_or_null_file_path_gracefully(): void
    {
        $nonExistingPath = 'fakes/files/ghost.txt';

        $this->localStorageService->delete($nonExistingPath);

        Storage::disk(self::LOCAL_STORAGE_DISK)->assertMissing($nonExistingPath);

        $this->localStorageService->delete(null);

        $this->assertEquals(0, Mockery::getContainer()->mockery_getExpectationCount());
    }

    /**
     * Test if can get URL when file path is given.
     *
     * @return void
     */
    public function test_if_can_get_url_when_file_path_is_given(): void
    {
        $filePath = 'fakes/files/document.pdf';

        $result = $this->localStorageService->getUrl($filePath);

        $expectedUrl = Storage::disk(self::LOCAL_STORAGE_DISK)->url($filePath);

        $this->assertEquals($expectedUrl, $result);
        $this->assertNotNull($result);
    }

    /**
     * Test if get URL returns null when file path is null.
     *
     * @return void
     */
    public function test_if_get_url_returns_null_when_file_path_is_null(): void
    {
        $result = $this->localStorageService->getUrl(null);

        $this->assertNull($result);
    }

    /**
     * Test if exists returns false when path is null.
     *
     * @return void
     */
    public function test_if_exists_returns_false_when_path_is_null(): void
    {
        $result = $this->localStorageService->exists(null);

        $this->assertFalse($result);
    }

    /**
     * Test if exists returns true when file exists.
     *
     * @return void
     */
    public function test_if_exists_returns_true_when_file_exists(): void
    {
        $path = 'fakes/files/active-file.jpg';

        Storage::disk(self::LOCAL_STORAGE_DISK)->put($path, 'content');

        $result = $this->localStorageService->exists($path);

        $this->assertTrue($result);
    }

    /**
     * Test if exists returns false when file does not exist.
     *
     * @return void
     */
    public function test_if_exists_returns_false_when_file_does_not_exist(): void
    {
        $result = $this->localStorageService->exists('fakes/files/missing-file.jpg');

        $this->assertFalse($result);
    }

    /**
     * Test if can override disk via constructor.
     *
     * @return void
     */
    public function test_if_can_override_disk_via_constructor(): void
    {
        Config::set('filesystems.default', 'local');

        $s3StorageService = new StorageService('s3');

        $this->assertEquals('s3', $s3StorageService->getDisk());
    }
}
