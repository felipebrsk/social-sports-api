<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Contracts\Services\StorageServiceInterface;

class StorageService implements StorageServiceInterface
{
    /**
     * Create a new service instance.
     *
     * @param string|null $disk
     * @return void
     */
    public function __construct(
        private readonly ?string $disk = null,
    ) {
        //
    }

    /**
     * {@inheritDoc}
     */
    public function store(
        UploadedFile $file,
        string $directory,
        ?string $oldFilePath = null,
    ): string|false {
        if ($oldFilePath) {
            $this->delete($oldFilePath);
        }

        return $file->store($directory, $this->getDisk());
    }

    /**
     * {@inheritDoc}
     */
    public function storeContentAs(
        UploadedFile $file,
        string $directory,
        string $filename,
        ?string $oldFilePath = null,
    ): string|false {
        if ($oldFilePath) {
            $this->delete($oldFilePath);
        }

        $directory = trim($directory, '/');
        $filename = ltrim($filename, '/');

        $path = Storage::disk($this->getDisk())->putFileAs(
            $directory,
            $file,
            $filename,
        );

        return $path;
    }

    /**
     * {@inheritDoc}
     */
    public function delete(?string $filePath): void
    {
        if ($filePath && Storage::disk($this->getDisk())->exists($filePath)) {
            Storage::disk($this->getDisk())->delete($filePath);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getUrl(?string $filePath): ?string
    {
        if ($filePath) {
            return Storage::disk($this->getDisk())->url($filePath);
        }

        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function exists(?string $path = null): bool
    {
        if (! $path) {
            return false;
        }

        return Storage::disk($this->getDisk())->exists($path);
    }

    /**
     * {@inheritDoc}
     */
    public function getDisk(): string
    {
        /** @var string $disk */
        $disk = config('filesystems.default');

        return $this->disk ?? $disk;
    }
}
