<?php
namespace Wesleywmd\Invent\Model\Config;

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
        $this->verifyModuleName($io, 'config');

        $question = 'What is the name of the config? (Name must contain 2 slashes)';
        $io->askForValidatedArgument('configName', $question, null, function($configName) {
            try{
                $this->validateNotNull($configName);
                $this->validateNoWhitespace($configName);
                $this->validateAlphaWithSpecial($configName,'\/');
                if (count(explode('/', $configName)) !== 3) {
                    throw new InputValidationException('value must contain exactly 2 slashes');
                }
            } catch(InputValidationException $e) {
                throw new InputValidationException('configName: '.$e->getMessage());
            }
            return $configName;
        }, 3);
        
        if (!is_null($io->getInput()->getOption('tabLabel'))) {
            $question = 'What is the Tab\'s label?';
            $io->askForValidatedOption('tabLabel', $question, null, function($tabLabel) {
                $this->validateAlphaWithSpecial($tabLabel,'\s');
                return $tabLabel;
            }, 3);

            $data = $this->dataFactory->create($io->getInput());

            if (is_null($io->getInput()->getOption('tabId'))) {
                if (!$io->confirm('Do you want to use the generated Tab Id? "'.$data->getSectionId().'"', false)) {
                    $question = 'What Tab Id do you want to use?';
                    $io->askForValidatedOption('tabId', $question, null, function($tabId) {
                        $this->validateNotNull($tabId);
                        $this->validateNoWhitespace($tabId);
                        $this->validateAlphaWithSpecial($tabId,'_');
                        return $tabId;
                    }, 3);
                }
            }
        }

        if (!is_null($io->getInput()->getOption('sectionLabel'))) {
            $this->verifyAclOption($io, 'sectionResource');
        }
    }
}