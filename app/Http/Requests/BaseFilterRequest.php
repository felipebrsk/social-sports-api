<?php

namespace App\Http\Requests;

use function in_array;
use function is_array;

abstract class BaseFilterRequest extends BaseRequest
{
    /**
     * {@inheritDoc}
     */
    protected function prepareForValidation(): void
    {
        parent::prepareForValidation();

        if (! $this->has('filter_by')) {
            $this->merge(['filter_by' => $this->all()]);
        }

        if ($filters = $this->input('filter_by')) {
            if (is_array($filters)) {
                $this->merge($filters);
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function validated(mixed $key = null, mixed $default = null): mixed
    {
        $flatData = parent::validated();

        if ($key !== null) {
            return data_get($flatData, $key, $default);
        }

        $structuredData = [];

        $nonFilterKeys = ['per_page', 'sort_by', 'sort_order', 'limit'];

        foreach ($flatData as $paramKey => $paramValue) {
            if (in_array($paramKey, $nonFilterKeys)) {
                $structuredData[$paramKey] = $paramValue;
            } else {
                $structuredData['filter_by'][$paramKey] = $paramValue;
            }
        }

        return $structuredData;
    }
}
