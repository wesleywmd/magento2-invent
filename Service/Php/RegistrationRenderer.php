<?php
namespace Wesleywmd\Invent\Service\Php;

class RegistrationRenderer
{

    public function render($moduleName)
    {
        return "<?php
\\Magento\\Framework\\Component\\ComponentRegistrar::register(
    \\Magento\\Framework\\Component\\ComponentRegistrar::MODULE,
    \"{$moduleName}\",
    __DIR__
);";
    }

}