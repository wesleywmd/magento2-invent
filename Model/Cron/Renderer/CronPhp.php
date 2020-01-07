<?php
namespace Wesleywmd\Invent\Model\Cron\Renderer;

use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Api\RendererInterface;
use Wesleywmd\Invent\Model\Component\AbstractPhpRenderer;
use Wesleywmd\Invent\Model\PhpParser\PhpBuilder;

class CronPhp extends AbstractPhpRenderer implements RendererInterface
{
    protected function getUseStatements(DataInterface $data)
    {
        return ['Psr\Log\LoggerInterface'];
    }

    protected function getClassStatement(DataInterface $data)
    {
        /** @var Data $data */
        return $this->phpBuilder->class($data->getClassName())
            ->setDocComment('/**
                              * TODO implement '.$data->getCronName().' class body
                              */')
            ->addStmt($this->phpBuilder->property('logger')->makeProtected())
            ->addStmt($this->phpBuilder->constructor(['logger'=>'LoggerInterface']))
            ->addStmt($this->getExecuteMethod($data));
    }

    private function getExecuteMethod(DataInterface $data)
    {
        /** @var Data $data */
        return $this->phpBuilder->method($data->getMethod())
            ->makePublic()
            ->setDocComment('/**
                              * TODO implement '.$data->getMethod().' method
                              */')
            ->addStmts([
                $this->phpBuilder->methodCall(
                    $this->phpBuilder->thisPropertyFetch('logger'),
                    'info', [
                        $this->phpBuilder->magicConstant(PhpBuilder::MAGIC_CONST_METHOD)
                    ]
                ),
                $this->phpBuilder->returnStmt($this->phpBuilder->var('this'))
            ]);
    }
}