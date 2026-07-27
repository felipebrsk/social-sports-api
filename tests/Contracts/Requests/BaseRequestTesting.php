<?php

namespace Tests\Contracts\Requests;

use Mockery;
use LogicException;
use Tests\TestCase;
use ReflectionMethod;
use Mockery\MockInterface;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\{
    Factory,
    DatabasePresenceVerifierInterface,
};

abstract class BaseRequestTesting extends TestCase implements ShouldTestRequest
{
    /**
     * The validation rules for the request.
     *
     * @var array<string, mixed>
     */
    protected array $rules = [];

    /**
     * The validation messages for the request.
     *
     * @var array<string, mixed>
     */
    protected array $messages = [];

    /**
     * The validation attributes for the request.
     *
     * @var array<string, mixed>
     */
    protected array $attributes = [];

    /**
     * The validator instance.
     */
    protected Factory $validator;

    /**
     * The initialized request object instance.
     *
     * @var FormRequest|(FormRequest&MockInterface)|null
     */
    protected $requestObject = null;

    /**
     * Property to store the database presence verifier mock.
     *
     * @var (DatabasePresenceVerifierInterface&MockInterface)|null
     */
    protected $presenceVerifierMock = null;

    /**
     * Simulated route parameters (e.g., ['id' => 1]).
     *
     * @var array<string, mixed>
     */
    protected array $routeParameters = [];

    /**
     * Get the request class name.
     *
     * @var class-string<FormRequest>
     */
    protected string $request;

    /**
     * Setup test environment for request validation.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->validator = $this->app['validator'];
    }

    /**
     * Define route parameters to simulate updates (e.g., ignore ID X).
     *
     * @param array<string, mixed> $parameters
     */
    protected function setRouteParameters(array $parameters): void
    {
        $this->routeParameters = $parameters;
    }

    /**
     * Validate multiple fields based on the request's rules.
     *
     * @param  array<string, mixed>  $data
     */
    protected function validate(array $data, bool $skipPreparation = false): bool
    {
        $request = $this->initializeRequestWithData($data, $skipPreparation);

        $finalData = $skipPreparation ? $data : $request->all();

        $validation = $this->validator->make($finalData, $this->rules, $this->messages, $this->attributes);

        if ($this->presenceVerifierMock) {
            $validation->setPresenceVerifier($this->presenceVerifierMock);
        }

        return $validation->passes();
    }

    /**
     * Get validation errors for the provided data.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, array<string>>
     */
    protected function getValidationErrors(array $data, bool $skipPreparation = false): array
    {
        $request = $this->initializeRequestWithData($data, $skipPreparation);

        $finalData = $skipPreparation ? $data : $request->all();

        $validation = $this->validator->make($finalData, $this->rules, $this->messages, $this->attributes);

        if ($this->presenceVerifierMock) {
            $validation->setPresenceVerifier($this->presenceVerifierMock);
        }

        return $validation->errors()->toArray();
    }

    /**
     * Creates and injects a mock of DatabasePresenceVerifierInterface.
     *
     * @param int|callable $returnCount
     */
    protected function mockPresenceVerifier(int|callable $returnCount): void
    {
        /** @var DatabasePresenceVerifierInterface&MockInterface $mock */
        $mock = $this->mock(DatabasePresenceVerifierInterface::class);

        $this->presenceVerifierMock = $mock;
        $this->presenceVerifierMock->shouldReceive('setConnection')->atLeast()->andReturnSelf();

        if (is_callable($returnCount)) {
            $this->presenceVerifierMock->shouldReceive('getCount')->andReturnUsing($returnCount);
        } else {
            $this->presenceVerifierMock->shouldReceive('getCount')->andReturn($returnCount);
        }

        $this->app['validator']->setPresenceVerifier($this->presenceVerifierMock);
    }

    /**
     * Reload request validation rules.
     */
    protected function reloadRequestRules(): void
    {
        $this->initializeRequestWithData([]);
    }

    /**
     * Initializes the Request object, runs prepareForValidation, and extracts rules.
     *
     * @param array<string, mixed> $data
     * @return FormRequest
     */
    private function initializeRequestWithData(array $data, bool $skipPreparation = false): FormRequest
    {
        /** @var class-string<FormRequest> $requestClass */
        $requestClass = $this->request();

        if (! empty($this->routeParameters)) {
            /** @var FormRequest&MockInterface $partialMock */
            $partialMock = Mockery::mock($requestClass)->makePartial();
            $partialMock
                ->shouldReceive('route')
                ->andReturnUsing(function (?string $param = null, mixed $default = null) {
                    if ($param === null) {
                        return (object) $this->routeParameters;
                    }

                    return $this->routeParameters[$param] ?? $default;
                });

            $this->requestObject = $partialMock;
        } else {
            $this->requestObject = new $requestClass();
        }

        $this->requestObject->initialize($data);
        $this->requestObject->setContainer($this->app);

        if (! $skipPreparation) {
            $method = new ReflectionMethod($this->requestObject, 'prepareForValidation');
            $method->invoke($this->requestObject);
        }

        if (method_exists($this->requestObject, 'rules')) {
            /** @var array<string, mixed> $rules */
            $rules = $this->requestObject->rules();

            $this->rules = $rules;

            $this->messages = $this->requestObject->messages();
            $this->attributes = $this->requestObject->attributes();
        } else {
            throw new LogicException('The request class must implement a rules method.');
        }

        return $this->requestObject;
    }
}
