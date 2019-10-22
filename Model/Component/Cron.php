<?php
namespace Wesleywmd\Invent\Model\Component;

use Wesleywmd\Invent\Api\Data\DomInterface;
use Wesleywmd\Invent\Api\Data\XmlFileInterface;

class Cron
{
    private $directoryList;

    private $phpBuilderFactory;
    
    private $prettyPrinter;
    
    private $xmlFileFactory;
    
    private $xmlDomFactory;
    
    private $moduleName;

    private $cronName;

    private $method;

    private $schedule;

    private $group;

    public function __construct(
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Wesleywmd\Invent\Model\PhpParser\PhpBuilderFactory $phpBuilderFactory,
        \Wesleywmd\Invent\Model\PhpParser\PrettyPrinter $prettyPrinter,
        \Wesleywmd\Invent\Model\ModuleForge\XmlFileFactory $xmlFileFactory,
        \Wesleywmd\Invent\Model\ModuleForge\XmlFile\DomFactory $xmlDomFactory,
        $data = []
    ) {
        $this->directoryList = $directoryList;
        $this->phpBuilderFactory = $phpBuilderFactory;
        $this->prettyPrinter = $prettyPrinter;
        $this->xmlFileFactory = $xmlFileFactory;
        $this->xmlDomFactory = $xmlDomFactory;
        $this->moduleName = $data['moduleName'];
        $this->cronName = $data['cronName'];
        $this->method = $data['method'];
        $this->schedule = $data['schedule'];
        $this->group = $data['group'];
    }
    
    public function addToModule()
    {
        $directories = explode("/", $this->cronName);
        $className = ucfirst(array_pop($directories));
        $directories = array_map( function($dir) { return ucfirst($dir); }, $directories);
        $directories = array_merge(["Cron"], $directories);

        $phpString = $this->getPhpString($className, $directories);
        $this->makeFile($phpString, $className.'.php', $directories);

        /** @var XmlClassInterface $crontabXml */
        $crontabXml = $this->xmlFileFactory->create(['moduleName'=>$this->moduleName, 'type'=>XmlFileInterface::TYPE_CRONTAB]);
        $xmlString = $this->getXmlString($crontabXml, $this->getInstance($className, $directories));
        $this->makeFile($xmlString, $crontabXml->getFileName(), $crontabXml->getDirectories());
    }
    
    protected function getInstance($className, $directories)
    {
        $instance = str_replace('_', '\\', $this->moduleName);
        foreach( $directories as $dir ) {
            $instance .= '\\' . ucfirst($dir);
        }
        return $instance . '\\' . $className;
    }

    protected function getXmlString($xmlFile, $instance)
    {
        /** @var DomInterface $crontabXmlDom */
        $crontabXmlDom = $this->xmlDomFactory->create(['xmlFile'=>$xmlFile]);
        $jobName = strtolower($this->moduleName . '_' . $this->cronName);
        $crontabXmlDom->updateElement("group", "id", $this->group)
            ->updateElement("job", "name", $jobName, null, ["group[@id=\"$this->group\"]"])
            ->updateAttribute("instance", $instance, ["group[@id=\"$this->group\"]", "job[@name=\"$jobName\"]"])
            ->updateAttribute("method", $this->method, ["group[@id=\"$this->group\"]", "job[@name=\"$jobName\"]"])
            ->updateElement("schedule", null, null, $this->schedule, ["group[@id=\"$this->group\"]", "job[@name=\"$jobName\"]"]);
        return $crontabXmlDom->toString();
    }
    
    protected function getPhpString($className, $directories)
    {
        $factory = $this->phpBuilderFactory->create();
        $node = $factory->moduleNamespace($this->moduleName, $directories)
            ->addStmts([
                $factory->use('\Psr\Log\LoggerInterface'),
                $factory->class($className)->addStmts([
                    $factory->construct([
                        $factory->param('logger')->setType('LoggerInterface')
                    ]),
                    $factory->method($this->method)
                        ->makePublic()
                        ->setDocComment('/**
                              * TODO implement '.$this->method.' method
                              */')
                        ->addStmts([
                            new \PhpParser\Node\Expr\MethodCall(
                                new \PhpParser\Node\Expr\PropertyFetch($factory->var('this'), 'logger'),
                                'info',
                                [new \PhpParser\Node\Arg(new \PhpParser\Node\Scalar\MagicConst\Method())]
                            ),
                            new \PhpParser\Node\Stmt\Return_($factory->var('this'))
                        ]),
                    $factory->property('logger')->makeProtected()
                ])
            ])
            ->getNode();

        return $this->prettyPrinter->print([$node]);
    }

    protected function makeFile($contents, $fileName, $directories = [])
    {
        $directoryPath = implode(DIRECTORY_SEPARATOR,[
            $this->directoryList->getPath('app'), 'code',
            str_replace('_', DIRECTORY_SEPARATOR, $this->moduleName)
        ]);

        if( !empty($directories) ) {
            $directoryPath .= DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $directories);
        }

        if( !is_dir( $directoryPath) ) {
            mkdir($directoryPath, 0777, true);
        }

        $handle = fopen($directoryPath . DIRECTORY_SEPARATOR . $fileName, 'w');
        fwrite($handle, $contents);
        fclose($handle);
    }
}