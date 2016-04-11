<?php namespace Impero\Apache\Entity;

use Impero\Apache\Record\Site;
use Pckg\Database\Entity;
use Weblab\Auth\Service\Auth;

class Sites extends Entity
{

    protected $record = Site::class;

    public function scopeUserIsAuthorized()
    {
        $auth = resolve(Auth::class);

        if ($auth->hasFlag('user')) {
            /**
             * User has access to it's domains
             */
            return $this->where('user_id', $auth->getUser()->id);

        } else if ($auth->hasFlag('reseller')) {
            /**
             * Reseller has access to it's and sub-user domains
             */
            return $this->where('user_id', $auth->getUser()->id, '=', function ($query) use ($auth) {
                $query->orWhere('user_id', $auth->getUser()->subusers->all()->map('id'));
            });

        } else if ($auth->hasFlag('admin')) {
            /**
             * Admin has access to all domains
             */
            return $this->where('user_id', $auth->getUser()->id, '=');

        } else if ($auth->hasFlag('superadmin')) {
            /**
             * Admin has access to all domains
             */
            return $this;

        }

        return $this->where('1 = 0');
    }

}