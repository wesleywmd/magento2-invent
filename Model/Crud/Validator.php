<?php
namespace Wesleywmd\Invent\Model\Crud;

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
        $this->verifyModuleName($io, 'admin crud');
//
//        $question = 'What is the cron\'s name?';
//        $errorMessage = 'Specified Cron already exists';
//        $this->verifyFileNameArgument($io, function($cronName) {
//            $this->validateNotNull($cronName);
//            $this->validateNoWhitespace($cronName);
//            $this->validateAlphaWithSpecial($cronName,'\/');
//            return $cronName;
//        }, $question, 'cronName', $errorMessage);
    }
}