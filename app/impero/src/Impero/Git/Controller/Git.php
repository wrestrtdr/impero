<?php namespace Impero\Git\Controller;

use Pckg\Framework\Controller;

class Git extends Controller
{

    public function postWebhookAction() {
        file_put_contents('/tmp/webhook.post.log', json_encode($_POST));
        dd('OK?');
        queue()->create('git:pull');

    }

}