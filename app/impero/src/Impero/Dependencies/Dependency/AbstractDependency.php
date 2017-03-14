<?php namespace Impero\Dependencies\Dependency;

use Impero\Services\Service\SshConnection;

abstract class AbstractDependency implements DependencyInterface
{

    /**
     * @var SshConnection
     */
    protected $connection;

    protected $dependency;

    protected $name;

    public function getName()
    {
        return $this->name;
    }

    public function __construct(SshConnection $connection)
    {
        $this->connection = $connection;
    }

    public function getConnection()
    {
        return $this->connection;
    }

    public function isInstalled()
    {
        $this->getConnection()
             ->exec($this->dependency, $error);

        if ($error && strpos($error, 'command not found')) {
            return false;
        }

        return true;
    }

}