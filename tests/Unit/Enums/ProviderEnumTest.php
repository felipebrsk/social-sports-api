<?php

namespace Tests\Unit\Enums;

use Tests\TestCase;
use App\Enums\ProviderEnum;

class ProviderEnumTest extends TestCase
{
    /**
     * Test if the enum has the correct backing integer values.
     *
     * @return void
     */
    public function test_enum_has_correct_backing_values(): void
    {
        $this->assertEquals(1, ProviderEnum::GOOGLE_ID->value);
    }

    /**
     * Test if the enum cases count is correct to prevent accidental additions.
     *
     * @return void
     */
    public function test_if_enum_has_exactly_the_expected_number_of_cases(): void
    {
        $cases = ProviderEnum::cases();

        $this->assertCount(1, $cases);
    }

    /**
     * Test if the enum can be instantiated from a valid integer value.
     *
     * @return void
     */
    public function test_if_enum_can_be_instantiated_from_valid_integer(): void
    {
        $this->assertInstanceOf(ProviderEnum::class, ProviderEnum::from(1));
    }
}
