<?php
namespace Wesleywmd\Invent\Model\Command;

use Magento\Setup\Console\InputValidationException;
use Wesleywmd\Invent\Api\DataFactoryInterface;
use Wesleywmd\Invent\Api\ValidatorInterface;
use Wesleywmd\Invent\Console\InventStyle;
use Wesleywmd\Invent\Helper\AclHelper;
use Wesleywmd\Invent\Model\Component\BaseValidator;
use Wesleywmd\Invent\Model\ModuleNameFactory;

class Validator extends BaseValidator implements ValidatorInterface
{
    public function __construct(DataFactory $dataFactory, ModuleNameFactory $moduleNameFactory, AclHelper $aclHelper)
    {
        parent::__construct($dataFactory, $moduleNameFactory, $aclHelper);
    }

    public function validate(InventStyle $io)
    {
        $this->verifyModuleName($io, 'console command');

        $question = 'What is the console command\'s name?';
        $errorMessage = 'Specified Console Command already exists';
        $this->verifyFileNameArgument($io, function($commandName) {
            $this->validateNotNull($commandName);
            $this->validateNoWhitespace($commandName);
            $this->validateAlphaNumericWithSpecial($commandName,':');
            return $commandName;
        }, $question, 'commandName', $errorMessage);
    }
}