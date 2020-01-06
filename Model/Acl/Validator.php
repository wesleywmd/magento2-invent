<?php
namespace Wesleywmd\Invent\Model\Acl;

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
        $this->verifyModuleName($io, 'acl');

        $moduleName = $this->moduleNameFactory->create($io->getInput()->getArgument('moduleName'));

        $question = 'What is the name for the new ACL? (Prefix will be added for you: '.$moduleName->getName().':: )';
        $io->askForValidatedArgument('aclName', $question, null, function($aclName) {
            $this->validateNotNull($aclName);
            $this->validateNoWhitespace($aclName);
            $this->validateAlphaWithSpecial($aclName, '_');
            return $aclName;
        }, 3);

        $this->verifyAclOption($io, 'parent');

        $aclData = $this->dataFactory->create($io->getInput());

        if (is_null($io->getInput()->getOption('title'))) {
            $question = 'Do you want to use the generated title? "'.$aclData->getTitle().'"';
            if (!$io->confirm($question, false)) {
                $question = 'What title do you want to use?';
                $io->askForValidatedOption('title', $question, null, function($title) {
                    $this->validateNotNull($title);
                    $this->validateAlphaNumericWithSpecial($title,'\s');
                    return $title;
                }, 3);
            }
        }

        $question = 'What is the sortOrder of the ACL?';
        $io->askForValidatedOption('sortOrder', $question, 10, function($sortOrder) {
            $this->validateNotNull($sortOrder);
            $this->validateNoWhitespace($sortOrder);
            $this->validateNumeric($sortOrder);
            return $sortOrder;
        }, 3);
    }
}