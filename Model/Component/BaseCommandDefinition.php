<?php
namespace Wesleywmd\Invent\Model\Component;

class BaseCommandDefinition
{
    protected $name;

    protected $description;
    
    protected $successMessage;

    protected $help;

    protected $arguments;

    protected $options;

    public function __construct($name, $description, $successMessage, $help = '', $arguments = [], $options = [])
    {
        $this->name = $name;
        $this->description = $description;
        $this->successMessage = $successMessage;
        $this->help = $help;
        $this->arguments = $arguments;
        $this->options = $options;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getSuccessMessage()
    {
        return $this->successMessage;
    }

    public function getHelp()
    {
        return $this->help;
    }

    public function getArguments()
    {
        return $this->arguments;
    }

    public function getOptions()
    {
        return $this->options;
    }
}