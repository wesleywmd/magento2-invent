<?php
namespace Wesleywmd\Invent\Model\Logger;

use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Api\PhpRendererInterface;
use Wesleywmd\Invent\Model\PhpParser\PhpBuilder;
use Wesleywmd\Invent\Model\PhpParser\PrettyPrinter;

class HandlerPhpRenderer implements PhpRendererInterface
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
        return $this->phpBuilder->namespace($data->getModuleName()->getNamespace(['Logger']))
            ->addStmt($this->phpBuilder->use('Magento\Framework\Logger\Handler\Base'))
            ->addStmt($this->phpBuilder->use('Monolog\Logger'))
            ->addStmt($this->getClassStatement($data))
            ->getNode();
    }

    private function getClassStatement(Data $data)
    {
        return $this->phpBuilder->class('Handler')
            ->extend('Base')
            ->addStmt($this->phpBuilder->property('loggerType')
                ->makeProtected()
                ->setDefault($this->phpBuilder->classConstFetch('Logger', $data->getType()))
            )
            ->addStmt($this->phpBuilder->property('fileName')
                ->makeProtected()
                ->setDefault($data->getFileName())
            );
    }
}