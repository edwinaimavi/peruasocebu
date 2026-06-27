<?php

namespace App\Models\Concerns;

trait DecodesTextValues
{
    public function getAttributeValue($key)
    {
        $value = parent::getAttributeValue($key);

        if (is_string($value) && in_array($key, $this->decodedTextAttributes(), true)) {
            return $this->decodeTextValue($value);
        }

        return $value;
    }

    public function setAttribute($key, $value)
    {
        if (is_string($value) && in_array($key, $this->decodedTextAttributes(), true)) {
            $value = $this->decodeTextValue($value);
        }

        return parent::setAttribute($key, $value);
    }

    protected function decodedTextAttributes(): array
    {
        return property_exists($this, 'decodedTextAttributes')
            ? $this->decodedTextAttributes
            : [];
    }

    protected function decodeTextValue(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $decoded = $value;

        for ($i = 0; $i < 3; $i++) {
            $next = html_entity_decode($decoded, ENT_QUOTES | ENT_HTML5, 'UTF-8');

            if ($next === $decoded) {
                break;
            }

            $decoded = $next;
        }

        return $decoded;
    }
}
