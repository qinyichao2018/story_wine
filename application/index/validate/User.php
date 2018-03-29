<?php
/**
 * Created by PhpStorm.
 * User: bc
 * Date: 2018/2/27
 * Time: 14:59
 */

namespace app\index\validate;


use think\Validate;

class User extends Validate
{
    protected $rule = [
        'username' => 'require|max:20',
        'password' => 'require|max:16',
    ];
}