<?php

namespace App\Helpers;

use App\Core\Database;

class Validator {
    private array $data;
    private array $rules;
    private array $errors = [];

    public function __construct(array $data, array $rules) {
        $this->data = $data;
        $this->rules = $rules;
    }

    public static function make(array $data, array $rules): self {
        $validator = new self($data, $rules);
        $validator->validate();
        return $validator;
    }

    public function validate(): bool {
        $this->errors = [];
        $pdo = Database::getInstance();

        foreach ($this->rules as $field => $ruleString) {
            $rulesList = is_array($ruleString) ? $ruleString : explode('|', $ruleString);
            $value = $this->data[$field] ?? null;

            foreach ($rulesList as $rule) {
                $params = [];
                if (str_contains($rule, ':')) {
                    [$rule, $paramStr] = explode(':', $rule, 2);
                    $params = explode(',', $paramStr);
                }

                if ($rule === 'required' && (is_null($value) || trim((string)$value) === '')) {
                    $this->addError($field, "The {$this->formatField($field)} field is required.");
                    break;
                }

                if ($value === null || $value === '') {
                    continue; // Skip further checks if empty and not required
                }

                if ($rule === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addError($field, "The {$this->formatField($field)} must be a valid email address.");
                }

                if ($rule === 'min') {
                    $min = (int) ($params[0] ?? 0);
                    if (is_numeric($value)) {
                        if ($value < $min) {
                            $this->addError($field, "The {$this->formatField($field)} must be at least {$min}.");
                        }
                    } elseif (mb_strlen((string)$value) < $min) {
                        $this->addError($field, "The {$this->formatField($field)} must be at least {$min} characters.");
                    }
                }

                if ($rule === 'max') {
                    $max = (int) ($params[0] ?? 0);
                    if (is_numeric($value)) {
                        if ($value > $max) {
                            $this->addError($field, "The {$this->formatField($field)} must not exceed {$max}.");
                        }
                    } elseif (mb_strlen((string)$value) > $max) {
                        $this->addError($field, "The {$this->formatField($field)} must not exceed {$max} characters.");
                    }
                }

                if ($rule === 'numeric' && !is_numeric($value)) {
                    $this->addError($field, "The {$this->formatField($field)} must be a number.");
                }

                if ($rule === 'in') {
                    if (!in_array($value, $params, true)) {
                        $this->addError($field, "The selected {$this->formatField($field)} is invalid.");
                    }
                }

                if ($rule === 'date') {
                    $d = \DateTime::createFromFormat('Y-m-d', $value);
                    if (!$d || $d->format('Y-m-d') !== $value) {
                        $this->addError($field, "The {$this->formatField($field)} must be a valid date (YYYY-MM-DD).");
                    }
                }

                if ($rule === 'time') {
                    if (!preg_match('/^([01]\d|2[0-3]):[0-5]\d(:[0-5]\d)?$/', $value)) {
                        $this->addError($field, "The {$this->formatField($field)} must be a valid time (HH:MM or HH:MM:SS).");
                    }
                }

                if ($rule === 'unique') {
                    // unique:table,column,ignoreId
                    $table = $params[0] ?? '';
                    $column = $params[1] ?? $field;
                    $ignoreId = $params[2] ?? null;

                    $query = "SELECT COUNT(*) FROM `{$table}` WHERE `{$column}` = ?";
                    $binds = [$value];

                    if ($ignoreId !== null && $ignoreId !== '') {
                        $query .= " AND id != ?";
                        $binds[] = $ignoreId;
                    }

                    $stmt = $pdo->prepare($query);
                    $stmt->execute($binds);
                    if ($stmt->fetchColumn() > 0) {
                        $this->addError($field, "The {$this->formatField($field)} has already been taken.");
                    }
                }
            }
        }

        return empty($this->errors);
    }

    private function addError(string $field, string $message): void {
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = $message;
        }
    }

    private function formatField(string $field): string {
        return str_replace(['_', '-'], ' ', $field);
    }

    public function passes(): bool {
        return empty($this->errors);
    }

    public function fails(): bool {
        return !empty($this->errors);
    }

    public function errors(): array {
        return $this->errors;
    }

    public function firstError(): ?string {
        return !empty($this->errors) ? reset($this->errors) : null;
    }
}
