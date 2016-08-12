<?php namespace Impero\Apache\Entity;

use Impero\Apache\Record\Site;
use Pckg\Auth\Entity\Users;
use Pckg\Auth\Service\Auth;
use Pckg\Database\Entity;
use Pckg\Dynamic\Entity\Snippet\EntityActions;
use Pckg\Maestro\Service\Contract\Entity as MaestroEntity;

class Sites extends Entity implements MaestroEntity
{

    use EntityActions;

    protected $record = Site::class;

    /**
     * Build edit url.
     *
     * @return string
     */
    public function getAddUrl()
    {
        return url('apache.add');
    }

    public function user()
    {
        return $this->belongsTo(Users::class)
                    ->foreignKey('user_id')
                    ->fill('user', 'sites');
    }

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
            return $this->where(
                'user_id',
                $auth->getUser()->id,
                '=',
                function($query) use ($auth) {
                    $query->orWhere('user_id', $auth->getUser()->subusers->all()->map('id'));
                }
            );

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