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
    protected $class;

    /**
     * @param \Rougin\Ezekiel\Active\Model $parent
     * @param string                       $class
     * @param string|null                  $foreign
     * @param string|null                  $owner
     */
    public function __construct(Model $parent, $class, $foreign = null, $owner = null)
    {
        /** @var \Rougin\Ezekiel\Active\Model */
        $instance = new $class;

        // Return foreign key from the related model ---
        $default = $instance->getForeignKey();

        $this->foreign = $foreign ? $foreign : $default;
        // ---------------------------------------------

        // Return the owner key from the parent ---
        $default = $instance->getPrimaryKey();

        $this->owner = $owner ? $owner : $default;
        // ----------------------------------------

        $this->parent = $parent;

        $this->class = $class;

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

        $class = $this->class;

        /** @var \Rougin\Ezekiel\Active\Model */
        $model = new $class;

        $model = $model->where($this->owner, $value);

        return $model->first();
    }
}
