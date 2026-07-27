<?php

namespace App\Contracts\Services;

use Illuminate\Http\JsonResponse;

interface ResponseServiceInterface
{
    /**
     * Convert response to JSON.
     *
     * @param int $code
     * @return JsonResponse
     */
    public function toJson(int $code = 200): JsonResponse;

    /**
     * Set the message.
     *
     * @param string $message
     * @return self
     */
    public function setMessage(string $message): self;

    /**
     * Set the content.
     *
     * @param mixed $content
     * @return self
     */
    public function setContent(mixed $content): self;

    /**
     * Convert to message array.
     *
     * @return array{message: string}
     */
    public function toMessage(): array;

    /**
     * Convert to array.
     *
     * @return array{data: mixed}
     */
    public function toArray(): array;

    /**
     * Removes the 'data' wrapper on JSON response.
     *
     * @return self
     */
    public function unwrap(): self;
}
