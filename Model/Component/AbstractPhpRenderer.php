<?php
namespace Wesleywmd\Invent\Model\Component;

use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Api\PhpRendererInterface;
use Wesleywmd\Invent\Model\PhpParser\PhpBuilder;
use Wesleywmd\Invent\Model\PhpParser\PrettyPrinter;

abstract class AbstractPhpRenderer
{
    protected $phpBuilder;

    protected $prettyPrinter;

    public function __construct(PhpBuilder $phpBuilder, PrettyPrinter $prettyPrinter)
    {
        $this->phpBuilder = $phpBuilder;
        $this->prettyPrinter = $prettyPrinter;
    }

    public function getContents(DataInterface $data)
    {
        return $this->prettyPrinter->print([$this->getBuilderNode($data)]);
    }

    protected function getBuilderNode(DataInterface $data)
    {
        $namespaceBuilder = $this->phpBuilder->namespace($this->getNamespace($data));
        foreach ($this->getUseStatements($data) as $as=>$useStatement) {
            $use = $this->phpBuilder->use($useStatement);
            if (is_string($as)) {
                $use->as($as);
            }
            $namespaceBuilder->addStmt($use);
        }
        $namespaceBuilder->addStmt($this->getClassStatement($data));
        return $namespaceBuilder->getNode();
    }

    protected function getNamespace(DataInterface $data)
    {
        return $data->getNamespace();
    }

    protected function getUseStatements(DataInterface $data)
    {
        return [];
    }
    
    abstract protected function getClassStatement(DataInterface $data);
}