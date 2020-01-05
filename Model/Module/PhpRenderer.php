<?php
namespace Wesleywmd\Invent\Model\Module;

use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Api\RendererInterface;
use Wesleywmd\Invent\Model\Component\AbstractPhpRenderer;

class PhpRenderer extends AbstractPhpRenderer implements RendererInterface
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

    protected function getClassStatement(DataInterface $data)
    {
        return $this->phpBuilder->class('Fake');
    }
}