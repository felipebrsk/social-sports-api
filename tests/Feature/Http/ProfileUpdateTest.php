<?php

namespace Tests\Feature\Http;

use Generator;
use Illuminate\Http\UploadedFile;
use Tests\Traits\Dummy\HasDummyProfile;
use Illuminate\Support\Facades\Storage;
use Tests\Feature\BaseIntegrationTesting;
use PHPUnit\Framework\Attributes\DataProvider;

class ProfileUpdateTest extends BaseIntegrationTesting
{
    use HasDummyProfile;

    /**
     * Get the route name.
     *
     * @return string
     */
    private function getRouteName(): string
    {
        return 'profiles.update';
    }

    /**
     * Test validation rules for invalid profile update payloads using DataProvider.
     *
     * @param array<string, mixed> $payload
     * @param array<string, string> $expectedErrorMessages
     * @return void
     */
    #[DataProvider('invalidProfilePayloadsProvider')]
    public function test_if_fails_with_invalid_profile_payloads(
        array $payload,
        array $expectedErrorMessages,
    ): void {
        $response = $this->putJson(route($this->getRouteName()), $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(array_keys($expectedErrorMessages));

        foreach ($expectedErrorMessages as $message) {
            $response->assertSee($message);
        }
    }

    /**
     * Test if unauthenticated user cannot update profile.
     *
     * @return void
     */
    public function test_if_unauthenticated_user_cannot_update_profile(): void
    {
        $this->actingAsGuest();

        $this->putJson(route($this->getRouteName()), [
            'bio' => 'Tentativa sem auth',
        ])->assertUnauthorized();
    }

    /**
     * Test if can update profile text fields without changing avatar.
     *
     * @return void
     */
    public function test_if_can_update_profile_text_fields_without_avatar(): void
    {
        $profile = $this->createDummyProfileTo($this->user->id, [
            'bio' => 'Bio antiga',
            'instagram' => '@antigo',
            'whatsapp' => '11888888888',
            'avatar' => 'profiles/existing_avatar.png',
        ]);

        $payload = [
            'bio' => 'Minha nova bio atualizada',
            'whatsapp' => '11999999999',
            'instagram' => '@novo_instagram',
        ];

        $this->putJson(route($this->getRouteName()), $payload)
            ->assertOk()
            ->assertJsonPath('message', 'O seu perfil foi atualizado com sucesso!');

        $this->assertDatabaseHas('profiles', [
            'id' => $profile->id,
            'bio' => $payload['bio'],
            'user_id' => $this->user->id,
            'whatsapp' => $payload['whatsapp'],
            'instagram' => $payload['instagram'],
            'avatar' => 'profiles/existing_avatar.png',
        ]);
    }

    /**
     * Test if partial updates preserve unprovided fields in payload.
     *
     * @return void
     */
    public function test_if_partial_update_preserves_unprovided_fields(): void
    {
        $profile = $this->createDummyProfileTo($this->user->id, [
            'bio' => 'Bio original intacta',
            'whatsapp' => '11999999999',
            'instagram' => '@insta_original',
        ]);

        $payload = [
            'bio' => 'Apenas a bio foi alterada',
        ];

        $this->putJson(route($this->getRouteName()), $payload)->assertOk();

        $this->assertDatabaseHas('profiles', [
            'id' => $profile->id,
            'whatsapp' => '11999999999',
            'user_id' => $this->user->id,
            'instagram' => '@insta_original',
            'bio' => 'Apenas a bio foi alterada',
        ]);
    }

    /**
     * Test if can upload a new avatar and store it correctly when user has no avatar yet.
     *
     * @return void
     */
    public function test_if_can_upload_new_avatar_when_none_exists(): void
    {
        $profile = $this->createDummyProfileTo($this->user->id, [
            'avatar' => null,
        ]);

        $file = UploadedFile::fake()->create('novo_avatar.jpg', 100);

        $payload = [
            'avatar' => $file,
        ];

        $this->putJson(route($this->getRouteName()), $payload)->assertOk();

        $profile->refresh();

        /** @var string $avatarPath */
        $avatarPath = $profile->getRawOriginal('avatar');

        Storage::disk($this->disk)->assertExists($avatarPath);
    }

    /**
     * Test if can update avatar and delete the old image file from storage.
     *
     * @return void
     */
    public function test_if_updating_avatar_deletes_old_file_from_storage(): void
    {
        $oldAvatarPath = 'profiles/old_photo_123.jpg';

        Storage::disk($this->disk)->put($oldAvatarPath, 'conteudo_imagem_antiga');
        Storage::disk($this->disk)->assertExists($oldAvatarPath);

        $profile = $this->createDummyProfileTo($this->user->id, [
            'avatar' => $oldAvatarPath,
        ]);

        $newFile = UploadedFile::fake()->create('foto_nova.png', 100);

        $payload = [
            'avatar' => $newFile,
        ];

        $this->putJson(route($this->getRouteName()), $payload)->assertOk();

        $profile->refresh();

        Storage::disk($this->disk)->assertMissing($oldAvatarPath);

        /** @var string $newAvatarPath */
        $newAvatarPath = $profile->getRawOriginal('avatar');

        $this->assertNotEquals($oldAvatarPath, $newAvatarPath);
        Storage::disk($this->disk)->assertExists($newAvatarPath);
    }

    /**
     * Data provider with generators for invalid profile update payloads.
     *
     * @return Generator<string, array{payload: array<string, mixed>, expectedErrorMessages: array<string, string>}>
     */
    public static function invalidProfilePayloadsProvider(): Generator
    {
        yield 'invalid data types for text fields' => [
            'payload' => [
                'bio' => 12345,
                'whatsapp' => 12345,
                'instagram' => 12345,
            ],
            'expectedErrorMessages' => [
                'bio' => 'O campo bio deve ser uma string.',
                'whatsapp' => 'O campo whatsapp deve ser uma string.',
                'instagram' => 'O campo instagram deve ser uma string.',
            ],
        ];

        yield 'bio exceeds maximum characters length' => [
            'payload' => [
                'bio' => str_repeat('A', 501),
            ],
            'expectedErrorMessages' => [
                'bio' => 'O campo bio n\u00e3o pode ser superior a 500 caracteres.',
            ],
        ];

        yield 'whatsapp exceeds maximum characters length' => [
            'payload' => [
                'whatsapp' => str_repeat('A', 256),
            ],
            'expectedErrorMessages' => [
                'whatsapp' => 'O campo whatsapp n\u00e3o pode ser superior a 255 caracteres.',
            ],
        ];

        yield 'instagram exceeds maximum characters length' => [
            'payload' => [
                'instagram' => str_repeat('A', 256),
            ],
            'expectedErrorMessages' => [
                'instagram' => 'O campo instagram n\u00e3o pode ser superior a 255 caracteres.',
            ],
        ];

        yield 'avatar is not a file' => [
            'payload' => [
                'avatar' => 'string-instead-of-file',
            ],
            'expectedErrorMessages' => [
                'avatar' => 'O campo avatar deve ser um arquivo.',
            ],
        ];

        yield 'avatar has invalid mime extension' => [
            'payload' => [
                'avatar' => UploadedFile::fake()->create('document.pdf', 100),
            ],
            'expectedErrorMessages' => [
                'avatar' => 'O campo avatar deve ser um arquivo do tipo: png, jpg, jpeg.',
            ],
        ];
    }
}
