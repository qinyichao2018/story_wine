<?php
/**
 * Created by PhpStorm.
 * User: bc
 * Date: 2018/3/6
 * Time: 19:10
 */

namespace app\index\validate;


use think\Validate;

class Pay extends Validate
{
    protected $rule = [
        'recharge_n' => 'between:1,1000',
    ];
}