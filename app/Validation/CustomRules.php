<?php

namespace App\Validation;

class CustomRules
{
    /**
     * Valida múltiples emails separados por punto y coma
     * 
     * @param string $str
     * @param string $fields
     * @param array $data
     * @return bool
     */
    public function valid_email_multiple(string $str = null, string $fields = null, array $data = null): bool
    {
        // Si está vacío, es válido (permit_empty)
        if (empty($str)) {
            return true;
        }

        // Separar por punto y coma
        $emails = explode(';', $str);
        
        foreach ($emails as $email) {
            $email = trim($email);
            
            // Si después de trim está vacío, continuar
            if (empty($email)) {
                continue;
            }
            
            // Validar cada email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return false;
            }
        }
        
        return true;
    }
}

