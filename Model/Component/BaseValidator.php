<?php
namespace Wesleywmd\Invent\Model\Component;

use Magento\Setup\Console\InputValidationException;
use Wesleywmd\Invent\Api\DataFactoryInterface;
use Wesleywmd\Invent\Api\ValidatorInterface;
use Wesleywmd\Invent\Console\InventStyle;
use Wesleywmd\Invent\Helper\AclHelper;
use Wesleywmd\Invent\Model\ModuleNameException;
use Wesleywmd\Invent\Model\ModuleNameFactory;

class BaseValidator implements ValidatorInterface
{
    protected $dataFactory;

    protected $moduleNameFactory;
    
    protected $aclHelper;

    public function __construct(
        DataFactoryInterface $dataFactory, 
        ModuleNameFactory $moduleNameFactory, 
        AclHelper $aclHelper
    ) {
        $this->dataFactory = $dataFactory;
        $this->moduleNameFactory = $moduleNameFactory;
        $this->aclHelper = $aclHelper;
    }

    public function validate(InventStyle $io)
    {
        return;
    }

    protected function verifyModuleName(InventStyle $io, $to = 'component')
    {
        $question = 'What module do you want to add a '.$to.' to?';
        $io->askForValidatedArgument('moduleName', $question, null, function($moduleName) {
            try{
                $this->validateNotNull($moduleName);
                $this->validateNoWhitespace($moduleName);
                $this->validateAlphaNumericWithSpecial($moduleName,'_');
                $this->validateMustContain($moduleName, '_');
                try {
                    $name = $this->moduleNameFactory->create($moduleName);
                } catch (ModuleNameException $e) {
                    throw new InputValidationException($e->getMessage());
                }
                if (!is_dir($name->getPath())) {
                    throw new InputValidationException('Specified Module does not exist');
                }
            } catch(InputValidationException $e) {
                throw new InputValidationException('moduleName: '.$e->getMessage());
            }
            return $moduleName;
        }, 3);
    }

    protected function verifyFileNameArgument(InventStyle $io, $validator, $question, $argument, $errorMessage)
    {
        do {
            /** @var InventStyle $io */
            $io->askForValidatedArgument($argument, $question, null, $validator, 3);
            $data = $this->dataFactory->create($io->getInput());
            if (is_file($data->getPath())) {
                $io->error($errorMessage);
                $io->getInput()->setArgument($argument, null);
            }
        } while(is_null($io->getInput()->getArgument($argument)));
    }

    protected function verifyAclOption(InventStyle $io, $option)
    {
        if ($this->aclHelper->findInTree($io->getInput()->getOption($option))) {
            $io->comment('Looks like you picked an existing '.$option.' resource. Lets find the correct one together.');
            $io->getInput()->setOption($option, null);
        }

        if (is_null($io->getInput()->getOption($option))) {
            $io->comment('Looks like you didn\'t specify a '.$option.' resource. Lets find the correct one together.');
        } else {
            $io->comment('Looks like you picked an invalid '.$option.' resource. Lets find the correct one together.');
            $io->getInput()->setOption($option, null);
        }

        $options = $this->aclHelper->getParentOptions('Magento_Backend::admin');
        $stop = [];
        while (!empty($options)) {
            sort($options);
            $parent = $io->choice('Which resource would you like?', array_merge($stop, $options));
            if ($parent === 'Stop Here') {
                break;
            }
            $io->getInput()->setOption($option, $parent);
            $options = $this->aclHelper->getParentOptions($parent);
            $stop = ['Stop Here'];
        }
    }

    public function validateNotNull($value)
    {
        if (is_null($value)) {
            throw new InputValidationException('value is required');
        }
    }

    public function validateNoWhitespace($value)
    {
        if (preg_match('/\s/', $value)) {
            throw new InputValidationException('value cannot contain spaces');
        }
    }

    public function validateAlpha($value)
    {
        if (!preg_match('/^[a-zA-Z]*$/', $value)) {
            throw new InputValidationException('value must only contain alpha characters');
        }
    }

    public function validateAlphaWithSpecial($value, $special)
    {
        if (!preg_match('/^[a-zA-Z'.$special.']*$/', $value)) {
            throw new InputValidationException('value must only contain alpha characters or characters in '.$special);
        }
    }

    public function validateNumeric($value)
    {
        if (!preg_match('/^[0-9]*$/', $value)) {
            throw new InputValidationException('value must only contain alpha characters');
        }
    }

    public function validateAlphaNumeric($value)
    {
        if (!preg_match('/^[a-zA-Z0-9]*$/', $value)) {
            throw new InputValidationException('value must only contain alpha-numeric characters');
        }
    }

    public function validateAlphaNumericWithSlash($value)
    {
        if (!preg_match('/^[a-zA-Z0-9\/]*$/', $value)) {
            throw new InputValidationException('value must only contain alpha-numeric characters or a slash');
        }
    }

    public function validateAlphaNumericWithSpecial($value, $special)
    {
        if (!preg_match('/^[a-zA-Z0-9'.$special.']*$/', $value)) {
            throw new InputValidationException('value must only contain alpha-numeric characters or characters in '.$special);
        }
    }

    public function validateMustContain($value, $characters)
    {
        if (!preg_match('/'.$characters.'/', $value)) {
            throw new InputValidationException('value must contain at least one of '.$characters);
        }
    }
}