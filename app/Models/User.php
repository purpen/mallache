<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $table = 'users';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'account', 'username', 'email', 'phone', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];


    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }


    /**
     * 获取图片url
     *
     * @return array
     */
    public function getImageAttribute()
    {
        return AssetModel::getImageUrl($this->id, 2, 1, 1);
    }

    /*
     * 一对一关联设计公司
     */
    public function designCompany()
    {
        return $this->hasOne('App\Models\DesignCompanyModel' , 'user_id');
    }

    /*
     * 一对多关联案例
     */
    public function designCase()
    {
        return $this->hasMany('App\Models\DesignCaseModel' , 'user_id');
    }

    /*
     * 一对多关联服务项目
     */
    public function designItem()
    {
        return $this->hasMany('App\Models\DesignItemModel' , 'user_id');
    }

    /*
     * 一对多关联报价
     */
    public function quotation()
    {
        return $this->hasMany('App\Models\QuotationModel' , 'user_id');
    }

}
