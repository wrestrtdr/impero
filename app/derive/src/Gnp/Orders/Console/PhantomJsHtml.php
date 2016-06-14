<?php namespace Gnp\Orders\Console;

use JonnyW\PhantomJs\Http\ResponseInterface;
use JonnyW\PhantomJs\Procedure\OutputInterface;

class PhantomJsHtml implements ResponseInterface, OutputInterface
{

    public function import(array $data) {
        return $data;
    }

    public function getHeaders() {
        return [];
    }

    public function getHeader($code) {
        return null;
    }

    public function getStatus() {
        return 200;
    }

    public function getContent() {
        return '<p>This is content</p>';
    }

    public function getContentType() {
        return 'text/html; charset=UTF-8';
    }

    public function getUrl() {
        return null;
    }

    public function getRedirectUrl() {
        return null;
    }

    public function isRedirect() {
        return false;
    }

    public function getTime() {
        return (date('Y-m-d H:i:s'));
    }

}