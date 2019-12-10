<?php
namespace Wesleywmd\Invent\Model\Module;

use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Api\PhpRendererInterface;

class PhpRenderer implements PhpRendererInterface
{
    public function getContents(DataInterface $data)
    {
        return implode("\n", [
            '<?php',
            '\Magento\Framework\Component\ComponentRegistrar::register(',
            '    \Magento\Framework\Component\ComponentRegistrar::MODULE,',
            '    \''.$data->getModuleName()->getName().'\',',
            '    __DIR__',
            ');'
        ]);
    }
}