<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    /**
     * Relationship with MenuItem model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */

    public function subMenus(){
        return $this->hasMany('App\models\MenuItem','parent_id','id');
    }

}
