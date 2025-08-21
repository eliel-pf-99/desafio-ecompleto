<?php

class DataValidator
{
    /**
     * Valida um CPF.
     * @param string $cpf
     * @return bool
     */
    public static function validateCpf(string $cpf): bool
    {
        
        $cpf = preg_replace('/[^0-9]/', '', $cpf);

        if (strlen($cpf) !== 11 || preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }

        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += (int) $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ((int) $cpf[$c] !== $d) {
                return false;
            }
        }

        return true;
    }

    /**
     * Valida o formato de um RG.
     * Esta validação não verifica o dígito verificador.
     * @param string $rg
     * @return bool
     */
    public static function validateRg(string $rg): bool
    {
        // Remove pontos, traços e espaços
        $cleanRg = preg_replace('/[^a-zA-Z0-9]/', '', $rg);
        
        // Regex para RG com 8 ou 9 dígitos + dígito verificador.
        // A validação de RG é complexa por causa das variações estaduais,
        // então esta regex serve apenas como uma validação de formato básica.
        $regex = '/^(\d{8}|\d{9})[\da-zA-Z]?$/';
        
        return (bool) preg_match($regex, $cleanRg);
    }

    /**
     * Valida um CNPJ.
     * @param string $cnpj
     * @return bool
     */
    public static function validateCnpj(string $cnpj): bool
    {
        
        $cnpj = preg_replace('/[^0-9]/', '', $cnpj);

       
        if (strlen($cnpj) !== 14 || preg_match('/(\d)\1{13}/', $cnpj)) {
            return false;
        }

        
        $soma = 0;
        $t = 12;
        for ($i = 0; $i < 12; $i++) {
            $soma += (int) $cnpj[$i] * (($t - $i + 1));
        }
        $d1 = (($soma % 11) < 2) ? 0 : 11 - ($soma % 11);

        if ((int) $cnpj[12] !== $d1) {
            return false;
        }

        
        $soma = 0;
        $t = 13;
        for ($i = 0; $i < 13; $i++) {
            $soma += (int) $cnpj[$i] * (($t - $i + 1));
        }
        $d2 = (($soma % 11) < 2) ? 0 : 11 - ($soma % 11);

        if ((int) $cnpj[13] !== $d2) {
            return false;
        }

        return true;
    }

    /**
     * Valida o tipo da pessoa.
     * @param string $type
     * @return bool
     */
    public static function validatePersonType(string $type): bool
    {
        $type = strtoupper($type);
        return in_array($type, ['F', 'PJ']);
    }

    /**
     * Valida o e-mail.
     * @param string $email
     * @return bool
     */
    public static function validateEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Valida o formato de uma data no padrão YYYY-MM-DD.
     * @param string $date
     * @return bool
     */
    public static function validateDate(string $date): bool
    {
        $regex = "/^\d{4}-\d{2}-\d{2}$/";
        return (bool) preg_match($regex, $date);
    }

    /**
     * Valida um número de cartão de crédito usando o algoritmo de Luhn.
     * @param string $cardNumber
     * @return bool
     */
    public static function validateCreditCard(string $cardNumber): bool
    {
        $sanitizedCardNumber = preg_replace('/[^0-9]/', '', $cardNumber);

        if (empty($sanitizedCardNumber) || strlen($sanitizedCardNumber) < 13) {
            return false;
        }

        $reversedDigits = array_reverse(str_split($sanitizedCardNumber));
        $sum = 0;

        foreach ($reversedDigits as $index => $digit) {
            $digit = (int) $digit;
            
            if ($index % 2 !== 0) {
                $digit *= 2;
                if ($digit > 9) {
                    $digit -= 9;
                }
            }
            $sum += $digit;
        }

        return ($sum % 10 === 0);
    }

    /**
     * Identifica a bandeira do cartão pelo número.
     * @param string $cardNumber
     * @return string|null Retorna a bandeira (ex: 'Visa', 'Amex') ou null se não reconhecida.
     */
    public static function getCardType(string $cardNumber): ?string
    {
        $sanitizedCardNumber = preg_replace('/[^0-9]/', '', $cardNumber);

        if (preg_match('/^5[1-5]/', $sanitizedCardNumber)) {
            return 'Mastercard';
        }
        if (preg_match('/^4/', $sanitizedCardNumber)) {
            return 'Visa';
        }
        if (preg_match('/^3[47]/', $sanitizedCardNumber)) {
            return 'Amex';
        }
        if (preg_match('/^6(011|5|4[4-9])/', $sanitizedCardNumber)) {
            return 'Discover';
        }
        // Adicionando Elo, já que estamos no Brasil
        if (preg_match('/^636297|^5067|438935/', $sanitizedCardNumber)) {
            return 'Elo';
        }

        return null;
    }

    /**
     * Valida o CVV com base na bandeira do cartão.
     * @param string $cvv
     * @param string $cardNumber O número do cartão para descobrir a bandeira.
     * @return bool
     */
    public static function validateCvv(string $cvv, string $cardNumber): bool
    {
        $cardType = self::getCardType($cardNumber);
        $sanitizedCvv = preg_replace('/[^0-9]/', '', $cvv);
        $length = strlen($sanitizedCvv);
        
        if ($cardType === null) {
            return false;
        }

        if ($cardType === 'Amex') {
            return $length === 4;
        }

        return $length === 3;
    }

    /**
     * Verifica se a data de validade de um cartão (MMYY) já venceu.
     * @param string $expirationDate A data de validade no formato MMYY.
     * @return bool
     */
    public static function isExpired(string $expirationDate): bool
    {
        $sanitizedDate = preg_replace('/[^0-9]/', '', $expirationDate);

        if (strlen($sanitizedDate) !== 4) {
            return true; 
        }

        $month = substr($sanitizedDate, 0, 2);
        $year = substr($sanitizedDate, 2, 2);
        
        $currentYear = (int) date('Y');
        $fullYear = (int) "20{$year}";

        if ($fullYear < $currentYear) {
            return true;
        }

        $expiration = new DateTime();
        $expiration->setDate($fullYear, (int) $month, 1);
        $expiration->modify('last day of this month');

        $now = new DateTime();

        return $expiration < $now;
    }
}
