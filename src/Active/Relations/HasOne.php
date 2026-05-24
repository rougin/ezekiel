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
class HasOne
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
    protected $local;

    /**
     * @var \Rougin\Ezekiel\Active\Model
     */
    protected $parent;

    /**
     * @var string
     */
    protected $class;

    /**
     * @var \Rougin\Ezekiel\Active\Model|null
     */
    protected $results = null;

    /**
     * @param \Rougin\Ezekiel\Active\Model $parent
     * @param string                       $class
     * @param string|null                  $foreign
     * @param string|null                  $local
     */
    public function __construct(Model $parent, $class, $foreign = null, $local = null)
    {
        // Return foreign key from the parent model ---
        $default = $parent->getForeignKey();

        $this->foreign = $foreign ? $foreign : $default;
        // ---------------------------------------------

        // Return key from the parent model ------
        $default = $parent->getPrimaryKey();

        $this->local = $local ? $local : $default;
        // ---------------------------------------

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
        $value = $this->parent->{$this->local};

        if ($value === null)
        {
            return null;
        }

        $class = $this->class;

        /** @var \Rougin\Ezekiel\Active\Model */
        $model = new $class;

        return $model->where($this->foreign, $value)->first();
    }
}
