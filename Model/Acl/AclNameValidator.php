<?php
namespace Wesleywmd\Invent\Model\Acl;

use Wesleywmd\Invent\Model\Component\AbstractValidator;

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