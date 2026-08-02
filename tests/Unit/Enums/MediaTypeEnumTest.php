<?php

namespace Tests\Unit\Enums;

use Tests\TestCase;
use App\Enums\MediaTypeEnum;

class MediaTypeEnumTest extends TestCase
{
    /**
     * Test if the enum has the correct backing integer values.
     *
     * @return void
     */
    public function test_enum_has_correct_backing_values(): void
    {
        $this->assertEquals(1, MediaTypeEnum::IMAGE_TYPE_ID->value);
        $this->assertEquals(2, MediaTypeEnum::FILE_TYPE_ID->value);
        $this->assertEquals(3, MediaTypeEnum::VIDEO_TYPE_ID->value);
        $this->assertEquals(4, MediaTypeEnum::LINK_TYPE_ID->value);
    }

    /**
     * Test if the enum cases count is correct to prevent accidental additions.
     *
     * @return void
     */
    public function test_if_enum_has_exactly_the_expected_number_of_cases(): void
    {
        $cases = MediaTypeEnum::cases();

        $this->assertCount(4, $cases);
    }

    /**
     * Test if the enum can be instantiated from a valid integer value.
     *
     * @return void
     */
    public function test_if_enum_can_be_instantiated_from_valid_integer(): void
    {
        $this->assertInstanceOf(MediaTypeEnum::class, MediaTypeEnum::from(1));
        $this->assertInstanceOf(MediaTypeEnum::class, MediaTypeEnum::from(2));
        $this->assertInstanceOf(MediaTypeEnum::class, MediaTypeEnum::from(3));
        $this->assertInstanceOf(MediaTypeEnum::class, MediaTypeEnum::from(4));
    }
}
