<?php namespace Impero\Git\Controller;

use Pckg\Framework\Controller;

class Git extends Controller
{

    public function getWebhookAction() {
        file_put_contents('/tmp/webhook.post.log', json_encode($_POST));
        file_put_contents('/tmp/webhook.get.log', json_encode($_GET));
        dd('OK?');
        queue()->create('git:pull');

    }

}