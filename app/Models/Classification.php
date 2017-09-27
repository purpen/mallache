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

    // 分类名称
    public function getTypeValueAttribute()
    {
        $classification_type = config('constant.classification_type');
        return $classification_type[$this->type] ?? '';
    }
}