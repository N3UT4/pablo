<?php

namespace App\Validation;

class DateAfterThreeDaysRule
{
    public function passes(string $attribute, mixed $value): bool
    {
        if (!is_string($value) || $value === '') {
            return false;
        }

        $inputDate = \DateTime::createFromFormat('Y-m-d', $value);
        if (!$inputDate) {
            return false;
        }

        $minDate = new \DateTime('now');
        $minDate->add(new \DateInterval('P3D'));
        $minDate->setTime(0, 0, 0);

        return $inputDate >= $minDate;
    }

    public function message(): string
    {
        $minDate = (new \DateTime('now'))->add(new \DateInterval('P3D'))->format('d/m/Y');
        return "La fecha de la cita debe ser posterior al {$minDate} (mínimo 3 días hábiles).";
    }
}