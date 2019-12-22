<?php
namespace Wesleywmd\Invent\Model\Logger;

use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Api\PhpRendererInterface;
use Wesleywmd\Invent\Model\Component\AbstractPhpRenderer;

class HandlerPhpRenderer extends AbstractPhpRenderer implements PhpRendererInterface
{
    protected function getUseStatements(DataInterface $data)
    {
        return [
            'Magento\Framework\Logger\Handler\Base',
            'Monolog\Logger'
        ];
    }

    protected function getClassStatement(DataInterface $data)
    {
        /** @var Data $data */
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