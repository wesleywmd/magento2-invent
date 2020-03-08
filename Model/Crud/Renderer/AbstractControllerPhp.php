<?php
namespace Wesleywmd\Invent\Model\Crud\Renderer;

use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Model\Component\AbstractPhpRenderer;
use Wesleywmd\Invent\Model\Crud;
use Wesleywmd\Invent\Model\Model;

abstract class AbstractControllerPhp extends AbstractPhpRenderer
{
    protected $className;

    protected $classExtends;

    protected $classImplements;

    public function getPath(DataInterface $data)
    {
        /** @var Crud\Data $data */
        return $data->getAdminhtmlControllerPath($this->className);
    }

    protected function getNamespace(DataInterface $data)
    {
        /** @var Crud\Data $data */
        return $data->getAdminhtmlControllerNamespace();
    }

    protected function getClassStatement(DataInterface $data)
    {
        /** @var Crud\Data $data */
        $class = $this->phpBuilder->class($this->className)
            ->addStmts($this->getClassStmts($data));
        if (!is_null($this->classExtends)) {
            $class->extend($this->classExtends);
        }
        if (!is_null($this->classImplements)) {
            $class->implement($this->classImplements);
        }
        return $class;
    }

    protected function getClassStmts(DataInterface $data)
    {
        return [];
    }

    protected function getAdminResourceConstant($menuResource, $extension = '')
    {
        return $this->phpBuilder->const('ADMIN_RESOURCE', $menuResource . $extension);
    }

    protected function thisGetRequest()
    {
        return $this->phpBuilder->thisMethodCall('getRequest');
    }

    protected function thisGetRequestParam($param, $default = null)
    {
        if (is_string($param)) {
            $param = $this->phpBuilder->val($param);
        }
        $args = [$param];
        if (!is_null($default)) {
            if (!is_object($default)) {
                $default = $this->phpBuilder->val($default);
            }
            $args[] = $default;
        }
        return $this->phpBuilder->methodCall($this->thisGetRequest(), 'getParam', $args);
    }

    protected function thisGetRequestParams()
    {
        return $this->phpBuilder->methodCall($this->thisGetRequest(), 'getParams');
    }

    protected function assignModel(Model\Data $model)
    {
        return $this->phpBuilder->assign(
            $this->varModel($model),
            $this->phpBuilder->methodCall($this->thisRepository($model), 'get', [$this->varEntityId()])
        );
    }

    protected function assignResultRedirect()
    {
        return $this->phpBuilder->assign(
            $this->varResultRedirect(),
            $this->phpBuilder->methodCall(
                $this->phpBuilder->thisPropertyFetch('resultRedirectFactory'),
                'create'
            )
        );
    }

    protected function returnResultRedirectGetPath($path, $params = [])
    {
        return $this->phpBuilder->methodCall(
            $this->varResultRedirect(),
            'setPath',
            array_merge([$this->phpBuilder->val($path)], $params)
        );
    }

    protected function thisMessageManagerCall($name, $phrase, $arguments = [])
    {
        return $this->phpBuilder->methodCall($this->thisMessageManager(), $name, [
            $this->phpBuilder->translate($phrase, $arguments)
        ]);
    }

    protected function varEntityId()
    {
        return $this->phpBuilder->var('entityId');
    }

    protected function varResultRedirect()
    {
        return $this->phpBuilder->var('resultRedirect');
    }

    protected function thisMessageManager()
    {
        return $this->phpBuilder->thisPropertyFetch('messageManager');
    }

    protected function varModel(Model\Data $model)
    {
        return $this->phpBuilder->var($model->getVar());
    }

    protected function thisRepository(Model\Data $model)
    {
        return $this->phpBuilder->thisPropertyFetch($model->getRepositoryVar());
    }
}