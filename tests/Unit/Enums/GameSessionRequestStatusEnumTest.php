<?php

namespace Tests\Unit\Enums;

use ReflectionEnum;
use Tests\TestCase;
use App\Enums\GameSessionRequestStatusEnum;
use App\Contracts\Enums\EnumWithLabelInterface;

class GameSessionRequestStatusEnumTest extends TestCase
{
    /**
     * Test if the enum has the correct backing integer values.
     *
     * @return void
     */
    public function test_enum_has_correct_backing_values(): void
    {
        $this->assertEquals(1, GameSessionRequestStatusEnum::PENDING->value);
        $this->assertEquals(2, GameSessionRequestStatusEnum::APPROVED->value);
        $this->assertEquals(3, GameSessionRequestStatusEnum::REFUSED->value);
    }

    /**
     * Test if the enum implements the correct label interface.
     *
     * @return void
     */
    public function test_enum_implements_enum_with_label_interface(): void
    {
        $reflection = new ReflectionEnum(GameSessionRequestStatusEnum::class);

        $this->assertTrue(
            $reflection->implementsInterface(EnumWithLabelInterface::class),
        );
    }

    /**
     * Test if the enum cases count is correct to prevent accidental additions.
     *
     * @return void
     */
    public function test_if_enum_has_exactly_the_expected_number_of_cases(): void
    {
        $cases = GameSessionRequestStatusEnum::cases();

        $this->assertCount(3, $cases);
    }

    /**
     * Test if the enum can be instantiated from a valid integer value.
     *
     * @return void
     */
    public function test_if_enum_can_be_instantiated_from_valid_integer(): void
    {
        $this->assertInstanceOf(GameSessionRequestStatusEnum::class, GameSessionRequestStatusEnum::from(1));
        $this->assertInstanceOf(GameSessionRequestStatusEnum::class, GameSessionRequestStatusEnum::from(2));
        $this->assertInstanceOf(GameSessionRequestStatusEnum::class, GameSessionRequestStatusEnum::from(3));
    }

    /**
     * Test if can correctly get the friendly label for the enum value.
     *
     * @return void
     */
    public function test_if_can_correctly_get_the_friendly_label_for_the_enum_value(): void
    {
        $this->assertEquals('Aprovado', GameSessionRequestStatusEnum::APPROVED->label());
        $this->assertEquals('Pendente', GameSessionRequestStatusEnum::PENDING->label());
        $this->assertEquals('Recusado', GameSessionRequestStatusEnum::REFUSED->label());
    }
}
