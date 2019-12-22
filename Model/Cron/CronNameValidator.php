<?php
namespace Wesleywmd\Invent\Model\Cron;

use Wesleywmd\Invent\Model\Component\AbstractValidator;

class CronNameValidator extends AbstractValidator
{
    protected $key = 'cronName';

    public function validate($cronName)
    {
        $this->validateNotNull($cronName);
        $this->validateNoWhitespace($cronName);
        $this->validateAlphaWithSpecial($cronName,'\/');
        return $cronName;
    }
}