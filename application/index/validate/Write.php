<?php
/**
 * Created by PhpStorm.
 * User: bc
 * Date: 2018/3/6
 * Time: 14:46
 */

namespace app\index\validate;


use think\Validate;

class Write extends Validate
{
    protected $rule = [
        'text'=> 'require|max:100' ,
        'm_text1'=> 'require',
        'select_name'=>'require',
    ];
}