<?php

namespace App\Models\Concerns;

trait EncryptsRouteKey
{
    public function getRouteKey(): mixed
    {
        return eid($this->getKey());
    }

    public function resolveRouteBinding($value, $field = null): ?static
    {
        if ($field === null || $field === $this->getRouteKeyName()) {
            $value = did((string) $value);
        }

        return parent::resolveRouteBinding($value, $field);
    }
}
