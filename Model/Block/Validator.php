<?php
namespace Wesleywmd\Invent\Model\Block;

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
        $this->verifyModuleName($io, 'block');

        $question = 'What is the block\'s name?';
        $errorMessage = 'Specified Block already exists';
        $this->verifyFileNameArgument($io, function($blockName) {
            $this->validateNotNull($blockName);
            $this->validateNoWhitespace($blockName);
            $this->validateAlphaNumericWithSpecial($blockName, '\/');
            return $blockName;
        }, $question, 'blockName', $errorMessage);
    }
}