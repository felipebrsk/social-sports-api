<?php

namespace App\Contracts\Services;

use Illuminate\Http\UploadedFile;

interface StorageServiceInterface
{
    /**
     * Stores a new file, optionally deleting an old one.
     *
     * @param UploadedFile $file
     * @param string $directory
     * @param string|null $oldFilePath
     * @return string|false
     */
    public function store(
        UploadedFile $file,
        string $directory,
        ?string $oldFilePath = null,
    ): string|false;

    /**
     * Stores raw file contents with a specific filename, optionally deleting an old one.
     *
     * @param UploadedFile $file File to upload
     * @param string $directory Target directory (e.g., "contracts/2025/")
     * @param string $filename Target filename (e.g., "contract.pdf")
     * @param string|null $oldFilePath Existing file path to delete before storing
     * @return string|false Stored relative path within the disk
     */
    public function storeContentAs(
        UploadedFile $file,
        string $directory,
        string $filename,
        ?string $oldFilePath = null,
    ): string|false;

    /**
     * Deletes a file from the storage.
     *
     * @param string|null $filePath
     * @return void
     */
    public function delete(?string $filePath): void;

    /**
     * Gets the public URL for a given file path.
     *
     * @param string|null $filePath
     * @return string|null
     */
    public function getUrl(?string $filePath): ?string;

    /**
     * Check if the file exists.
     *
     * @param string|null $filePath
     * @return bool
     */
    public function exists(?string $filePath): bool;

    /**
     * Get the configured default filesystem disk dynamically.
     *
     * @return string
     */
    public function getDisk(): string;
}
