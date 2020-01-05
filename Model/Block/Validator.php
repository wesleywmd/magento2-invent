<?php
namespace Wesleywmd\Invent\Model\Block;

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
        $this->verifyModuleName($io, 'block');

        $question = 'What is the block\'s name?';
        $errorMessage = 'Specified Block already exists';
        $this->verifyFileNameArgument($io, function($value) {
            try{
                $this->validateNotNull($value);
                $this->validateNoWhitespace($value);
                $this->validateAlphaNumericWithSpecial($value, '\/');
            } catch(InputValidationException $e) {
                throw new InputValidationException('blockName: '.$e);
            }
        }, $question, 'blockName', $errorMessage);
    }
}