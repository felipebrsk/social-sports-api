<?php

namespace Tests\Unit\Models;

use App\Models\Payment;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Tests\Contracts\Models\{
    ShouldTestCasts,
    ShouldTestTable,
    BaseModelTesting,
    ShouldTestTraits,
    ShouldTestGuarded,
    ShouldTestRelations,
    ShouldTestFillables,
};

class PaymentTest extends BaseModelTesting implements
    ShouldTestCasts,
    ShouldTestTable,
    ShouldTestTraits,
    ShouldTestGuarded,
    ShouldTestFillables,
    ShouldTestRelations
{
    /**
     * @inheritDoc
     */
    public function model(): string
    {
        return Payment::class;
    }

    /**
     * @inheritDoc
     */
    public function test_fillable_attributes(): void
    {
        $fillable = [
            'amount',
            'gateway',
            'user_id',
            'pix_qr_code',
            'game_session_id',
            'payment_type_id',
            'payment_status_id',
            'gateway_transaction_id',
        ];

        $this->assertHasFillables($fillable);
    }

    /**
     * @inheritDoc
     */
    public function test_casts_attributes(): void
    {
        $casts = [
            'id' => 'int',
            'user_id' => 'int',
            'payment_type_id' => 'int',
            'game_session_id' => 'int',
            'payment_status_id' => 'int',
        ];

        $this->assertHasCasts($casts);
    }

    /**
     * @inheritDoc
     */
    public function test_relations_attributes(): void
    {
        $relations = [
            'user' => BelongsTo::class,
            'type' => BelongsTo::class,
            'status' => BelongsTo::class,
            'gameSession' => BelongsTo::class,
        ];

        $this->assertHasRelations($relations);
    }

    /**
     * @inheritDoc
     */
    public function test_guarded_attributes(): void
    {
        $guarded = [
            '*',
        ];

        $this->assertHasGuarded($guarded);
    }

    /**
     * @inheritDoc
     */
    public function test_table_attribute(): void
    {
        $table = 'payments';

        $this->assertHasTable($table);
    }

    /**
     * @inheritDoc
     */
    public function test_traits_attributes(): void
    {
        $traits = [
            HasFactory::class,
        ];

        $this->assertUsesTraits($traits);
    }
}
