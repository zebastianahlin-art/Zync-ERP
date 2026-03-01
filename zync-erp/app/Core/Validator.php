<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Minimal input validator.
 *
 * Usage:
 *   $v = new Validator($request->body);
 *   $v->required('email')->email('email')->maxLength('name', 100);
 *   if ($v->fails()) {
 *       return $this->render('form', ['errors' => $v->errors()]);
 *   }
 */
class Validator
{
    /** @var array<string, string> */
    private array $errors = [];

    /** @param array<string, mixed> $data */
    public function __construct(private readonly array $data) {}

    /** Ensure the field is present and non-empty. */
    public function required(string $field, string $label = ''): static
    {
        $label = $label ?: $field;
        $value = trim((string) ($this->data[$field] ?? ''));

        if ($value === '') {
            $this->errors[$field] = "{$label} is required.";
        }

        return $this;
    }

    /** Ensure the field contains a valid e-mail address. */
    public function email(string $field, string $label = ''): static
    {
        $label = $label ?: $field;
        $value = trim((string) ($this->data[$field] ?? ''));

        if ($value !== '' && filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
            $this->errors[$field] = "{$label} must be a valid email address.";
        }

        return $this;
    }

    /** Ensure the field does not exceed $max characters. */
    public function maxLength(string $field, int $max, string $label = ''): static
    {
        $label = $label ?: $field;
        $value = (string) ($this->data[$field] ?? '');

        if (mb_strlen($value) > $max) {
            $this->errors[$field] = "{$label} must not exceed {$max} characters.";
        }

        return $this;
    }

    /** Returns true when at least one validation rule failed. */
    public function fails(): bool
    {
        return $this->errors !== [];
    }

    /**
     * Return all field errors.
     *
     * @return array<string, string>
     */
    public function errors(): array
    {
        return $this->errors;
    }
}
