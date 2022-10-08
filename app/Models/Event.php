<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Event extends Model
{

    /**
     * Relationship with Workshop model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */

    public function workshops(){
        return $this->hasMany('App\Models\Workshop');
    }
}
