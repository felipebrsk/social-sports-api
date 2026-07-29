<?php

namespace App\Casts;

use App\Enums\MediaTypeEnum;
use Illuminate\Database\Eloquent\Model;
use App\Contracts\Services\StorageServiceInterface;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

use function is_string;

class StorageUrl implements CastsAttributes
{
    /**
     * Create a new cast instance.
     *
     * @param StorageServiceInterface $storageService
     * @return void
     */
    public function __construct(
        private ?StorageServiceInterface $storageService = null,
    ) {
        $this->storageService ??= app(StorageServiceInterface::class);
    }

    /**
     * Cast the given value (relative path) to full Storage URL.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if (! is_string($value) || empty($value)) {
            return null;
        }

        /** @var string|int|null $mediaTypeId */
        $mediaTypeId = $attributes['media_type_id'] ?? null;

        if ($mediaTypeId && (int) $mediaTypeId === MediaTypeEnum::LINK_TYPE_ID->value) {
            return $value;
        }

        return $this->storageService->getUrl($value);
    }

    /**
     * Prepare the given value for storage (keep relative path in database).
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        return $value;
    }
}
