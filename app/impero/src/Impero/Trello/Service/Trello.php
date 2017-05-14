<?php namespace Impero\Trello\Service;

use Trello\Trello as TrelloClient;

class Trello
{

    /**
     * @var TrelloClient
     */
    protected $trello;

    public function __construct()
    {
        $this->trello = new TrelloClient(config('trello.key'), null, config('trello.token'));
    }

    /**
     * @return TrelloClient
     */
    public function getTrello()
    {
        return $this->trello;
    }

}