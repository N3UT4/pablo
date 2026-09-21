<?php
// =====================================================================
// FILE: app/helpers/AbonoHelper.php
// =====================================================================
// DESCRIPCIÓN: Helper para la lógica de negocio de abonos/depósitos según el tamaño del tatuaje.
// Implementa las reglas: abono mínimo por tamaño, validación de monto, cálculo de saldo restante.
// UBICACIÓN MVC: Helper (capa de servicio de dominio)
// =====================================================================

class AbonoHelper
{
    // Valores de abono mínimo por tamaño (en COP)
    private const ABONOS_POR_TAMANO = [
        'pequeño' => 20000,
        'mediano' => 50000,
        'grande'  => 100000,
    ];

    // Tamaños válidos
    private const TAMANOS_VALIDOS = ['pequeño', 'mediano', 'grande'];

    /**
     * Obtiene el abono mínimo requerido según el tamaño del tatuaje.
     *
     * @param string $tamano Tamaño del tatuaje ('pequeño', 'mediano', 'grande')
     * @return int Abono mínimo en COP
     * @throws InvalidArgumentException Si el tamaño no es válido
     */
    public static function obtenerAbonoMinimo(string $tamano): int
    {
        $tamano = strtolower(trim($tamano));

        if (!in_array($tamano, self::TAMANOS_VALIDOS, true)) {
            throw new InvalidArgumentException(
                "Tamaño de tatuaje inválido: '{$tamano}'. Tamaños permitidos: " . implode(', ', self::TAMANOS_VALIDOS)
            );
        }

        return self::ABONOS_POR_TAMANO[$tamano];
    }

    /**
     * Valida que el monto ingresado sea igual al abono mínimo por tamaño
     * O igual al precio total del tatuaje (pago completo).
     *
     * @param float $monto Monto ingresado por el cliente
     * @param string $tamano Tamaño del tatuaje
     * @param float $precioTotal Precio total acordado del tatuaje
     * @return array Resultado de validación con keys: 'valido', 'mensaje', 'tipo_pago'
     */
    public static function validarMontoAbono(float $monto, string $tamano, float $precioTotal): array
    {
        $abonoMinimo = self::obtenerAbonoMinimo($tamano);

        // Redondear a 2 decimales para comparación monetaria
        $monto = round($monto, 2);
        $precioTotal = round($precioTotal, 2);
        $abonoMinimo = round($abonoMinimo, 2);

        // Validar que el monto sea positivo
        if ($monto <= 0) {
            return [
                'valido' => false,
                'mensaje' => 'El monto debe ser mayor a cero.',
                'tipo_pago' => null,
            ];
        }

        // Validar que el monto no exceda el precio total
        if ($monto > $precioTotal) {
            return [
                'valido' => false,
                'mensaje' => "El monto ($monto COP) no puede exceder el precio total del tatuaje ($precioTotal COP).",
                'tipo_pago' => null,
            ];
        }

        // Verificar si es pago del abono mínimo por tamaño
        $esAbonoMinimo = ($monto === $abonoMinimo);

        // Verificar si es pago completo (100% del precio total)
        $esPagoCompleto = ($monto === $precioTotal);

        if (!$esAbonoMinimo && !$esPagoCompleto) {
            return [
                'valido' => false,
                'mensaje' => "El monto debe ser igual al abono mínimo por tamaño ($abonoMinimo COP) O al precio total del tatuaje ($precioTotal COP). Monto ingresado: $monto COP.",
                'tipo_pago' => null,
            ];
        }

        $tipoPago = $esPagoCompleto ? 'pago_completo' : 'abono_minimo';

        return [
            'valido' => true,
            'mensaje' => $esPagoCompleto
                ? "Pago completo del tatuaje registrado ($precioTotal COP)."
                : "Abono mínimo por tamaño '$tamano' registrado ($abonoMinimo COP).",
            'tipo_pago' => $tipoPago,
            'abono_aplicado' => $monto,
            'abono_minimo' => $abonoMinimo,
            'precio_total' => $precioTotal,
        ];
    }

    /**
     * Calcula el saldo restante (pendiente por pagar) después de aplicar el abono.
     * El abono NO reduce el precio total; se registra como saldo a favor.
     *
     * @param float $precioTotal Precio total acordado del tatuaje
     * @param float $totalAbonado Suma de todos los abonos verificados/pendientes
     * @return float Saldo restante por pagar (precio total - total abonado)
     */
    public static function calcularSaldoRestante(float $precioTotal, float $totalAbonado): float
    {
        $saldo = round($precioTotal - $totalAbonado, 2);
        return max(0, $saldo); // No permitir saldo negativo
    }

    /**
     * Genera la respuesta JSON estructurada para el frontend.
     *
     * @param string $tamano Tamaño del tatuaje
     * @param float $monto Monto pagado
     * @param float $precioTotal Precio total del tatuaje
     * @param float $totalAbonadoAnterior Total abonado antes de este pago
     * @return array Respuesta estructurada
     */
    public static function generarRespuestaAbono(
        string $tamano,
        float $monto,
        float $precioTotal,
        float $totalAbonadoAnterior = 0
    ): array {
        $validacion = self::validarMontoAbono($monto, $tamano, $precioTotal);

        if (!$validacion['valido']) {
            return [
                'ok' => false,
                'error' => $validacion['mensaje'],
                'data' => null,
            ];
        }

        $nuevoTotalAbonado = round($totalAbonadoAnterior + $monto, 2);
        $saldoRestante = self::calcularSaldoRestante($precioTotal, $nuevoTotalAbonado);
        $porcentajePagado = $precioTotal > 0 ? round(($nuevoTotalAbonado / $precioTotal) * 100, 2) : 0;

        return [
            'ok' => true,
            'mensaje' => $validacion['mensaje'],
            'data' => [
                'tamano' => strtolower(trim($tamano)),
                'abono_minimo_requerido' => (float) $validacion['abono_minimo'],
                'abono_aplicado' => (float) $validacion['abono_aplicado'],
                'tipo_pago' => $validacion['tipo_pago'], // 'abono_minimo' | 'pago_completo'
                'precio_total_tatuaje' => (float) $precioTotal,
                'total_abonado_acumulado' => (float) $nuevoTotalAbonado,
                'saldo_restante' => (float) $saldoRestante,
                'porcentaje_pagado' => (float) $porcentajePagado,
                'estado_cuenta' => $saldoRestante <= 0 ? 'pagado_total' : 'pendiente',
            ],
        ];
    }

    /**
     * Obtiene la lista de tamaños válidos con sus abonos mínimos.
     * Útil para formularios y documentación.
     *
     * @return array
     */
    public static function obtenerTamanosConAbonos(): array
    {
        return self::ABONOS_POR_TAMANO;
    }

    /**
     * Verifica si un tamaño es válido.
     *
     * @param string $tamano
     * @return bool
     */
    public static function esTamanoValido(string $tamano): bool
    {
        return in_array(strtolower(trim($tamano)), self::TAMANOS_VALIDOS, true);
    }
}