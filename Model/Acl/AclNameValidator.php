<?php
namespace Wesleywmd\Invent\Model\Acl;

use Magento\Setup\Console\InputValidationException;
use Wesleywmd\Invent\Console\AbstractValidator;

class AclNameValidator extends AbstractValidator
{
    protected $key = 'aclName';

    public function validate($aclName)
    {
        $this->validateNotNull($aclName);
        $this->validateNoWhitespace($aclName);
        $this->validateAlphaWithSpecial($aclName, '_');
        return $aclName;
    }
}