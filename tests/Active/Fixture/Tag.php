<?php

namespace Rougin\Ezekiel\Active\Fixture;

use Rougin\Ezekiel\Active\Model;

/**
 * @package Ezekiel
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class Tag extends Model
{
    /**
     * @var string[]
     */
    protected $fillable = array('id', 'name');

    /**
     * @return \Rougin\Ezekiel\Active\Relations\BelongsToMany
     */
    public function posts()
    {
        return $this->belongsToMany(__NAMESPACE__ . '\Post', 'post_tag', 'tag_id', 'post_id')
            ->withPivot('extra');
    }
}
