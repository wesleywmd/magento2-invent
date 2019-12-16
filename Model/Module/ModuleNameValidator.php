<?php
namespace Wesleywmd\Invent\Model\Module;

use Magento\Setup\Console\InputValidationException;
use Wesleywmd\Invent\Console\AbstractValidator;
use Wesleywmd\Invent\Model\ModuleNameException;
use Wesleywmd\Invent\Model\ModuleNameFactory;

class ModuleNameValidator extends AbstractValidator
{
    private $moduleNameFactory;
    
    protected $key = 'moduleName';
    
    public function __construct(ModuleNameFactory $moduleNameFactory)
    {
        $this->moduleNameFactory = $moduleNameFactory;
    }

    public function validate($moduleName)
    {
        $this->validateNotNull($moduleName);
        $this->validateNoWhitespace($moduleName);
        $this->validateAlphaNumericWithSpecial($moduleName,'_');
        $this->validateMustContain($moduleName, '_');
        $this->validateModuleExists($moduleName);
        return $moduleName;
    }
    
    private function validateModuleExists($moduleName)
    {
        try {
            $name = $this->moduleNameFactory->create($moduleName);
        } catch (ModuleNameException $e) {
            throw new InputValidationException($e->getMessage());
        }
        if (!is_dir($name->getPath())) {
            throw new InputValidationException('Specified Module does not exist');
        }
    }
}