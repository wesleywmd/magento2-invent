<?php
namespace Wesleywmd\Invent\Console\Command;

use Magento\Setup\Console\Style\MagentoStyleFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Wesleywmd\Invent\Helper\AclHelper;
use Wesleywmd\Invent\Model\Menu;
use Wesleywmd\Invent\Model\ModuleNameFactory;

class InventAdminMenuCommand extends Command
{
    private $menu;

    private $menuDataFactory;

    private $moduleNameFactory;

    private $magentoStyleFactory;

    private $aclHelper;

    public function __construct(
        Menu $menu,
        Menu\DataFactory $menuDataFactory,
        ModuleNameFactory $moduleNameFactory,
        MagentoStyleFactory $magentoStyleFactory,
        AclHelper $aclHelper
    ) {
        parent::__construct();
        $this->menu = $menu;
        $this->menuDataFactory = $menuDataFactory;
        $this->moduleNameFactory = $moduleNameFactory;
        $this->magentoStyleFactory = $magentoStyleFactory;
        $this->aclHelper = $aclHelper;
    }

    protected function configure()
    {
        $this->setName('invent:admin:menu')
            ->setDescription('Create Admin Menu Item')
            ->addArgument('moduleName', InputArgument::REQUIRED, 'Module Name')
            ->addArgument('menuName', InputArgument::REQUIRED, 'Menu Name')
            ->addOption('parent', null, InputOption::VALUE_REQUIRED, 'Parent Menu Name', null)
            ->addOption('title', null, InputOption::VALUE_REQUIRED, 'Menu Title', null)
            ->addOption('sortOrder', null, InputOption::VALUE_REQUIRED, 'Menu Sort Order', 10)
            ->addOption('action', null, InputOption::VALUE_REQUIRED, 'Menu Action', null)
            ->addOption('resource', null, InputOption::VALUE_REQUIRED, 'ACL Resource', null);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $menuData = $this->menuDataFactory->create([
                'moduleName' => $this->moduleNameFactory->create($input->getArgument('moduleName')),
                'menuName' => $input->getArgument('menuName'),
                'parentMenu' => $input->getOption('parent'),
                'title' => $input->getOption('title'),
                'sortOrder' => $input->getOption('sortOrder'),
                'action' => $input->getOption('action'),
                'resource' => $input->getOption('resource')
            ]);
            $this->menu->addToModule($menuData);
            $output->writeln('Menu Created Successfully!');
        } catch (\Exception $e) {
            $output->writeln($e->getMessage());
            return 1;
        }
    }
}