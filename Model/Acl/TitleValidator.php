<?php
namespace Wesleywmd\Invent\Model\Acl;

use Magento\Setup\Console\InputValidationException;
use Wesleywmd\Invent\Console\AbstractValidator;

class TitleValidator extends AbstractValidator
{
    protected $key = 'title';

    public function validate($title)
    {
        $this->validateNotNull($title);
        $this->validateAlphaNumericWithSpecial($title,'\s');
        return $title;
    }
}