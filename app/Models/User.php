<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $table = 'users';

    /**
     * 应该被转换成原生类型的属性。
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'account', 'username', 'email', 'phone', 'password', 'type'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'price_total', 'price_frozen',
    ];

    protected $appends = [
        'logo_image'
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
    public function getLogoImageAttribute()
    {
        return AssetModel::getOneImage($this->logo);
    }

    /*
     * 一对一关联设计公司
     */
    public function designCompany()
    {
        return $this->hasOne('App\Models\DesignCompanyModel', 'user_id');
    }

    /*
     * 一对多关联案例
     */
    public function designCase()
    {
        return $this->hasMany('App\Models\DesignCaseModel', 'user_id');
    }

    /*
     * 一对多关联服务项目
     */
    public function designItem()
    {
        return $this->hasMany('App\Models\DesignItemModel', 'user_id');
    }

    /*
     * 一对多关联报价
     */
    public function quotation()
    {
        return $this->hasMany('App\Models\QuotationModel', 'user_id');
    }

    /**
     * 一对多关联项目需求
     */
    public function item()
    {
        return $this->hasMany('App\Models\Item', 'user_id');
    }

    //一对一关联支付单
    public function payOrder()
    {
        return $this->hasOne('App\Models\PayOrder', 'user_id');
    }

    /**
     * 一对一关联需求公司
     */
    public function demandCompany()
    {
        return $this->hasOne('App\Models\DemandCompany', 'user_id');
    }

    /**
     * 一对多关联提款单
     */
    public function withdrawOrder()
    {
        return $this->hasMany('App\Models\WithdrawOrder', 'user_id');
    }

    /*
     * 一对多关联云盘文件目录表
     */
    public function panDirector()
    {
        return $this->hasMany('App\Models\PanDirector', 'user_id');
    }

    /**
     * 增加用户账户金额（总金额、冻结金额）
     *
     * @param int $user_id
     * @param float $amount
     */
    public function totalAndFrozenIncrease(int $user_id, float $amount)
    {
        $user = User::find($user_id);

        $user->price_total = bcadd($user->price_total, $amount, 2);
        $user->price_frozen = bcadd($user->price_frozen, $amount, 2);
        if (!$user->save()) {
            Log::error('user_id:' . $user_id . '账户金额增加失败');
        }
    }

    /**
     * 减少用户账户金额（总金额、冻结金额）
     *
     * @param int $user_id
     * @param float $amount
     */
    public function totalAndFrozenDecrease(int $user_id, float $amount)
    {
        $user = User::find($user_id);

        $user->price_total = bcsub($user->price_total, $amount, 2);
        $user->price_frozen = bcsub($user->price_frozen, $amount, 2);
        if (!$user->save()) {
            Log::error('user_id:' . $user_id . '账户金额减少失败');
        }
    }

    /**
     * 增加用户账户总金额
     *
     * @param int $user_id
     * @param float $amount
     */
    public function totalIncrease(int $user_id, float $amount)
    {
        $user = User::find($user_id);

        $user->price_total = bcadd($user->price_total, $amount, 2);
        if (!$user->save()) {
            Log::error('user_id:' . $user_id . '账户总金额增加失败');
        }
    }

    /**
     * 减少用户账户总金额
     *
     * @param int $user_id
     * @param float $amount
     */
    public function totalDecrease(int $user_id, float $amount)
    {
        $user = User::find($user_id);

        $user->price_total = bcsub($user->price_total, $amount, 2);
        if (!$user->save()) {
            Log::error('user_id:' . $user_id . '账户总金额减少失败');
        }
    }

    /**
     * 增加账户冻结金额
     *
     * @param int $user_id
     * @param float $amount
     */
    public function frozenIncrease(int $user_id, float $amount)
    {
        $user = User::find($user_id);

        $user->price_frozen = bcadd($user->price_frozen, $amount, 2);
        if (!$user->save()) {
            Log::error('user_id:' . $user_id . '账户冻结金额增加失败');
        }
    }

    /**
     * 减少账户冻结金额
     *
     * @param int $user_id
     * @param float $amount
     */
    public function frozenDecrease(int $user_id, float $amount)
    {
        $user = User::find($user_id);

        $user->price_frozen = bcsub($user->price_frozen, $amount, 2);
        if (!$user->save()) {
            Log::error('user_id:' . $user_id . '账户冻结金额减少失败');
        }
    }


    /**
     * 用户可提现金额
     */
    public function getCashAttribute()
    {
        return bcsub($this->price_total, $this->price_frozen, 2);
    }

    /**
     * 用户是否是管理员
     *
     * @param int $user_id
     * @return bool
     */
    public static function isAdmin(int $user_id)
    {
        $auth = self::find($user_id);
        $is_admin = false;
        if ($auth) {
            if ($auth->role_id > 0) {
                $is_admin = true;
            }
        }
        return $is_admin;
    }

    /**
     * 注销账户
     */
    public function unsetUser()
    {
        $account = '2' . substr($this->account,1);
        $this->account = $account;
        $this->phone = $account;
        $this->email = null;
        $this->status = -1;
        $this->save();
    }

    /**
     * 根据用户id来获取设计公司id
     */
    public static function designCompanyId(int $user_id)
    {
        $user = self::find($user_id);
        if($user){
            $invite_company_id = $user->invite_company_id;
            //被邀请的设计公司等于0的话，就返回主账户的设计公司id
            if($invite_company_id == 0){
                $design_company_id = $user->design_company_id;
                return $design_company_id;
            }else{
                return $invite_company_id;
            }
        }

    }
}
