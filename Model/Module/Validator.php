<?php
namespace Wesleywmd\Invent\Model\Module;

use Magento\Setup\Console\InputValidationException;
use Wesleywmd\Invent\Api\DataFactoryInterface;
use Wesleywmd\Invent\Api\ValidatorInterface;
use Wesleywmd\Invent\Console\InventStyle;
use Wesleywmd\Invent\Helper\AclHelper;
use Wesleywmd\Invent\Model\Component\BaseValidator;
use Wesleywmd\Invent\Model\ModuleNameException;
use Wesleywmd\Invent\Model\ModuleNameFactory;

class Validator extends BaseValidator implements ValidatorInterface
{
    public function __construct(DataFactory $dataFactory, ModuleNameFactory $moduleNameFactory, AclHelper $aclHelper)
    {
        parent::__construct($dataFactory, $moduleNameFactory, $aclHelper);
    }

    public function validate(InventStyle $io)
    {
        $question = 'What is the name of the new module you want to add?';
        $io->askForValidatedArgument('moduleName', $question, null, function($moduleName) {
            $this->validateNotNull($moduleName);
            $this->validateNoWhitespace($moduleName);
            $this->validateAlphaNumericWithSpecial($moduleName,'_');
            $this->validateMustContain($moduleName, '_');
            try {
                $name = $this->moduleNameFactory->create($moduleName);
            } catch (ModuleNameException $e) {
                throw new InputValidationException($e->getMessage());
            }
            if (is_dir($name->getPath())) {
                throw new InputValidationException('Specified Module already exists');
            }
            return $moduleName;
        }, 3);
    }
}