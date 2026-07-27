<?php

declare(strict_types=1);

namespace Tests\Unit\Traits;

use Mockery;
use Tests\TestCase;
use App\Traits\HasSlug;
use Mockery\MockInterface;
use Illuminate\Database\Eloquent\{
    Model,
    Builder,
};

/**
 * Class HasSlugUnitTest
 *
 * Isolated unit tests for the HasSlug trait without database interactions.
 *
 * @package Tests\Unit\Traits
 */
class HasSlugTest extends TestCase
{
    /**
     * Test if a slug is generated correctly when no collisions exist in the database.
     *
     * @return void
     */
    public function test_it_generates_slug_when_no_collision(): void
    {
        /** @var DummyUnitModel&MockInterface $model */
        $model = Mockery::mock(DummyUnitModel::class)->makePartial();

        $builder = Mockery::mock(Builder::class);

        $model
            ->shouldReceive('getAttribute')
            ->with('name')
            ->andReturn('Hello World Test');
        $model
            ->shouldReceive('getAttribute')
            ->with('slug')
            ->andReturnNull();
        $model
            ->shouldReceive('newQuery')
            ->once()
            ->andReturn($builder);

        $builder
            ->shouldReceive('where')
            ->once()
            ->with('slug', 'hello-world-test')
            ->andReturnSelf();
        $builder
            ->shouldReceive('exists')
            ->once()
            ->andReturnFalse();

        $model
            ->shouldReceive('setAttribute')
            ->once()
            ->with('slug', 'hello-world-test');

        $model->triggerSlugGeneration();
    }

    /**
     * Test if a suffix (-1) is appended to the slug when a collision is detected.
     *
     * @return void
     */
    public function test_it_appends_suffix_when_slug_collides(): void
    {
        /** @var DummyUnitModel&MockInterface $model */
        $model = Mockery::mock(DummyUnitModel::class)->makePartial();

        $builder = Mockery::mock(Builder::class);

        $model
            ->shouldReceive('getAttribute')
            ->with('name')
            ->andReturn('Duplicate Title');
        $model
            ->shouldReceive('getAttribute')
            ->with('slug')
            ->andReturnNull();
        $model
            ->shouldReceive('newQuery')
            ->twice()
            ->andReturn($builder);

        $builder
            ->shouldReceive('where')
            ->once()
            ->with('slug', 'duplicate-title')
            ->andReturnSelf();
        $builder
            ->shouldReceive('where')
            ->once()
            ->with('slug', 'duplicate-title-1')
            ->andReturnSelf();
        $builder
            ->shouldReceive('exists')
            ->twice()
            ->andReturnValues([true, false]);

        $model
            ->shouldReceive('setAttribute')
            ->once()
            ->with('slug', 'duplicate-title-1');

        $model->triggerSlugGeneration();
    }

    /**
     * Test that slug generation is skipped when destination slug is already present and updates are disabled.
     *
     * @return void
     */
    public function test_it_skips_generation_if_slug_exists_and_should_not_update(): void
    {
        /** @var DummyUnitModel&MockInterface $model */
        $model = Mockery::mock(DummyUnitModel::class)->makePartial();

        $model
            ->shouldReceive('getAttribute')
            ->with('slug')
            ->andReturn('already-existing-slug');

        $model->shouldReceive('newQuery')->never();
        $model->shouldReceive('setAttribute')->never();

        $model->triggerSlugGeneration();
    }

    /**
     * Test if multiple source columns are correctly formatted into a single slug.
     *
     * @return void
     */
    public function test_it_combines_multiple_source_columns(): void
    {
        /** @var CustomUnitModel&MockInterface $model */
        $model = Mockery::mock(CustomUnitModel::class)->makePartial();

        $builder = Mockery::mock(Builder::class);

        $model
            ->shouldReceive('getAttribute')
            ->with('first_name')
            ->andReturn('John');
        $model
            ->shouldReceive('getAttribute')
            ->with('last_name')
            ->andReturn('Doe');
        $model
            ->shouldReceive('getAttribute')
            ->with('custom_url')
            ->andReturnNull();
        $model
            ->shouldReceive('newQuery')
            ->once()
            ->andReturn($builder);

        $builder
            ->shouldReceive('where')
            ->once()
            ->with('custom_url', 'john-doe')
            ->andReturnSelf();
        $builder
            ->shouldReceive('exists')
            ->once()
            ->andReturnFalse();

        $model
            ->shouldReceive('setAttribute')
            ->once()
            ->with('custom_url', 'john-doe');

        $model->triggerSlugGeneration();
    }
}

class DummyUnitModel extends Model
{
    use HasSlug;

    public $exists = false;

    public function triggerSlugGeneration(): void
    {
        $this->generateAndSetSlug();
    }
}

class CustomUnitModel extends Model
{
    use HasSlug;

    public $exists = false;

    public function triggerSlugGeneration(): void
    {
        $this->generateAndSetSlug();
    }

    /**
     * @return array<int, string>
     */
    protected function getSlugSourceColumns(): array
    {
        return ['first_name', 'last_name'];
    }

    protected function getSlugDestinationColumn(): string
    {
        return 'custom_url';
    }
}
