<?php

namespace services;

class Validator
{
    /**
     * @throws \Exception
     */
    public function validateText($text, $symbolsNumber): void
    {
        if (empty($text)) {
            throw new \Exception('Text is required');
        }

        if (mb_strlen($text, "UTF-8") > $symbolsNumber) {
            throw new \Exception('Text is more than ' . $symbolsNumber . ' symbols');
        }
    }
}