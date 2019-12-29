<?php
namespace Wesleywmd\Invent\Console\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Wesleywmd\Invent\Console\InventStyleFactory;
use Wesleywmd\Invent\Helper\AclHelper;
use Wesleywmd\Invent\Model\Acl;
use Wesleywmd\Invent\Model\Module\ModuleNameValidator;
use Wesleywmd\Invent\Model\Module\SortOrderValidator;
use Wesleywmd\Invent\Model\ModuleNameFactory;

class InventAclCommand extends InventCommandAcl
{
    protected $successMessage = 'ACL Created Successfully!';
    
    private $aclDataFactory;

    private $titleValidator;

    private $sortOrderValidator;

    public function __construct(
        Acl $component,
        ModuleNameFactory $moduleNameFactory,
        InventStyleFactory $inventStyleFactory,
        ModuleNameValidator $moduleNameValidator,
        AclHelper $aclHelper,
        Acl\AclNameValidator $aclNameValidator,
        Acl\DataFactory $aclDataFactory,
        Acl\TitleValidator $titleValidator,
        SortOrderValidator $sortOrderValidator
    ) {
        parent::__construct($component, $moduleNameFactory, $inventStyleFactory, $moduleNameValidator, $aclHelper, $aclNameValidator);
        $this->aclDataFactory = $aclDataFactory;
        $this->titleValidator = $titleValidator;
        $this->sortOrderValidator = $sortOrderValidator;
    }

    protected function configure()
    {
        $this->setName('invent:acl')
            ->setDescription('Create ACL Premission')
            ->addModuleNameArgument()
            ->addArgument('aclName', InputArgument::REQUIRED, 'ACL Name')
            ->addOption('parent', null, InputOption::VALUE_REQUIRED, 'Parent ACL Name', null)
            ->addOption('title', null, InputOption::VALUE_REQUIRED, 'ACL Title', null)
            ->addOption('sortOrder', null, InputOption::VALUE_REQUIRED, 'ACL Sort Order', 10);
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $io = $this->inventStyleFactory->create(compact('input','output'));

        $this->verifyModuleName($io, 'acl');

        $moduleName = $this->moduleNameFactory->create($input->getArgument('moduleName'));

        $question = 'What is the name for the new ACL? (Prefix will be added for you: '.$moduleName->getName().':: )';
        $io->askForValidatedArgument('aclName', $question, null, $this->aclNameValidator, 3);

        $this->verifyAclOption($io, 'parent');

        $aclData = $this->getData($input);

        if (is_null($input->getOption('title'))) {
            if (!$io->confirm('Do you want to use the generated title? "'.$aclData->getTitle().'"', false)) {
                $question = 'What title do you want to use?';
                $io->askForValidatedOption('title', $question, null, $this->titleValidator, 3);
            }
        }

        $question = 'What is the sortOrder of the ACL?';
        $io->askForValidatedOption('sortOrder', $question, 10, $this->sortOrderValidator, 3);
    }

    protected function getData(InputInterface $input)
    {
        return $this->aclDataFactory->create([
            'moduleName' => $this->moduleNameFactory->create($input->getArgument('moduleName')),
            'aclName' => $input->getArgument('aclName'),
            'parentAcl' => $input->getOption('parent'),
            'title' => $input->getOption('title'),
            'sortOrder' => $input->getOption('sortOrder')
        ]);
    }
}