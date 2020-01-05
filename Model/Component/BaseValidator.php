<?php
namespace Wesleywmd\Invent\Model\Component;

use Wesleywmd\Invent\Api\DataFactoryInterface;
use Wesleywmd\Invent\Api\ValidatorInterface;
use Wesleywmd\Invent\Console\InventStyle;
use Wesleywmd\Invent\Model\ModuleNameException;
use Wesleywmd\Invent\Model\ModuleNameFactory;

class BaseValidator implements ValidatorInterface
{
    protected $dataFactory;

    protected $moduleNameFactory;

    public function __construct(DataFactoryInterface $dataFactory, ModuleNameFactory $moduleNameFactory)
    {
        $this->dataFactory = $dataFactory;
        $this->moduleNameFactory = $moduleNameFactory;
    }

    public function validate(InventStyle $io)
    {
        return;
    }

    protected function verifyModuleName(InventStyle $io, $to = 'component')
    {
        $question = 'What module do you want to add a '.$to.' to?';
        $io->askForValidatedArgument('moduleName', $question, null, function($value) {
            try{
                $this->validateNotNull($moduleName);
                $this->validateNoWhitespace($moduleName);
                $this->validateAlphaNumericWithSpecial($moduleName,'_');
                $this->validateMustContain($moduleName, '_');
                try {
                    $name = $this->moduleNameFactory->create($moduleName);
                } catch (ModuleNameException $e) {
                    throw new InputValidationException($e->getMessage());
                }
                if (!is_dir($name->getPath())) {
                    throw new InputValidationException('Specified Module does not exist');
                }
            } catch(InputValidationException $e) {
                throw new InputValidationException('moduleName: '.$e);
            }
        }, 3);
    }

    protected function verifyFileNameArgument(InventStyle $io, $validator, $question, $argument, $errorMessage)
    {
        do {
            /** @var InventStyle $io */
            $io->askForValidatedArgument($argument, $question, null, $validator, 3);
            $data = $this->dataFactory->create($io->getInput());
            if (is_file($data->getPath())) {
                $io->error($errorMessage);
                $io->getInput()->setArgument($argument, null);
            }
        } while(is_null($io->getInput()->getArgument($argument)));
    }

    public function validateNotNull($value)
    {
        if (is_null($value)) {
            throw new InputValidationException('value is required');
        }
    }

    public function validateNoWhitespace($value)
    {
        if (preg_match('/\s/', $value)) {
            throw new InputValidationException('value cannot contain spaces');
        }
    }

    public function validateAlpha($value)
    {
        if (!preg_match('/^[a-zA-Z]*$/', $value)) {
            throw new InputValidationException('value must only contain alpha characters');
        }
    }

    public function validateAlphaWithSpecial($value, $special)
    {
        if (!preg_match('/^[a-zA-Z'.$special.']*$/', $value)) {
            throw new InputValidationException('value must only contain alpha characters or characters in '.$special);
        }
    }

    public function validateNumeric($value)
    {
        if (!preg_match('/^[0-9]*$/', $value)) {
            throw new InputValidationException('value must only contain alpha characters');
        }
    }

    public function validateAlphaNumeric($value)
    {
        if (!preg_match('/^[a-zA-Z0-9]*$/', $value)) {
            throw new InputValidationException('value must only contain alpha-numeric characters');
        }
    }

    public function validateAlphaNumericWithSlash($value)
    {
        if (!preg_match('/^[a-zA-Z0-9\/]*$/', $value)) {
            throw new InputValidationException('value must only contain alpha-numeric characters or a slash');
        }
    }

    public function validateAlphaNumericWithSpecial($value, $special)
    {
        if (!preg_match('/^[a-zA-Z0-9'.$special.']*$/', $value)) {
            throw new InputValidationException('value must only contain alpha-numeric characters or characters in '.$special);
        }
    }

    public function validateMustContain($value, $characters)
    {
        if (!preg_match('/'.$characters.'/', $value)) {
            throw new InputValidationException('value must contain at least one of '.$characters);
        }
    }
}