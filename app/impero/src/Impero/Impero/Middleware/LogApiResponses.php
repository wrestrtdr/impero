<?php namespace Impero\Impero\Middleware;

use Impero\Impero\Record\ApiRequest;

class LogApiResponses
{

    public function execute(callable $next)
    {
        $url = router()->getUri();
        if (strpos($url, '/api/') === 0) {
            ApiRequest::create([
                                   'created_at' => date('Y-m-d H:i:s'),
                                   'data'       => json_encode(response()->getOutput()),
                                   'ip'         => server('REMOTE_ADDR'),
                                   'url'        => $url . ':response',
                               ]);
        }

        return $next();
    }

}