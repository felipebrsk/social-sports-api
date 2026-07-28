<?php

namespace Tests\Unit\Requests\Profile;

use Generator;
use Illuminate\Http\UploadedFile;
use Tests\Contracts\Requests\BaseRequestTesting;
use App\Http\Requests\Profile\ProfileUpdateRequest;
use PHPUnit\Framework\Attributes\{
    CoversClass,
    DataProvider,
};

/**
 * Class ProfileUpdateRequestTest
 *
 * @package Tests\Unit\Requests\Profile
 */
#[CoversClass(ProfileUpdateRequest::class)]
class ProfileUpdateRequestTest extends BaseRequestTesting
{
    /**
     * {@inheritDoc}
     */
    public function request(): string
    {
        return ProfileUpdateRequest::class;
    }

    /**
     * Test that valid data passes validation.
     *
     * @param array<string, mixed> $data
     * @return void
     */
    #[DataProvider('validDataProvider')]
    public function test_valid_data_passes(array $data): void
    {
        $this->reloadRequestRules();

        $this->assertTrue($this->validate($data), 'Validation should pass for the provided valid data.');
    }

    /**
     * Test that invalid data fails validation with the correct error messages.
     *
     * @param array<string, mixed> $data
     * @param array<string, string> $expectedErrors
     * @return void
     */
    #[DataProvider('invalidDataProvider')]
    public function test_invalid_data_fails(array $data, array $expectedErrors): void
    {
        $this->assertFalse($this->validate($data), 'Validation should fail for the provided invalid data.');

        $errors = $this->getValidationErrors($data);

        foreach ($expectedErrors as $field => $expectedMessage) {
            $this->assertArrayHasKey($field, $errors, "Error key for '$field' was not found.");
            $this->assertStringContainsString($expectedMessage, $errors[$field][0], "The error message for '$field' did not match the expected value.");
        }
    }

    /**
     * Data provider for valid validation cases.
     *
     * @return Generator<string, array{data: array<string, mixed>}>
     */
    public static function validDataProvider(): Generator
    {
        yield 'empty payload is valid' => [
            'data' => [],
        ];

        yield 'all fields with valid values' => [
            'data' => [
                'whatsapp' => '11999999999',
                'instagram' => '@usuario_teste',
                'bio' => 'Esta é uma bio válida de teste.',
                'avatar' => UploadedFile::fake()->create('avatar.png', 100),
            ],
        ];

        yield 'avatar with jpg extension' => [
            'data' => [
                'avatar' => UploadedFile::fake()->create('avatar.jpg', 100),
            ],
        ];

        yield 'avatar with jpeg extension' => [
            'data' => [
                'avatar' => UploadedFile::fake()->create('avatar.jpeg', 100),
            ],
        ];

        yield 'bio with exactly 500 characters' => [
            'data' => [
                'bio' => str_repeat('A', 500),
            ],
        ];
    }

    /**
     * Data provider for invalid validation cases.
     *
     * @return Generator<string, array{data: array<string, mixed>, expectedErrors: array<string, string>}>
     */
    public static function invalidDataProvider(): Generator
    {
        yield 'invalid data types for text fields' => [
            'data' => [
                'bio' => 12345,
                'whatsapp' => 12345,
                'instagram' => 12345,
            ],
            'expectedErrors' => [
                'bio' => 'O campo bio deve ser uma string.',
                'whatsapp' => 'O campo whatsapp deve ser uma string.',
                'instagram' => 'O campo instagram deve ser uma string.',
            ],
        ];

        yield 'bio exceeds 500 characters limit' => [
            'data' => [
                'bio' => str_repeat('A', 501),
            ],
            'expectedErrors' => [
                'bio' => 'O campo bio não pode ser superior a 500 caracteres.',
            ],
        ];

        yield 'whatsapp exceeds 255 characters limit' => [
            'data' => [
                'whatsapp' => str_repeat('A', 256),
            ],
            'expectedErrors' => [
                'whatsapp' => 'O campo whatsapp não pode ser superior a 255 caracteres.',
            ],
        ];

        yield 'instagram exceeds 255 characters limit' => [
            'data' => [
                'instagram' => str_repeat('A', 256),
            ],
            'expectedErrors' => [
                'instagram' => 'O campo instagram não pode ser superior a 255 caracteres.',
            ],
        ];

        yield 'avatar is not a file' => [
            'data' => [
                'avatar' => 'not-a-file-string',
            ],
            'expectedErrors' => [
                'avatar' => 'O campo avatar deve ser um arquivo.',
            ],
        ];

        yield 'avatar has invalid extension' => [
            'data' => [
                'avatar' => UploadedFile::fake()->create('documento.pdf', 100),
            ],
            'expectedErrors' => [
                'avatar' => 'O campo avatar deve ser um arquivo do tipo: png, jpg, jpeg.',
            ],
        ];
    }
}
