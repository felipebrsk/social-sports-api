<?php

namespace Tests;

use ReflectionClass;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * The storage fake disk.
     *
     * @var string
     */
    public string $disk = 'fakeAws';

    /**
     * Setup test environments.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(
            Carbon::now(),
        );

        Storage::fake($this->disk);
    }

    /**
     * Get a protected or private property from an object.
     *
     * @param object $object
     * @param string $property
     * @return mixed
     */
    protected function getProtectedProperty(object $object, string $property): mixed
    {
        $reflection = new ReflectionClass($object);
        $property = $reflection->getProperty($property);

        return $property->getValue($object);
    }

    /**
     * Call a protected or private method from an object.
     *
     * @param object $object
     * @param string $method
     * @param array<mixed> $args
     */
    protected function callProtectedMethod(object $object, string $method, array $args = []): mixed
    {
        $reflection = new ReflectionClass($object);
        $method = $reflection->getMethod($method);

        return $method->invokeArgs($object, $args);
    }

    /**
     * Tear down application test environments.
     *
     * @return void
     */
    public function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }
}
