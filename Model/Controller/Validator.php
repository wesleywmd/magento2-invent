<?php
namespace Wesleywmd\Invent\Model\Controller;

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
        $this->verifyModuleName($io, 'controller');

        $question = 'What is the controller\'s url?';
        $errorMessage = 'Specified Controller already exists';
        $this->verifyFileNameArgument($io, function($controllerUrl) {
            try{
                $this->validateNotNull($controllerUrl);
                $this->validateNoWhitespace($controllerUrl);
                $this->validateAlphaNumericWithSpecial($controllerUrl,'\/');
            } catch(InputValidationException $e) {
                throw new InputValidationException('controllerUrl: '.$e);
            }
        }, $question, 'controllerUrl', $errorMessage);

        $question = 'What router should this controller be associated to?';
        $io->askForValidatedOption('router', $question, 'standard', function($router) {
            try{
                $this->validateNotNull($router);
                $this->validateNoWhitespace($router);
                $this->validateAlpha($router);
            } catch(InputValidationException $e) {
                throw new InputValidationException('controllerUrl: '.$e);
            }
        }, 3);
    }
}