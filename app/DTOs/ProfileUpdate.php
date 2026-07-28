<?php

namespace App\DTOs;

use Illuminate\Http\UploadedFile;

class ProfileUpdate
{
    /**
     * Create a new instance.
     *
     * @param int $uid
     * @param string|null $bio
     * @param string|null $whatsapp
     * @param string|null $instagram
     * @param UploadedFile|null $avatar
     * @return void
     */
    public function __construct(
        public readonly int $uid,
        public readonly ?string $bio,
        public readonly ?string $whatsapp,
        public readonly ?string $instagram,
        public readonly ?UploadedFile $avatar,
    ) {
        //
    }

    /**
     * Extract data from array.
     *
     * @param int $uid
     * @param array<string, string|null|UploadedFile> $data
     * @return self
     */
    public static function fromRequest(int $uid, array $data): self
    {
        $avatar = $data['avatar'] ?? null;

        return new self(
            $uid,
            $data['bio'] ?? null,
            $data['whatsapp'] ?? null,
            $data['instagram'] ?? null,
            avatar: $avatar instanceof UploadedFile ? $avatar : null,
        );
    }
}
