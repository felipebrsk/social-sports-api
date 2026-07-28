<?php

namespace Tests\Unit\DTOs;

use Tests\TestCase;
use App\DTOs\ProfileUpdate;
use Illuminate\Http\UploadedFile;

class ProfileUpdateTest extends TestCase
{
    /**
     * Test if the DTO can be successfully instantiated via constructor.
     *
     * @return void
     */
    public function test_if_dto_can_be_instantiated_via_constructor(): void
    {
        $uid = 123;
        $bio = fake()->sentence();
        $instagram = fake()->url();
        $whatsapp = fake()->phoneNumber();
        $avatar = UploadedFile::fake()->create('fake.png');

        $dto = new ProfileUpdate($uid, $bio, $whatsapp, $instagram, $avatar);

        $this->assertInstanceOf(ProfileUpdate::class, $dto);
        $this->assertSame($uid, $dto->uid);
        $this->assertSame($bio, $dto->bio);
        $this->assertSame($avatar, $dto->avatar);
        $this->assertSame($whatsapp, $dto->whatsapp);
        $this->assertSame($instagram, $dto->instagram);
    }

    /**
     * Test if the DTO can be correctly constructed from a valid request array.
     *
     * @return void
     */
    public function test_if_dto_can_be_correctly_created_from_request_array(): void
    {
        $uid = 123;

        $data = [
            'bio' => fake()->sentence(),
            'instagram' => fake()->url(),
            'whatsapp' => fake()->phoneNumber(),
            'avatar' => UploadedFile::fake()->create('fake.png'),
        ];

        $dto = ProfileUpdate::fromRequest($uid, $data);

        $this->assertInstanceOf(ProfileUpdate::class, $dto);
        $this->assertSame($uid, $dto->uid);
        $this->assertSame($data['bio'], $dto->bio);
        $this->assertSame($data['avatar'], $dto->avatar);
        $this->assertSame($data['whatsapp'], $dto->whatsapp);
        $this->assertSame($data['instagram'], $dto->instagram);
    }

    /**
     * Test if the DTO applies empty string fallbacks when request keys are missing.
     *
     * @return void
     */
    public function test_if_dto_applies_empty_string_fallbacks_when_keys_are_missing(): void
    {
        $data = [];

        $dto = ProfileUpdate::fromRequest(123, $data);

        $this->assertInstanceOf(ProfileUpdate::class, $dto);
        $this->assertNull($dto->bio);
        $this->assertNull($dto->avatar);
        $this->assertNull($dto->whatsapp);
        $this->assertNull($dto->instagram);
    }
}
