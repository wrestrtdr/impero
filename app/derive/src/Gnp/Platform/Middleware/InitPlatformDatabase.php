<?php namespace Gnp\Platform\Middleware;

use Gnp\Platform\Entity\Platforms;
use Pckg\Concept\Context;
use Pckg\Concept\Reflect;
use Pckg\Database\Command\InitDatabase;
use Pckg\Database\Repository;

class InitPlatformDatabase
{

    /**
     * @var Context
     */
    protected $context;

    /**
     * @var InitDatabase
     */
    protected $initDatabase;

    /**
     * @var Platforms
     */
    protected $platforms;

    public function __construct(Context $context, InitDatabase $initDatabase, Platforms $platforms) {
        $this->context = $context;
        $this->initDatabase = $initDatabase;
        $this->platforms = $platforms;
    }

    public function execute(callable $next) {
        if (!isset($_SESSION['platform_id'])) {
            $_SESSION['platform_id'] = 1;
        }

        $platform = $this->platforms->where('id', $_SESSION['platform_id'])->one();

        $this->context->bind(
            Repository::class . '.gnp',
            $this->initDatabase->initPdoDatabase(
                $platform->getDatabaseConfig(),
                'gnp'
            )
        );

        return $next();
    }

}