<?php
namespace App\Models;

class Classification extends BaseModel
{
    protected $table = 'classification';

    protected $fillable = ['type', 'name', 'content'];

    /**
     * 一对多相对关联文章表article
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function article()
    {
        return $this->hasMany('App\Models\Article','classification_id');
    }
}