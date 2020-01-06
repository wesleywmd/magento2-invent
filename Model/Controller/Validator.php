<?php
namespace Wesleywmd\Invent\Model\Controller;

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
        $this->verifyModuleName($io, 'controller');

        $question = 'What is the controller\'s url?';
        $errorMessage = 'Specified Controller already exists';
        $this->verifyFileNameArgument($io, function($controllerUrl) {
            $this->validateNotNull($controllerUrl);
            $this->validateNoWhitespace($controllerUrl);
            $this->validateAlphaNumericWithSpecial($controllerUrl,'\/');
            return $controllerUrl;
        }, $question, 'controllerUrl', $errorMessage);

        $question = 'What router should this controller be associated to?';
        $io->askForValidatedOption('router', $question, 'standard', function($router) {
            $this->validateNotNull($router);
            $this->validateNoWhitespace($router);
            $this->validateAlpha($router);
            return $router;
        }, 3);
    }
}