<?php
namespace Wesleywmd\Invent\Helper;

class ModuleForgeHelper extends \Magento\Framework\App\Helper\AbstractHelper
{
    public function getRegistrationPhpContent($moduleName)
    {
        return "<?php
\\Magento\\Framework\\Component\\ComponentRegistrar::register(
    \\Magento\\Framework\\Component\\ComponentRegistrar::MODULE,
    \"{$moduleName}\",
    __DIR__
);";
    }
}