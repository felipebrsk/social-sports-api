<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\{
    Arr,
    Str,
};

class BaseRequest extends FormRequest
{
    /**
     * Fields that should be converted into booleans.
     *
     * @var array<int, string> $booleans
     */
    protected array $booleans = [];

    /**
     * {@inheritDoc}
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    protected function prepareForValidation(): void
    {
        $this->castBooleanFields();
    }

    /**
     * Coverts the defined boolean fields into real booleans with wildcards.
     *
     * @return void
     */
    protected function castBooleanFields(): void
    {
        if (empty($this->booleans)) {
            return;
        }

        $data = $this->all();

        $flatData = Arr::dot($data);
        $hasChanges = false;

        foreach ($flatData as $key => $value) {
            foreach ($this->booleans as $pattern) {
                if (Str::is($pattern, $key)) {
                    if ($value !== null) {
                        $castedValue = filter_var(
                            $value,
                            FILTER_VALIDATE_BOOL,
                            FILTER_NULL_ON_FAILURE,
                        );

                        if ($castedValue !== null) {
                            data_set($data, $key, $castedValue);

                            $hasChanges = true;
                        }
                    }

                    break;
                }
            }
        }

        if ($hasChanges) {
            $this->replace((array) $data);
        }
    }
}
