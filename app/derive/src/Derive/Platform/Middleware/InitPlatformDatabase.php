<?php namespace Derive\Platform\Middleware;

use Derive\Platform\Entity\Platforms;
use Exception;
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

    public function __construct(Context $context, InitDatabase $initDatabase, Platforms $platforms)
    {
        $this->context = $context;
        $this->initDatabase = $initDatabase;
        $this->platforms = $platforms;
    }

    public function execute(callable $next, $platformId = null)
    {
        if (!isConsole()) {
            /**
             * If request is made directly do derive.foobar.si, we estamblish derive connection.
             * Otherwise we should connect to platform database.
             */
            $platform = (new Platforms())->where('title', $_SERVER['HTTP_HOST'])->one();
            if ($platform) {
                $platformId = $platform->id;
            } else {
                if (!isset($_SESSION['platform_id'])) {
                    $_SESSION['platform_id'] = 1;
                }
                $platformId = $_SESSION['platform_id'];
            }
        } else {
            $_SESSION['platform_id'] = $platformId;
        }

        if (!$platformId) {
            throw new Exception('Platform is missing (InitPlatformDatabase)');
        }

        $platform = $this->platforms->where('id', $platformId)->oneOrFail();

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