<?php

namespace Rougin\Ezekiel\Active\Relations;

use Rougin\Ezekiel\Active\Depot;
use Rougin\Ezekiel\Active\Manager;
use Rougin\Ezekiel\Active\Model;

/**
 * @package Ezekiel
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class BelongsTo
{
    /**
     * @var \Rougin\Ezekiel\Active\Depot
     */
    protected $depot;

    /**
     * @var string
     */
    protected $foreign;

    /**
     * @var string
     */
    protected $owner;

    /**
     * @var \Rougin\Ezekiel\Active\Model
     */
    protected $parent;

    /**
     * @var string
     */
    protected $related;

    /**
     * @var \Rougin\Ezekiel\Active\Model|null
     */
    protected $results = null;

    /**
     * @param \Rougin\Ezekiel\Active\Model $parent
     * @param string                       $related
     * @param string|null                  $foreign
     * @param string|null                  $owner
     */
    public function __construct(Model $parent, $related, $foreign = null, $owner = null)
    {
        /** @var \Rougin\Ezekiel\Active\Model */
        $instance = new $related;

        $this->foreign = $foreign ?: $instance->getForeignKey();

        $this->owner = $owner ?: $instance->getKeyName();

        $this->parent = $parent;

        $this->related = $related;

        $name = $parent->getConnectionName();

        $this->depot = new Depot(new Manager, $name);
    }

    /**
     * @return \Rougin\Ezekiel\Active\Model|null
     */
    public function getResults()
    {
        $value = $this->parent->{$this->foreign};

        if ($value === null)
        {
            return null;
        }

        $related = $this->related;

        /** @var \Rougin\Ezekiel\Active\Model */
        $model = new $related;

        return $model->where($this->owner, $value)->first();
    }
}
