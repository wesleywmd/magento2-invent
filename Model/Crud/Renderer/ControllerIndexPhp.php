<?php
namespace Wesleywmd\Invent\Model\Crud\Renderer;

use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Api\RendererInterface;
use Wesleywmd\Invent\Model\Crud;

class ControllerIndexPhp extends AbstractControllerPhp implements RendererInterface
{
    protected $className = 'Index';

    protected $classExtends = 'AbstractController';

    protected function getClassStmts(DataInterface $data)
    {
        /** @var Crud\Data $data */
        return [$this->getExecuteMethod($data->getModel()->getModelName())];
    }

    protected function getExecuteMethod($modelName)
    {
        return $this->phpBuilder->method('execute')
            ->makePublic()
            ->addStmts([
                $this->phpBuilder->thisMethodCall('initPage'),
                $this->phpBuilder->thisMethodCall('prependTitle', [$this->phpBuilder->translate($modelName)]),
                $this->phpBuilder->thisMethodCall('renderPage'),
            ]);
    }
}