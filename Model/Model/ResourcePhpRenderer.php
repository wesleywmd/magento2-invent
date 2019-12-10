<?php
namespace Wesleywmd\Invent\Model\Model;

use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Api\PhpRendererInterface;
use Wesleywmd\Invent\Model\PhpParser\PhpBuilder;
use Wesleywmd\Invent\Model\PhpParser\PrettyPrinter;

class ResourcePhpRenderer implements PhpRendererInterface
{
    private $phpBuilder;

    private $prettyPrinter;

    public function __construct(PhpBuilder $phpBuilder, PrettyPrinter $prettyPrinter)
    {
        $this->phpBuilder = $phpBuilder;
        $this->prettyPrinter = $prettyPrinter;
    }

    public function getContents(DataInterface $data)
    {
        return $this->prettyPrinter->print([$this->getBuilderNode($data)]);
    }

    private function getBuilderNode(Data $data)
    {
        return $this->phpBuilder->namespace($data->getModuleName()->getNamespace(['Model','ResourceModel']))
            ->addStmt($this->phpBuilder->use('Magento\Framework\Model\ResourceModel\Db\AbstractDb'))
            ->addStmt($this->phpBuilder->use($data->getModuleName()->getNamespace(['Api','Data',$data->getModelName().'Interface'])))
            ->addStmt($this->getClassStatement($data))
            ->getNode();
    }

    private function getClassStatement(Data $data)
    {
        $tableName = $data->getModuleName()->getSlug([$data->getModelName()]);
        $class = $this->phpBuilder->class($data->getModelName())
            ->extend('AbstractDb')
            ->addStmt($this->phpBuilder->method('_construct')
                ->makeProtected()
                ->addStmt($this->phpBuilder->methodCall($this->phpBuilder->var('this'), '_init', [
                    $this->phpBuilder->classConstFetch($data->getModelName().'Interface','DB_MAIN_TABLE'),
                    $this->phpBuilder->classConstFetch($data->getModelName().'Interface','ENTITY_ID')
                ]))
            );
        return $class;
    }
}