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
class HasMany
{
    /**
     * @var \Rougin\Ezekiel\Active\Depot
     */
    protected $depot;

    /**
     * @var string
     */
    protected $foreignKey;

    /**
     * @var string
     */
    protected $localKey;

    /**
     * @var \Rougin\Ezekiel\Active\Model
     */
    protected $parent;

    /**
     * @var string
     */
    protected $related;

    /**
     * @var \Rougin\Ezekiel\Active\Model[]
     */
    protected $results = array();

    /**
     * @param \Rougin\Ezekiel\Active\Model $parent
     * @param string                       $related
     * @param string|null                  $foreignKey
     * @param string|null                  $localKey
     */
    public function __construct(Model $parent, $related, $foreignKey = null, $localKey = null)
    {
        $this->foreignKey = $foreignKey ?: $parent->getForeignKey();

        $this->localKey = $localKey ?: $parent->getKeyName();

        $this->parent = $parent;

        $this->related = $related;

        $name = $parent->getConnectionName();

        $this->depot = new Depot(new Manager, $name);
    }

    /**
     * @return \Rougin\Ezekiel\Active\Model[]
     */
    public function getResults()
    {
        $value = $this->parent->{$this->localKey};

        if ($value === null)
        {
            return array();
        }

        $related = $this->related;

        /** @var \Rougin\Ezekiel\Active\Model */
        $model = new $related;

        return $model->where($this->foreignKey, $value)->get();
    }
}
