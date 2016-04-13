<?php namespace Impero\Ftp\Record\Ftp;

use Impero\Ftp\Entity\Ftps;
use Impero\Ftp\Record\Ftp;
use Pckg\Concept\Reflect\Resolver as ResolverInterface;
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
     * @var Ftps
     */
    protected $ftps;

    /**
     * @var Response
     */
    protected $response;

    public function __construct(Router $router, Auth $auth, Ftps $ftps, Response $response)
    {
        $this->router = $router;
        $this->auth = $auth;
        $this->ftps = $ftps;
        $this->response = $response;
    }

    /**
     * @T00D00 - parameter from router (let's say it's 'zobnascetka-si-schtr4jh-3') should already be passed to resolve method.
     * We should also know which class are we resolving (Record\Ftp::class)
     */
    public function resolve($class)
    {
        if ($class != Ftp::class) {
            /**
             * Other resolver should resolve this.
             */
            return;
        }
        if (!$id = $this->router->get('ftp')) {
            /**
             * How should we handle this?
             */
            $this->response->bad('Ftp parameter is missing ...');
        }

        /**
         * We should resolve parameter or throw exception.
         */
        return $this->ftps->where('id', $id)
            ->userIsAuthorized()
            ->oneOrFail(function () {
                $this->response->unauthorized('Ftp account not found');
            });
    }

}
