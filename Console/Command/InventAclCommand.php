<?php
namespace Wesleywmd\Invent\Console\Command;

use Magento\Setup\Console\Style\MagentoStyleFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Wesleywmd\Invent\Helper\AclHelper;
use Wesleywmd\Invent\Model\Acl;
use Wesleywmd\Invent\Model\ModuleNameFactory;

class InventAclCommand extends Command
{
    private $acl;

    private $aclDataFactory;

    private $moduleNameFactory;

    private $magentoStyleFactory;

    private $aclHelper;

    public function __construct(
        Acl $acl,
        Acl\DataFactory $aclDataFactory,
        ModuleNameFactory $moduleNameFactory,
        MagentoStyleFactory $magentoStyleFactory,
        AclHelper $aclHelper
    ) {
        parent::__construct();
        $this->acl = $acl;
        $this->aclDataFactory = $aclDataFactory;
        $this->moduleNameFactory = $moduleNameFactory;
        $this->magentoStyleFactory = $magentoStyleFactory;
        $this->aclHelper = $aclHelper;
    }

    protected function configure()
    {
        $this->setName('invent:acl')
            ->setDescription('Create ACL Premission')
            ->addArgument('moduleName', InputArgument::REQUIRED, 'Module Name')
            ->addArgument('aclName', InputArgument::REQUIRED, 'ACL Name')
            ->addOption('parent', null, InputOption::VALUE_REQUIRED, 'Parent ACL Name', null)
            ->addOption('title', null, InputOption::VALUE_REQUIRED, 'ACL Title', null)
            ->addOption('sortOrder', null, InputOption::VALUE_REQUIRED, 'ACL Sort Order', 10);
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        parent::interact($input, $output);
        $io = $this->magentoStyleFactory->create(['input'=>$input,'output'=>$output]);
        if (!$this->aclHelper->findInTree($input->getOption('parent'))) {
            if (is_null($input->getOption('parent'))) {
                $io->comment('Looks like you didn\'t specify a parent resource. Lets find the correct one together');
            } else {
                $io->comment('Looks like you picked an invalid parent resource. Lets find the correct one together');
                $input->setOption('parent', null);
            }
            $options = $this->aclHelper->getParentOptions('Magento_Backend::admin');
            $stop = [];
            while (!empty($options)) {
                sort($options);
                $parent = $io->choice('Which resource would you like as a parent?', array_merge($stop, $options));
                if ($parent === 'Stop Here') {
                    break;
                }
                $input->setOption('parent', $parent);
                $options = $this->aclHelper->getParentOptions($parent);
                $stop = ['Stop Here'];
            }
        }
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $aclData = $this->aclDataFactory->create([
                'moduleName' => $this->moduleNameFactory->create($input->getArgument('moduleName')),
                'aclName' => $input->getArgument('aclName'),
                'parentAcl' => $input->getOption('parent'),
                'title' => $input->getOption('title'),
                'sortOrder' => $input->getOption('sortOrder')
            ]);
            $this->acl->addToModule($aclData);
            $output->writeln('ACL Created Successfully!');
        } catch (\Exception $e) {
            $output->writeln($e->getMessage());
            return 1;
        }
    }
}