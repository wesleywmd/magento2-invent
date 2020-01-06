<?php
namespace Wesleywmd\Invent\Model\Preference;

use Magento\Setup\Console\InputValidationException;
use Wesleywmd\Invent\Api\DataFactoryInterface;
use Wesleywmd\Invent\Api\ValidatorInterface;
use Wesleywmd\Invent\Console\InventStyle;
use Wesleywmd\Invent\Helper\AclHelper;
use Wesleywmd\Invent\Model\Component\BaseValidator;
use Wesleywmd\Invent\Model\ModuleNameFactory;
use Wesleywmd\Invent\Model\XmlParser\Location;

class Validator extends BaseValidator implements ValidatorInterface
{
    public function __construct(DataFactory $dataFactory, ModuleNameFactory $moduleNameFactory, AclHelper $aclHelper)
    {
        parent::__construct($dataFactory, $moduleNameFactory, $aclHelper);
    }

    public function validate(InventStyle $io)
    {
        $this->verifyModuleName($io, 'preference');

        $question = 'What object is the preference for?';
        $io->askForValidatedArgument('for', $question, null, function($for) {
            $this->validateNotNull($for);
            $this->validateNoWhitespace($for);
            $this->validateAlphaWithSpecial($for, '\/');
            return $for;
        }, 3);

        $question = 'What object will be used for the preference?';
        $io->askForValidatedArgument('type', $question, null, function($type) {
            $this->validateNotNull($type);
            $this->validateNoWhitespace($type);
            $this->validateAlphaWithSpecial($type, '\/');
            return $type;
        }, 3);

        $question = 'What DI area is the preference for?';
        $io->askForValidatedOption('area', $question, Location::AREA_GLOBAL, function($area) {
            $this->validateNotNull($area);
            $this->validateNoWhitespace($area);
            $this->validateAlpha($area);
            return $area;
        }, 3);
    }
}