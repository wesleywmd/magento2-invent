<?php
namespace Wesleywmd\Invent\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Wesleywmd\Invent\Console\InventStyle;
use Wesleywmd\Invent\Console\InventStyleFactory;
use Wesleywmd\Invent\Helper\AclHelper;
use Wesleywmd\Invent\Model\Acl;
use Wesleywmd\Invent\Model\Module\ModuleNameValidator;
use Wesleywmd\Invent\Model\Module\SortOrderValidator;
use Wesleywmd\Invent\Model\ModuleNameFactory;

class InventAclCommand extends Command
{
    private $acl;

    private $aclDataFactory;

    private $moduleNameFactory;

    private $inventStyleFactory;

    private $aclHelper;

    private $moduleNameValidator;

    private $aclNameValidator;

    private $titleValidator;

    private $sortOrderValidator;

    public function __construct(
        Acl $acl,
        Acl\DataFactory $aclDataFactory,
        ModuleNameFactory $moduleNameFactory,
        InventStyleFactory $inventStyleFactory,
        AclHelper $aclHelper,
        ModuleNameValidator $moduleNameValidator,
        Acl\AclNameValidator $aclNameValidator,
        Acl\TitleValidator $titleValidator,
        SortOrderValidator $sortOrderValidator
    ) {
        parent::__construct();
        $this->acl = $acl;
        $this->aclDataFactory = $aclDataFactory;
        $this->moduleNameFactory = $moduleNameFactory;
        $this->inventStyleFactory = $inventStyleFactory;
        $this->aclHelper = $aclHelper;
        $this->moduleNameValidator = $moduleNameValidator;
        $this->aclNameValidator = $aclNameValidator;
        $this->titleValidator = $titleValidator;
        $this->sortOrderValidator = $sortOrderValidator;
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
        $io = $this->inventStyleFactory->create(compact('input','output'));

        $question = 'What module do you want to add an ACL to?';
        $io->askForValidatedArgument('moduleName', $question, null, $this->moduleNameValidator, 3);

        $moduleName = $this->moduleNameFactory->create($input->getArgument('moduleName'));

        $question = 'What is the name for the new ACL? (Prefix will be added for you: '.$moduleName->getName().':: )';
        $io->askForValidatedArgument('aclName', $question, null, $this->aclNameValidator, 3);

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

        $aclData = $this->aclDataFactory->create([
            'moduleName' => $moduleName,
            'aclName' => $input->getArgument('aclName'),
            'parentAcl' => $input->getOption('parent'),
            'title' => null,
            'sortOrder' => $input->getOption('sortOrder')
        ]);

        if (is_null($input->getOption('title'))) {
            if (!$io->confirm('Do you want to use the generated title? "'.$aclData->getTitle().'"', false)) {
                $question = 'What title do you want to use?';
                $io->askForValidatedOption('title', $question, null, $this->titleValidator, 3);
            }
        }

        $question = 'What is the sortOrder of the ACL?';
        $io->askForValidatedOption('sortOrder', $question, 10, $this->sortOrderValidator, 3);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var InventStyle $io */
        $io = $this->inventStyleFactory->create(compact('input', 'output'));
        try {
            $aclData = $this->aclDataFactory->create([
                'moduleName' => $this->moduleNameFactory->create($input->getArgument('moduleName')),
                'aclName' => $input->getArgument('aclName'),
                'parentAcl' => $input->getOption('parent'),
                'title' => $input->getOption('title'),
                'sortOrder' => $input->getOption('sortOrder')
            ]);
            $this->acl->addToModule($aclData);
            $io->success('ACL Created Successfully!');
        } catch (\Exception $e) {
            $io->error($e->getMessage());
            return 1;
        }
    }
}