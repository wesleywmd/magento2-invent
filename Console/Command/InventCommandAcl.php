<?php
namespace Wesleywmd\Invent\Console\Command;

use Magento\Setup\Console\Style\MagentoStyleInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Wesleywmd\Invent\Api\ComponentInterface;
use Wesleywmd\Invent\Console\InventStyle;
use Wesleywmd\Invent\Console\InventStyleFactory;
use Wesleywmd\Invent\Helper\AclHelper;
use Wesleywmd\Invent\Model\Acl\AclNameValidator;
use Wesleywmd\Invent\Model\Module\ModuleNameValidator;
use Wesleywmd\Invent\Model\ModuleNameFactory;

abstract class InventCommandAcl extends InventCommandBase
{
    protected $aclHelper;
    
    protected $aclNameValidator;

    public function __construct(
        ComponentInterface $component,
        ModuleNameFactory $moduleNameFactory,
        InventStyleFactory $inventStyleFactory,
        ModuleNameValidator $moduleNameValidator,
        AclHelper $aclHelper,
        AclNameValidator $aclNameValidator
    ) {
        parent::__construct($component, $moduleNameFactory, $inventStyleFactory, $moduleNameValidator);
        $this->aclHelper = $aclHelper;
        $this->aclNameValidator = $aclNameValidator;
    }

    protected function verifyAclOption(InventStyle $io, $option)
    {
        if (!$this->aclHelper->findInTree($io->getInput()->getOption($option))) {
            if (is_null($io->getInput()->getOption($option))) {
                $io->comment('Looks like you didn\'t specify a '.$option.' resource. Lets find the correct one together');
            } else {
                $io->comment('Looks like you picked an invalid '.$option.' resource. Lets find the correct one together');
                $io->getInput()->setOption($option, null);
            }
            $options = $this->aclHelper->getParentOptions('Magento_Backend::admin');
            $stop = [];
            while (!empty($options)) {
                sort($options);
                $parent = $io->choice('Which resource would you like?', array_merge($stop, $options));
                if ($parent === 'Stop Here') {
                    break;
                }
                $io->getInput()->setOption($option, $parent);
                $options = $this->aclHelper->getParentOptions($parent);
                $stop = ['Stop Here'];
            }
        }
    }
}