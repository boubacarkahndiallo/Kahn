<?php

namespace App\Traits;

use libphonenumber\PhoneNumberUtil;
use libphonenumber\NumberParseException;

trait PhoneNumberValidator
{
    protected function validatePhoneNumber($number)
    {
        $phoneUtil = PhoneNumberUtil::getInstance();
        try {
            // Parser le numéro en spécifiant la Guinée (GN) comme région par défaut
            // Cela permet de gérer les numéros locaux (ex: 61234567) et internationaux
            $phoneNumber = $phoneUtil->parse($number, 'GN');

            // Vérifier si le numéro est valide
            return $phoneUtil->isValidNumber($phoneNumber);
        } catch (NumberParseException $e) {
            return false;
        }
    }

    protected function formatPhoneNumber($number)
    {
        $phoneUtil = PhoneNumberUtil::getInstance();
        try {
            // Parser en spécifiant la Guinée (GN) comme région par défaut
            $phoneNumber = $phoneUtil->parse($number, 'GN');
            if ($phoneUtil->isValidNumber($phoneNumber)) {
                // Retourner le format E164 (format international avec +)
                return $phoneUtil->format($phoneNumber, \libphonenumber\PhoneNumberFormat::E164);
            }
            return null;
        } catch (NumberParseException $e) {
            return null;
        }
    }
}
