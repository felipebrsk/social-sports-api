<?php

namespace App\Services;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use App\Contracts\Services\ResponseServiceInterface;

class ResponseService implements ResponseServiceInterface
{
    /**
     * The message content.
     */
    public string $message;

    /**
     * The response content.
     */
    public mixed $content;

    /**
     * Determines if the content should be wrapped on 'data' key.
     */
    protected bool $wrap = true;

    /**
     * Create a new response instance.
     *
     * @return void
     */
    public function __construct(string $message = '')
    {
        $this->message = $message;
    }

    /**
     * {@inheritDoc}
     */
    public function toJson(int $code = 200): JsonResponse
    {
        if ($this->message) {
            return response()->json($this->toMessage(), $code);
        }

        if (! $this->wrap) {
            return response()->json($this->content, $code);
        }

        return response()->json($this->toArray(), $code);
    }

    /**
     * {@inheritDoc}
     */
    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setContent(mixed $content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function toMessage(): array
    {
        return [
            'message' => $this->message,
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function toArray(): array
    {
        $data = $this->content;

        if ($data instanceof Collection) {
            $data = $data->toArray();
        }

        return [
            'data' => $data,
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function unwrap(): self
    {
        $this->wrap = false;

        return $this;
    }
}
