<?php namespace Impero\Servers\Entity;

use Impero\Dependencies\Entity\Dependencies;
use Impero\Servers\Record\Server;
use Impero\Services\Entity\Services;
use Pckg\Database\Entity;
use Pckg\Generic\Entity\ListItems;

class Servers extends Entity
{

    protected $record = Server::class;

    public function tags()
    {
        return $this->hasMany(Tags::class)
                    ->foreignKey('server_id');
    }

    public function system()
    {
        return $this->belongsTo(Systems::class)
                    ->foreignKey('system_id');
    }

    public function status()
    {
        return $this->belongsTo(ListItems::class)
                    ->foreignKey('status')
                    ->primaryKey('slug')
                    ->where('list_items.list_id', 'servers.status');
    }

    public function services()
    {
        return $this->hasAndBelongsTo(Services::class)
                    ->over(ServersServices::class)
                    ->leftForeignKey('server_id')
                    ->rightForeignKey('service_id');
    }

    public function dependencies()
    {
        return $this->hasAndBelongsTo(Dependencies::class)
                    ->over(ServersDependencies::class)
                    ->leftForeignKey('server_id')
                    ->rightForeignKey('dependency_id');
    }

}