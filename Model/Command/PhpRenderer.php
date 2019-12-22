<?php
namespace Wesleywmd\Invent\Model\Command;

use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Api\PhpRendererInterface;
use Wesleywmd\Invent\Model\Component\AbstractPhpRenderer;

class PhpRenderer extends AbstractPhpRenderer implements PhpRendererInterface
{
    protected function getUseStatements(DataInterface $data)
    {
        return [
            'Symfony\Component\Console\Command\Command',
            'Symfony\Component\Console\Input\InputInterface',
            'Symfony\Component\Console\Output\OutputInterface'
        ];
    }

    protected function getClassStatement(DataInterface $data)
    {
        /** @var Data $data */
        return $this->phpBuilder->class($data->getClassName())
            ->extend('Command')
            ->addStmt($this->getConfigureMethod($data))
            ->addStmt($this->getExecuteMethod($data));
    }

    private function getConfigureMethod(DataInterface $data)
    {
        /** @var Data $data */
        return $this->phpBuilder->method('configure')
            ->makeProtected()
            ->setDocComment('/**
                              * TODO implement configure method
                              */')
            ->addStmt($this->phpBuilder->thisMethodCall('setName', [
                $this->phpBuilder->val($data->getCommandName())
            ]));
    }

    private function getExecuteMethod(DataInterface $data)
    {
        /** Data $data */
        return $this->phpBuilder->method('execute')
            ->makeProtected()
            ->setDocComment('/**
                              * TODO implement execute method
                              */')
            ->addParam($this->phpBuilder->param('input')->setType('InputInterface'))
            ->addParam($this->phpBuilder->param('output')->setType('OutputInterface'));
    }
}