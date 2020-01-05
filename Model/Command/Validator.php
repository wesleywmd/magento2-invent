<?php
namespace Wesleywmd\Invent\Model\Command;

use Magento\Setup\Console\InputValidationException;
use Wesleywmd\Invent\Api\DataFactoryInterface;
use Wesleywmd\Invent\Api\ValidatorInterface;
use Wesleywmd\Invent\Console\InventStyle;
use Wesleywmd\Invent\Model\Component\BaseValidator;
use Wesleywmd\Invent\Model\ModuleNameFactory;

class Validator extends BaseValidator implements ValidatorInterface
{
    public function __construct(DataFactory $dataFactory, ModuleNameFactory $moduleNameFactory)
    {
        parent::__construct($dataFactory, $moduleNameFactory);
    }

    public function validate(InventStyle $io)
    {
        $this->verifyModuleName($io, 'console command');

        $question = 'What is the console command\'s name?';
        $errorMessage = 'Specified Console Command already exists';
        $this->verifyFileNameArgument($io, function($commandName) {
            try{
                $this->validateNotNull($commandName);
                $this->validateNoWhitespace($commandName);
                $this->validateAlphaNumericWithSpecial($commandName,':');
            } catch(InputValidationException $e) {
                throw new InputValidationException('commandName: '.$e);
            }
        }, $question, 'commandName', $errorMessage);
    }
}