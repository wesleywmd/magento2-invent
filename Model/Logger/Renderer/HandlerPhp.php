<?php
namespace Wesleywmd\Invent\Model\Logger\Renderer;

use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Api\RendererInterface;
use Wesleywmd\Invent\Model\Component\AbstractPhpRenderer;

class HandlerPhp extends AbstractPhpRenderer implements RendererInterface
{
    public function getPath(DataInterface $data)
    {
        /** @var Data $data */
        return $data->getHandlerPath();
    }

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