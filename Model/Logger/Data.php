<?php
namespace Wesleywmd\Invent\Model\Logger;

use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Model\Component\AbstractData;
use Wesleywmd\Invent\Model\ModuleName;

class Data extends AbstractData implements DataInterface
{
    private $loggerName;

    private $fileName;

    private $type;

    public function __construct(ModuleName $moduleName, $loggerName, $fileName = 'error', $type = 'INFO')
    {
        parent::__construct($moduleName, 'Logger', ['Logger']);
        $this->loggerName = $loggerName;
        $this->fileName = $fileName;
        $this->type = $type;
    }

    public function getLoggerName()
    {
        return $this->moduleName->getSlug([$this->loggerName]);
    }

    public function getFileName()
    {
        return '/var/log/'.str_replace('_', '/', $this->moduleName->getSlug([$this->fileName])).'.log';
    }

    public function getType()
    {
        return $this->type;
    }

    public function getHandlerPath()
    {
        return $this->moduleName->getPath(array_merge($this->directories, ['Handler.php']));
    }

    public function getHandlerInstance()
    {
        return $this->moduleName->getNamespace(array_merge($this->directories,['Handler']));
    }
}