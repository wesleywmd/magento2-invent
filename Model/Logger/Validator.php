<?php
namespace Wesleywmd\Invent\Model\Logger;

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
        $this->verifyModuleName($io, 'logger');

        $question = 'What is the logger\'s name?';
        $errorMessage = 'Specified Logger already exists';
        $this->verifyFileNameArgument($io, function($loggerName) {
            $this->validateNotNull($loggerName);
            $this->validateNoWhitespace($loggerName);
            $this->validateAlphaWithSpecial($loggerName,'_');
            return $loggerName;
        }, $question, 'loggerName', $errorMessage);
        
        /** @var Data $data */
        $data = $this->dataFactory->create($io->getInput());
        if (is_file($data->getHandlerPath())) {
            $question = 'Appears a Handler already exists in the specified module. Should we recreate it? (if no, the logger will not be created)';
            if ($io->confirm($question, true)) {
                unlink($data->getHandlerPath());
            } else {
                $io->warning('Logger could not be created');
            }
        }
    }
}