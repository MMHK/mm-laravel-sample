<?php
/**
 * Created by PhpStorm.
 * User: mixmedia
 * Date: 2019/5/8
 * Time: 10:58
 */

namespace App\Models;


interface IFormFields
{
    /**
     * 获取 grid 配置
     * @return mixed
     */
    static public function map();

    /**
     * 获取 form 配置
     * @return mixed
     */
    public function getFormFields();

    /**
     * 获取 验证规则 配置
     * @return mixed
     */
    public function rules();

    /**
     * 获取 验证提示 配置
     * @return mixed
     */
    public function messages();
}