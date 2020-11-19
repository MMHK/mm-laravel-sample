<?php
namespace App\Helper;

use Illuminate\Support\Str;

use Carbon\Carbon;

/**
 * Created by PhpStorm.
 * User: mixmedia
 * Date: 2018/4/17
 * Time: 14:09
 */
class Validator
{

    static protected $only = [];




    /**
     * 验证在给定的匹配项目中是唯一值
     * @param $attribute
     * @param mixed $value
     * @param array $parameters  0 => 给定的匹配项目
     * @param $validator
     * @return bool
     */
    public function onlyIn($attribute, $value, $parameters, $validator) {
        /**
         * @var $validator \Illuminate\Validation\Validator
         */
        $match_count = collect(\Arr::dot($validator->getData()))
            ->filter(function ($val, $key) use ($parameters, $value) {
                return Str::is($parameters[0], $key) && $val == $value;
            })
            ->count();

        return $match_count <= 1;
    }

    /**
     * 验证在给定的匹配项目中是唯一值, 除了例外值
     *
     * @param $attribute
     * @param mixed $value
     * @param array $parameters 0 => 给定的匹配项目， 1 => 例外值
     * @param $validator
     * @return bool
     */
    public function onlyInExceptValue($attribute, $value, $parameters, $validator) {
        /**
         * @var $validator \Illuminate\Validation\Validator
         */
        /**
         * 排除例外值
         */
        if ($parameters[1] == $value) {
            return true;
        }
        $match_count = collect(\Arr::dot($validator->getData()))
            ->filter(function ($val, $key) use ($parameters, $value) {
                return Str::is($parameters[0], $key) && $val == $value;
            })
            ->count();

        return $match_count <= 1;
    }

    /**
     * 计算年龄 7-50 岁
     *
     * @param $attribute
     * @param $value
     * @param $parameters [0] 最小值 $parameters[1]最大值（）
     * @param $validator
     * @return bool
     */
    public  function age($attribute, $value, $parameters, $validator) {
        $min = 0;
        $max = 0;
        if (count($parameters) > 0) {
            $min = $parameters[0];
        }
        if (count($parameters) > 1) {
            $max = $parameters[1];
        }

        try {
            $age = Carbon::createFromFormat('Y-m-d', $value)->age;
            if ($max) {
                return ($age <= $max && $age >= $min);
            }
            if ($min) {
                return ($age >= $min);
            }
        } catch (\Exception $e) {
            return false;
        }

        return false;
    }

    /**
     * 验证信用卡  卡号和类型
     *
     * @param $attribute
     * @param $value
     * @param $parameters
     * @param $validator
     * @return bool
     */
    public function credit($attribute, $value, $parameters, $validator)
    {
        $array = \Inacho\CreditCard::validCreditCard($value);
        if( $array['valid'] !==false )
        {
            return  in_array($array['type'],$parameters) ;
        }
        return false;
    }

    /**
     * 值不重复
     *
     * @param $attribute
     * @param $value
     * @param $parameters  $parameters可以让特殊字段可以重复出现
     * @param $validator
     * @return bool
     */
    public function no_repeat($attribute, $value, $parameters, $validator)
    {
        static $array = [];

        try{
            if( empty($value) )return false;
            $array[] = $value;
            $data = array_count_values($array);
        }catch (\Exception $e){
            return false;
        }



        if( $data[ $value ] >= 2 ){
            if( in_array($value,$parameters)   ){
                return true;
            }
            return false;
        }
        return true;
    }



    /**
     * 检测唯一（非数据库查询）
     * @param string $attribute
     * @param mixed $value
     * @param array $parameters 0 表示匹配key， ... 之后的参数表示匹配value(可选)
     * @param \Illuminate\Validation\Validator $validator
     * @return bool
     */
    public function Only($attribute, $value, $parameters, $validator) {

        if (count($parameters) > 1) {
            $target = $parameters[0].'.'.$value;
            array_shift($parameters);

            $in_val = $parameters;
            if (in_array($value, $in_val)) {
                $flag = \Arr::get(self::$only, $target);

                if ($flag == null) {
                    \Arr::set(self::$only, $target, $value);
                    return true;
                }

                return false;
            }
        }
        if (count($parameters) == 1) {
            $target = $parameters[0].'.'.$value;
            $flag = \Arr::get(self::$only, $target);
            if ($flag == null) {
                \Arr::set(self::$only, $target, $value);
                return true;
            }
            return false;
        }


        return true;
    }

    /**
     * 带调节最小值限制
     * @param string $attribute
     * @param mixed $value
     * @param array $parameters 0 最小值， 1 匹配额外字段， 2 字段值
     * @param \Illuminate\Validation\Validator $validator
     * @return bool
     */
    public function minIf($attribute, $value, $parameters, $validator) {

        if (count($parameters) > 2) {
            $min = $parameters[0];

            $target = $parameters[1];
            $val = $parameters[2];

            $data = $validator->getData();
            $target_val = \Arr::get($data, $target);
            if ($target_val == $val) {
                return ($value >= $min);
            }
        }
        return true;
    }
    /**
     * 带调节最小值限制(3个参数值相加)
     * @param string $attribute
     * @param mixed $value
     * @param array $parameters 0 最小值， 1 匹配额外字段， 2 字段值
     * @param \Illuminate\Validation\Validator $validator
     * @return bool
     */
    public function minIfMore($attribute, $value, $parameters, $validator) {

        if (count($parameters) > 2) {
            $min = $parameters[0];

            $target = $parameters[1];
            $val = $parameters[2];

            $num = 0;
            $data = $validator->getData();
            for($i=3;$i<=5;$i++){
                $num += \Arr::get($data, $parameters[$i],0);
            }
            $target_val = \Arr::get($data, $target);
            if ($target_val == $val) {
               return ($num >= $min);
            }
        }
        return true;
    }
    /**
     * 带调节最小值限制(3个参数值相加)
     * @param string $attribute
     * @param mixed $value
     * @param array $parameters 0 最小值， 1 匹配额外字段， 2 字段值
     * @param \Illuminate\Validation\Validator $validator
     * @return bool
     */
    public function maxIfMore($attribute, $value, $parameters, $validator) {

        if (count($parameters) > 2) {
            $min = $parameters[0];

            $target = $parameters[1];
            $val = $parameters[2];

            $num = 0;
            $data = $validator->getData();
            for($i=3;$i<=5;$i++){
                $num += \Arr::get($data, $parameters[$i],0);
            }
            $target_val = \Arr::get($data, $target);
            if ($target_val == $val) {
                return ($num <= $min);
            }
        }
        return true;
    }
    /**
     * 日期在规定天数之内 包含边界值
     * @param string $attribute
     * @param mixed $value
     * @param array $parameters 0 规定天数， 1 开始计算日期字段key， 2 日期格式
     * @param \Illuminate\Validation\Validator $validator
     * @return bool
     */
    public function dayInDate($attribute, $value, $parameters, $validator) {
        if (count($parameters) > 2) {
            $day = $parameters[0];
            $target = $parameters[1];
            $format = $parameters[2];
            $data = $validator->getData();

            try {
                $target = Carbon::createFromFormat($format, \Arr::get($data, $target));
                $now = Carbon::createFromFormat($format, $value);
                $diff = $target->diffInDays($now) + 1;

                return $diff <= $day;
            } catch (\Exception $e) {
                return false;
            }
        }
        return false;
    }
    /**
     * 日期在规定天数之内 包含边界值(指定開始日期)
     * @param string $attribute
     * @param mixed $value
     * @param array $parameters 0 规定天数， 1 开始计算日期， 2 日期格式
     * @param \Illuminate\Validation\Validator $validator
     * @return bool
     */
    public function dayInSpecifiedDate($attribute, $value, $parameters, $validator) {
        if (count($parameters) > 2) {
            $day = $parameters[0];
            $date = $parameters[1];
            $format = $parameters[2];
            $data = $validator->getData();

            try {
                $target = Carbon::createFromFormat($format, $date);
                $now = Carbon::createFromFormat($format, $value);
                $diff = $target->diffInDays($now) + 1;

                return $diff <= $day;
            } catch (\Exception $e) {
                return false;
            }
        }
        return false;
    }
    /**
     * 日期在规定范围内 包含边界值
     * @param string $attribute
     * @param mixed $value
     * @param array $parameters 0 自然后几个月， 1 开始计算日期字段key， 2 日期格式
     * @param \Illuminate\Validation\Validator $validator
     * @return bool
     */
    public function rangeInDate($attribute, $value, $parameters, $validator){
        if (count($parameters) > 2) {
            $months = $parameters[0];
            $target = $parameters[1];
            $format = $parameters[2];
            $data = $validator->getData();

            try {
                $target = date('Y-m-1',strtotime(\Arr::get($data,$target)));
                $begin = Carbon::createFromFormat($format, $target);
                $begin = $begin->addMonths($months);
                $begin = $begin->subDays(1);//最大时间
                $end = Carbon::createFromFormat($format, $value);
                $min = $begin->min($end)->toDateString();//比较大小
                if($min==$value){//所填写的时间是否最小
                    return true;
                }
            } catch (\Exception $e) {
                return false;
            }
        }
        return false;
    }
}