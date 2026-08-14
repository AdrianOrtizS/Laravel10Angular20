<?php

namespace App\Services;

class DocumentValidator
{
    public static function cedula(string $cedula): bool
    {
        if (!preg_match('/^\d{10}$/', $cedula)) {
            return false;
        }
        $provincia = intval(substr($cedula, 0, 2));
        if ($provincia < 1 || $provincia > 24) {
            return false;
        }
        if (intval($cedula[2]) >= 6) {
            return false;
        }
        $coef = [2,1,2,1,2,1,2,1,2];
        $suma = 0;
        for ($i=0;$i<9;$i++) {
            $valor = intval($cedula[$i]) * $coef[$i];
            if ($valor >= 10) {
                $valor -= 9;
            }
            $suma += $valor;
        }
        $digito = (10 - ($suma % 10)) % 10;
        return $digito == intval($cedula[9]);
    }

    public static function ruc(string $ruc): bool
    {
        if (!preg_match('/^\d{13}$/', $ruc)) {
            return false;
        }

        $tercer = intval($ruc[2]);

        // Persona natural
        if ($tercer < 6) {

            if (!self::cedula(substr($ruc,0,10))) {
                return false;
            }

            return substr($ruc,10) === '001';
        }

        // Empresa privada
        if ($tercer == 9) {

            return self::validarSociedadPrivada($ruc);
        }

        // Entidad pública
        if ($tercer == 6) {

            return self::validarEntidadPublica($ruc);
        }

        return false;
    }

    public static function passport(string $passport): bool
    {
        return preg_match('/^[A-Za-z0-9]{3,20}$/', $passport);
    }

    private static function validarSociedadPrivada($ruc): bool
    {
        $coef = [4,3,2,7,6,5,4,3,2];

        $suma = 0;

        for($i=0;$i<9;$i++){
            $suma += intval($ruc[$i]) * $coef[$i];
        }

        $digito = 11 - ($suma % 11);

        if($digito == 11) $digito = 0;
        if($digito == 10) $digito = 1;

        return $digito == intval($ruc[9]) &&
               substr($ruc,10) == "001";
    }

    private static function validarEntidadPublica($ruc): bool
    {
        $coef = [3,2,7,6,5,4,3,2];

        $suma = 0;

        for($i=0;$i<8;$i++){
            $suma += intval($ruc[$i]) * $coef[$i];
        }

        $digito = 11 - ($suma % 11);

        if($digito == 11) $digito = 0;
        if($digito == 10) $digito = 1;

        return $digito == intval($ruc[8]) &&
               substr($ruc,9) == "001";
    }
}