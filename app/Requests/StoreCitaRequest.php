<?php

namespace App\Requests;

use App\Validation\DateAfterThreeDaysRule;

class StoreCitaRequest
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
        return new self($_POST ?? []);
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
            'id_tatuador' => 'El tatuador es obligatorio.',
            'tipo_servicio' => 'El tipo de servicio es obligatorio.',
            'tamano' => 'El tamaño del tatuaje es obligatorio.',
            'fecha_cita' => 'La fecha de la cita es obligatoria.',
            'hora_cita' => 'La hora de la cita es obligatoria.',
            'monto' => 'El monto es obligatorio.',
            'metodo_pago' => 'El método de pago es obligatorio.',
        ];

        foreach ($required as $field => $message) {
            if (!isset($this->data[$field]) || $this->data[$field] === '' || $this->data[$field] === null) {
                $this->addError($field, $message);
            }
        }
    }

    private function validateFieldTypes(): void
    {
        if (isset($this->data['id_tatuador'])) {
            $id = (int) $this->data['id_tatuador'];
            if ($id <= 0) {
                $this->addError('id_tatuador', 'El tatuador seleccionado no es válido.');
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
        $tamanoValidos = ['pequeño', 'mediano', 'grande'];
        if (isset($this->data['tamano'])) {
            $tamano = strtolower(trim((string) $this->data['tamano']));
            if (!in_array($tamano, $tamanoValidos, true)) {
                $this->addError('tamano', "Tamaño de tatuaje inválido. Debe ser: " . implode(', ', $tamanoValidos) . ".");
            }
        }

        $metodosValidos = ['nequi', 'transferencia', 'efectivo', 'tarjeta'];
        if (isset($this->data['metodo_pago'])) {
            $metodo = strtolower(trim((string) $this->data['metodo_pago']));
            if (!in_array($metodo, $metodosValidos, true)) {
                $this->addError('metodo_pago', 'Selecciona un método de pago válido: ' . implode(', ', $metodosValidos) . '.');
            }
        }

        if (isset($this->data['fecha_cita'])) {
            $rule = new DateAfterThreeDaysRule();
            if (!$rule->passes('fecha_cita', $this->data['fecha_cita'])) {
                $this->addError('fecha_cita', $rule->message());
            }
        }

        if (isset($this->data['metodo_pago'], $this->data['comprobante'])) {
            $metodo = strtolower(trim((string) $this->data['metodo_pago']));
            $comprobante = trim((string) $this->data['comprobante']);
            if ($metodo === 'nequi' && strlen($comprobante) < 4) {
                $this->addError('comprobante', 'Ingresa el número de comprobante de la transferencia por Nequi (mínimo 4 caracteres).');
            }
        }
    }

    private function sanitizeInputs(): void
    {
        $this->sanitized = [
            'id_tatuador' => isset($this->data['id_tatuador']) ? (int) $this->data['id_tatuador'] : 0,
            'tipo_servicio' => isset($this->data['tipo_servicio']) ? trim((string) $this->data['tipo_servicio']) : '',
            'tamano' => isset($this->data['tamano']) ? strtolower(trim((string) $this->data['tamano'])) : '',
            'detalle_personalizado' => isset($this->data['detalle_personalizado']) ? trim((string) $this->data['detalle_personalizado']) : '',
            'observaciones' => isset($this->data['observaciones']) ? trim((string) $this->data['observaciones']) : '',
            'fecha_cita' => isset($this->data['fecha_cita']) ? trim((string) $this->data['fecha_cita']) : '',
            'hora_cita' => isset($this->data['hora_cita']) ? trim((string) $this->data['hora_cita']) : '',
            'monto' => isset($this->data['monto']) ? round((float) $this->data['monto'], 2) : 0.0,
            'metodo_pago' => isset($this->data['metodo_pago']) ? strtolower(trim((string) $this->data['metodo_pago'])) : '',
            'comprobante' => isset($this->data['comprobante']) ? trim((string) $this->data['comprobante']) : '',
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