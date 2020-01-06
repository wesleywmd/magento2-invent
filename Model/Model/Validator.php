<?php
namespace Wesleywmd\Invent\Model\Model;

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
        $this->verifyModuleName($io, 'model');

        $question = 'What is the model\'s name?';
        $errorMessage = 'Specified Model already exists';
        $this->verifyFileNameArgument($io, function($modelName) {
            $this->validateNotNull($modelName);
            $this->validateNoWhitespace($modelName);
            $this->validateAlpha($modelName);
            return $modelName;
        }, $question, 'modelName', $errorMessage);
    }
}