<?php
namespace Wesleywmd\Invent\Model\Logger;

use Wesleywmd\Invent\Model\Component\AbstractValidator;

class LoggerNameValidator extends AbstractValidator
{
    protected $key = 'loggerName';

    public function validate($loggerName)
    {
        $this->validateNotNull($loggerName);
        $this->validateNoWhitespace($loggerName);
        $this->validateAlphaWithSpecial($loggerName,'_');
        return $loggerName;
    }
}