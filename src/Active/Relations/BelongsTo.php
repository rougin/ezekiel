<?php

namespace Rougin\Ezekiel\Active\Relations;

use Rougin\Ezekiel\Active\Model;

/**
 * @package Ezekiel
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class BelongsTo extends Relation
{
    /**
     * @var string
     */
    protected $foreign;

    /**
     * @var string
     */
    protected $owner;

    /**
     * @param \Rougin\Ezekiel\Active\Model $parent
     * @param string                       $related
     * @param string|null                  $foreign
     * @param string|null                  $owner
     */
    public function __construct(Model $parent, $related, $foreign = null, $owner = null)
    {
        parent::__construct($parent, $related);

        /** @var \Rougin\Ezekiel\Active\Model */
        $instance = new $related;

        $pdo = $parent->getPdo();

        if ($pdo)
        {
            $instance->setPdo($pdo);
        }

        if (! $foreign)
        {
            $this->foreign = $instance->getForeignKey();
        }

        if (! $owner)
        {
            $this->owner = $instance->getKeyName();
        }
    }

    /**
     * @param \Rougin\Ezekiel\Active\Model[] $models
     *
     * @return void
     */
    public function addEagers(array $models)
    {
        $keys = array();

        foreach ($models as $model)
        {
            $key = $model->getAttribute($this->foreign);

            if ($key !== null)
            {
                $keys[] = $key;
            }
        }

        if (! empty($keys))
        {
            $this->query->whereIn($this->owner, $keys);
        }
    }

    /**
     * @return \Rougin\Ezekiel\Active\Model|null
     */
    public function getResults()
    {
        if ($this->results === null)
        {
            $this->results = $this->query
                ->where($this->owner, '=', $this->parent->getAttribute($this->foreign))
                ->first();
        }

        /** @var \Rougin\Ezekiel\Active\Model|null */
        return $this->results;
    }

    /**
     * @param \Rougin\Ezekiel\Active\Model[] $models
     * @param mixed                          $results
     * @param string                         $relation
     *
     * @return void
     */
    public function match(array $models, $results, $relation)
    {
        $dictionary = array();

        /** @var \Rougin\Ezekiel\Active\Model[] $resultList */
        $resultList = is_array($results) ? $results : array();

        foreach ($resultList as $result)
        {
            $key = $result->getAttribute($this->owner);

            /** @var array<int|string, \Rougin\Ezekiel\Active\Model> $dictionary */

            $dictionary[$key] = $result;
        }

        foreach ($models as $model)
        {
            $key = $model->getAttribute($this->foreign);

            /** @var array<int|string, \Rougin\Ezekiel\Active\Model> $dictionary */

            $value = isset($dictionary[$key]) ? $dictionary[$key] : null;

            $model->setRelation($relation, $value);
        }
    }
}
