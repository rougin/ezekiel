<?php

namespace Rougin\Ezekiel\Active\Relations;

use Rougin\Ezekiel\Active\Model;

/**
 * @package Ezekiel
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
abstract class Relation
{
    /**
     * @var \Rougin\Ezekiel\Active\Model
     */
    protected $parent;

    /**
     * @var \Rougin\Ezekiel\Active\Builder
     */
    protected $query;

    /**
     * @var string
     */
    protected $related;

    /**
     * @var mixed
     */
    protected $results;

    /**
     * @param \Rougin\Ezekiel\Active\Model[] $models
     *
     * @return void
     */
    abstract public function addEagers(array $models);

    /**
     * @return mixed
     */
    abstract public function getResults();

    /**
     * @param \Rougin\Ezekiel\Active\Model[] $models
     * @param mixed                          $results
     * @param string                         $relation
     *
     * @return void
     */
    abstract public function match(array $models, $results, $relation);

    /**
     * @param \Rougin\Ezekiel\Active\Model $parent
     * @param string                       $related
     */
    public function __construct(Model $parent, $related)
    {
        $this->parent = $parent;

        $this->related = $related;

        /** @var \Rougin\Ezekiel\Active\Model $instance */
        $instance = new $related;

        $pdo = $parent->getPdo();

        if ($pdo)
        {
            $instance->setPdo($pdo);
        }

        $this->query = $instance->newQuery();
    }

    /**
     * @return mixed
     */
    public function getEager()
    {
        return $this->getResults();
    }

    /**
     * @return \Rougin\Ezekiel\Active\Model
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @return \Rougin\Ezekiel\Active\Builder
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @return \Rougin\Ezekiel\Active\Model
     */
    public function getRelated()
    {
        $class = $this->related;

        /** @var \Rougin\Ezekiel\Active\Model $instance */
        $instance = new $class;

        $pdo = $this->parent->getPdo();

        if ($pdo)
        {
            $instance->setPdo($pdo);
        }

        return $instance;
    }

    /**
     * @return string
     */
    public function getRelatedClass()
    {
        return $this->related;
    }

    /**
     * @param \Rougin\Ezekiel\Active\Model[] $models
     * @param string                         $relation
     *
     * @return \Rougin\Ezekiel\Active\Model[]
     */
    public function newRelations(array $models, $relation)
    {
        foreach ($models as $model)
        {
            $model->setRelation($relation, array());
        }

        return $models;
    }
}
