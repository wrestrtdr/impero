<?php namespace Impero\Impero\Middleware;

use Impero\Impero\Record\ApiRequest;

class LogApiRequests
{

    public function execute(callable $next)
    {
        $url = router()->getUri();
        if (strpos($url, '/api/') === 0) {
            ApiRequest::create([
                                   'created_at' => date('Y-m-d H:i:s'),
                                   'data'       => json_encode(post()->all()),
                                   'ip'         => server('REMOTE_ADDR'),
                                   'url'        => $url,
                               ]);
        }

        return $next();
    }

}