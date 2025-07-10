<?php
namespace App\Core;

class Validator {

    private static array $errors = [];

    private function __construct() {}

    public static function getErrors(): array {
        return self::$errors;
    }

    public static function addError(string $field, string $message): void {
        self::$errors[$field] = $message;
    }

    public static function isValid(): bool {
        return empty(self::$errors);
    }

    public static function isEmpty(string $field, $value)
    {
        if (trim($value) === '') {
            self::$errors[$field] = 'Ce champ est obligatoire.';
        }
    }

    public static function isEmail(string $field, string $value) {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            self::addError($field, 'Le champ '. $field . ' doit être une adresse e-mail valide.');
        }
    }
}