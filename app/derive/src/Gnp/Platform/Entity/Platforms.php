<?php namespace Gnp\Platform\Entity;

use Gnp\Platform\Record\Platform;
use Pckg\Auth\Record\User;
use Pckg\Database\Entity;
use Pckg\Database\Relation\HasMany;

class Platforms extends Entity
{

    protected $record = Platform::class;

    public function platformsUsers() {
        return $this->hasMany(PlatformsUsers::class)
                    ->foreignKey('platform_id')
                    ->fill('users');
    }

    public function forUser(User $user) {
        return $this->joinPlatformsUsers(
            function(HasMany $platformsUsers) use ($user) {
                $platformsUsers->where('user_id', $user->id);
            }
        );
    }

}