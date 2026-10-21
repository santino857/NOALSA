<?php

// Funciones de formato para montos, cantidades y fechas (estilo Argentina)

if (! function_exists('a_numero')) {
    /** Convierte "1.5" o "1,5" a float. */
    function a_numero($valor): float
    {
        return (float) str_replace(',', '.', trim((string) $valor));
    }
}

if (! function_exists('dinero')) {
    function dinero($valor): string
    {
        $v        = round((float) $valor, 2);
        $decimales = abs($v - round($v)) > 0.004 ? 2 : 0;

        return ($v < 0 ? '-' : '') . '$ ' . number_format(abs($v), $decimales, ',', '.');
    }
}

if (! function_exists('cantidad')) {
    function cantidad($valor, string $unidad = 'kilo'): string
    {
        $n = rtrim(rtrim(number_format((float) $valor, 3, ',', '.'), '0'), ',');

        return $n . ($unidad === 'kilo' ? ' kg' : ' u.');
    }
}

if (! function_exists('fecha_ar')) {
    function fecha_ar(?string $fecha): string
    {
        return $fecha ? date('d/m/Y', strtotime($fecha)) : '';
    }
}

if (! function_exists('estado_pago')) {
    /** @return array{0:string,1:string} [texto, clase bootstrap] */
    function estado_pago($total, $pagado): array
    {
        $saldo = round((float) $total - (float) $pagado, 2);

        if ($saldo <= 0.004) {
            return ['Pagada', 'success'];
        }
        if ((float) $pagado > 0.004) {
            return ['Pago parcial', 'warning'];
        }

        return ['Debe', 'danger'];
    }
}
