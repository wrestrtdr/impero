<?php namespace Impero\Apache\Record\Site;

use Impero\Apache\Entity\Sites;
use Impero\Apache\Record\Site;
use Pckg\Concept\Reflect;
use Pckg\Concept\Reflect\Resolver as ResolverInterface;
use Pckg\Database\Record;
use Pckg\Framework\Response;
use Pckg\Framework\Router;
use Weblab\Auth\Service\Auth;

class Resolver implements ResolverInterface
{

    /**
     * @var Router
     */
    protected $router;

    /**
     * @var Auth
     */
    protected $auth;

    /**
     * @var Sites
     */
    protected $sites;

    /**
     * @var Response
     */
    protected $response;

    public function __construct(Router $router, Auth $auth, Sites $sites, Response $response)
    {
        $this->router = $router;
        $this->auth = $auth;
        $this->sites = $sites;
        $this->response = $response;
    }

    /**
     * @T00D00 - parameter from router (let's say it's 'zobnascetka-si-schtr4jh-3') should already be passed to resolve method.
     * We should also know which class are we resolving (Record\Site::class)
     */
    public function resolve($class)
    {
        if ($class != Site::class) {
            /**
             * Other resolver should resolve this.
             */
            return;
        }
        if (!$id = $this->router->get('site')) {
            /**
             * How should we handle this?
             */
            $this->response->bad('Site parameter is missing ...');
        }

        /**
         * We should resolve parameter or throw exception.
         */
        return $this->sites->where('id', $id)
            ->userIsAuthorized()
            ->oneOrFail(function () {
                $this->response->unauthorized('Site not found');
            });
    }

}
