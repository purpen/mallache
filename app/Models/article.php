<?php
namespace App\Models;

class Article extends BaseModel
{
    protected $table = 'article';

    protected $fillable = ['title', 'content'];

    /**
     * 一对多相对关分类表classification
     */
    public function classification()
    {
        return $this->belongsTo('App\Models\Classification', 'classification_id');
    }

}