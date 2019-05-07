<?php
namespace Wesleywmd\Invent\Api\Data;

interface DomInterface
{
    public function updateElement($node, $key = null, $value = null, $text = null, $xpath = []);
    public function updateAttribute($attribute, $value, $xpath = []);
}