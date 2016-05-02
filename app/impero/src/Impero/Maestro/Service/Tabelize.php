<?php namespace Impero\Maestro\Service;

use Pckg\Database\Entity;

class Tabelize
{

    /**
     * @var Entity
     */
    protected $entity;

    /**
     * @var array
     */
    protected $fields = [];

    /**
     * @var array
     */
    protected $recordActions = [
        'edit',
        'delete',
    ];

    /**
     * @var array
     */
    protected $entityActions = [
        'add',
    ];

    /**
     * @var string
     */
    protected $title;

    public function __construct(Entity $entity = null, $fields = [])
    {
        $this->entity = $entity;
        $this->fields = $fields;
    }

    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setEntity(Entity $entity)
    {
        $this->entity = $entity;

        return $this;
    }

    public function getEntity()
    {
        return $this->entity;
    }

    public function setFields(array $fields = [])
    {
        $this->fields = $fields;

        return $this;
    }

    public function getFields()
    {
        return $this->fields;
    }

    public function setRecordActions(array $recordActions = [])
    {
        $this->recordActions = $recordActions;

        return $this;
    }

    public function getRecordActions()
    {
        return $this->recordActions;
    }

    public function setEntityActions(array $entityActions = [])
    {
        $this->entityActions = $entityActions;

        return $this;
    }

    public function getEntityActions()
    {
        return $this->entityActions;
    }

    public function getRecords()
    {
        return $this->entity->all();
    }

    public function __toString()
    {
        return (string)view('Impero/Maestro:tabelize', [
            'recordActions' => $this->recordActions,
            'entityActions' => $this->entityActions,
            'records'       => $this->entity->all(),
            'tabelize'      => $this,
        ]);
    }

}