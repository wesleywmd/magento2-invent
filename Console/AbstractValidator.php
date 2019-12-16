<?php
namespace Wesleywmd\Invent\Console;

use Magento\Setup\Console\InputValidationException;

abstract class AbstractValidator
{
    protected $key;

    public function __invoke($value)
    {
        return $this->validate($value);
    }

    abstract public function validate($value);

    public function validateNotNull($value)
    {
        if (is_null($value)) {
            throw new InputValidationException($this->key.' is required');
        }
    }

    public function validateNoWhitespace($value)
    {
        if (preg_match('/\s/', $value)) {
            throw new InputValidationException($this->key.' cannot contain spaces');
        }
    }

    public function validateAlphaNumeric($value)
    {
        if (!preg_match('/^[a-zA-Z0-9]*$/', $value)) {
            throw new InputValidationException($this->key.' must only contain alpha-numeric characters');
        }
    }

    public function validateAlphaNumericWithSpecial($value, $special)
    {
        if (!preg_match('/^[a-zA-Z0-9'.$special.']*$/', $value)) {
            throw new InputValidationException($this->key.' must only contain alpha-numeric characters or characters in '.$special);
        }
    }
    
    public function validateMustContain($value, $characters)
    {
        if (!preg_match('/'.$characters.'/', $value)) {
            throw new InputValidationException($this->key.' must contain at least one of '.$characters);
        }
    }
}