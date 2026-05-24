<?php

namespace Rougin\Ezekiel\Active\Relations;

use Rougin\Ezekiel\Active\Model;

/**
 * @package Ezekiel
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class HasMany extends Relation
{
    /**
     * @var string
     */
    protected $foreignKey;

    /**
     * @var string
     */
    protected $localKey;

    /**
     * @param \Rougin\Ezekiel\Active\Model $parent
     * @param string                       $related
     * @param string|null                  $foreignKey
     * @param string|null                  $localKey
     */
    public function __construct(Model $parent, $related, $foreignKey = null, $localKey = null)
    {
        parent::__construct($parent, $related);

        $this->foreignKey = $foreignKey ?: $parent->getForeignKey();

        $this->localKey = $localKey ?: $parent->getKeyName();
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
            $key = $model->getAttribute($this->localKey);

            if ($key !== null)
            {
                $keys[] = $key;
            }
        }

        if (! empty($keys))
        {
            $this->query->whereIn($this->foreignKey, $keys);
        }
    }

    /**
     * @return \Rougin\Ezekiel\Active\Model[]
     */
    public function getResults()
    {
        if ($this->results === null)
        {
            $this->results = $this->query
                ->where($this->foreignKey, '=', $this->parent->getAttribute($this->localKey))
                ->get();
        }

        /** @var \Rougin\Ezekiel\Active\Model[] */
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
            $key = $result->getAttribute($this->foreignKey);

            /** @var array<int|string, \Rougin\Ezekiel\Active\Model[]> $dictionary */

            if (! isset($dictionary[$key]))
            {
                $dictionary[$key] = array();
            }

            $dictionary[$key][] = $result;
        }

        foreach ($models as $model)
        {
            $key = $model->getAttribute($this->localKey);

            /** @var array<int|string, \Rougin\Ezekiel\Active\Model[]> $dictionary */

            $value = isset($dictionary[$key]) ? $dictionary[$key] : array();

            $model->setRelation($relation, $value);
        }
    }
}
