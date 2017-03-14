<?php namespace Impero\Dependencies\Dependency;

interface DependencyInterface
{

    public function getName();

    public function getStatus();

    public function getVersion();

}