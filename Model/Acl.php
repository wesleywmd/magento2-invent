<?php
namespace Wesleywmd\Invent\Model;

use Wesleywmd\Invent\Api\ComponentInterface;
use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Helper\FileHelper;
use Wesleywmd\Invent\Model\Component\AbstractComponent;

class Acl extends AbstractComponent implements ComponentInterface
{
    public function __construct(
        FileHelper $fileHelper,
        Acl\XmlRenderer $xmlRenderer
    ) {
        parent::__construct($fileHelper, null, $xmlRenderer);
    }

    public function addToModule(DataInterface $data)
    {
        $this->createXmlFile($data);
    }
}