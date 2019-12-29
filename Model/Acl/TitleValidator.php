<?php
namespace Wesleywmd\Invent\Model\Acl;

use Wesleywmd\Invent\Model\Component\AbstractValidator;

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