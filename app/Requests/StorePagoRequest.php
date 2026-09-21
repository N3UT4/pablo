<?php

namespace App\Requests;

class StorePagoRequest
{
    private array $data = [];
    private array $errors = [];
    private array $sanitized = [];

    public function __construct(array $input = [])
    {
        $this->data = $input;
    }

    public static function fromGlobals(): self
    {
        $input = $_POST ?? [];
        if (empty($input)) {
            $raw = file_get_contents('php://input');
            $input = json_decode($raw ?: '[]', true) ?? [];
        }
        return new self($input);
    }

    public function validate(): bool
    {
        $this->errors = [];
        $this->sanitized = [];

        $this->validateRequiredFields();
        $this->validateFieldTypes();
        $this->validateBusinessRules();
        $this->sanitizeInputs();

        return empty($this->errors);
    }

    private function validateRequiredFields(): void
    {
        $required = [
            'cita_id' => 'El ID de la cita es obligatorio.',
            'monto' => 'El monto del pago es obligatorio.',
            'metodo' => 'El método de pago es obligatorio.',
        ];

        foreach ($required as $field => $message) {
            if (!isset($this->data[$field]) || $this->data[$field] === '' || $this->data[$field] === null) {
                $this->addError($field, $message);
            }
        }
    }

    private function validateFieldTypes(): void
    {
        if (isset($this->data['cita_id'])) {
            $id = (int) $this->data['cita_id'];
            if ($id <= 0) {
                $this->addError('cita_id', 'El ID de la cita debe ser un número entero positivo.');
            }
        }

        if (isset($this->data['monto'])) {
            $monto = (float) $this->data['monto'];
            if ($monto <= 0) {
                $this->addError('monto', 'El monto debe ser mayor a cero.');
            }
        }
    }

    private function validateBusinessRules(): void
    {
        $metodosValidos = ['nequi', 'transferencia', 'efectivo', 'tarjeta'];
        if (isset($this->data['metodo'])) {
            $metodo = strtolower(trim((string) $this->data['metodo']));
            if (!in_array($metodo, $metodosValidos, true)) {
                $this->addError('metodo', 'Selecciona un método de pago válido: ' . implode(', ', $metodosValidos) . '.');
            }
        }

        if (isset($this->data['metodo'], $this->data['comprobante'])) {
            $metodo = strtolower(trim((string) $this->data['metodo']));
            $comprobante = trim((string) $this->data['comprobante']);
            if ($metodo === 'nequi' && strlen($comprobante) < 4) {
                $this->addError('comprobante', 'Ingresa el número de comprobante de la transferencia por Nequi (mínimo 4 caracteres).');
            }
        }

        if (isset($this->data['estado'])) {
            $estadosValidos = ['pendiente', 'verificado', 'rechazado'];
            $estado = strtolower(trim((string) $this->data['estado']));
            if (!in_array($estado, $estadosValidos, true)) {
                $this->addError('estado', 'Estado de pago inválido. Valores permitidos: ' . implode(', ', $estadosValidos) . '.');
            }
        }
    }

    private function sanitizeInputs(): void
    {
        $this->sanitized = [
            'cita_id' => isset($this->data['cita_id']) ? (int) $this->data['cita_id'] : 0,
            'monto' => isset($this->data['monto']) ? round((float) $this->data['monto'], 2) : 0.0,
            'metodo' => isset($this->data['metodo']) ? strtolower(trim((string) $this->data['metodo'])) : '',
            'comprobante' => isset($this->data['comprobante']) ? trim((string) $this->data['comprobante']) : '',
            'estado' => isset($this->data['estado']) ? strtolower(trim((string) $this->data['estado'])) : 'pendiente',
        ];
    }

    private function addError(string $field, string $message): void
    {
        $this->errors[$field][] = $message;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getFirstError(): ?string
    {
        foreach ($this->errors as $fieldErrors) {
            if (!empty($fieldErrors)) {
                return $fieldErrors[0];
            }
        }
        return null;
    }

    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    public function getSanitized(): array
    {
        return $this->sanitized;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->sanitized[$key] ?? $default;
    }

    public function all(): array
    {
        return $this->sanitized;
    }

    public function only(array $keys): array
    {
        return array_intersect_key($this->sanitized, array_flip($keys));
    }

    public function except(array $keys): array
    {
        return array_diff_key($this->sanitized, array_flip($keys));
    }
}