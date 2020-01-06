<?php
namespace Wesleywmd\Invent\Model\Menu;

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
        $this->verifyModuleName($io, 'menu');

        $question = 'What is the name of the menu?';
        $io->askForValidatedArgument('menuName', $question, null, function($menuName) {
            $this->validateNotNull($menuName);
            $this->validateNoWhitespace($menuName);
            $this->validateAlphaWithSpecial($menuName,'\/');
            return $menuName;
        }, 3);
    }
}