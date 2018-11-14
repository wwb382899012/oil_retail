<?php

class MathUtility{

    /**
     * 2个任意精度数字的加法计算
     * @param string $leftOperand
     * @param string $rightOperand
     * @param int $scale 结果中小数点后的小数位数
     * @return string
     */
    public static function add(string $leftOperand , string $rightOperand,int $scale = 4):string{
        return bcadd($leftOperand,$rightOperand,$scale);
    }

    /**
     * 2个任意精度数字的减法
     * @param string $leftOperand
     * @param string $rightOperand
     * @param int $scale 结果中小数点后的小数位数
     * @return string
     */
    public static function sub(string $leftOperand , string $rightOperand,int $scale = 4):string{
        return bcsub($leftOperand,$rightOperand,$scale);
    }

    /**
     * 2个任意精度数字乘法计算
     * @param string $leftOperand
     * @param string $rightOperand
     * @param int $scale 结果中小数点后的小数位数
     * @return string
     */
    public static function mul(string $leftOperand , string $rightOperand,int $scale = 4):string{
        return bcmul($leftOperand,$rightOperand,$scale);
    }

    /***
     * 2个任意精度的数字除法计算
     * @param string $leftOperand
     * @param string $rightOperand
     * @param int $scale 结果中小数点后的小数位数
     * @return string
     */
    public static function div(string $leftOperand , string $rightOperand,int $scale = 4):string{
        if(self::equal($leftOperand,0) || self::equal($rightOperand,0)){
            return 0;
        }

        return bcdiv($leftOperand,$rightOperand,$scale);
    }

    /**
     * 比较两个任意精度的数字，左边的数left_operand比较右边的数right_operand大返回true
     * @param string $leftOperand
     * @param string $rightOperand
     * @param int $scale 比较的小数位数
     * @return bool
     */
    public static function greater(string $leftOperand , string $rightOperand,int $scale = 4):bool{
        return (1 === bccomp($leftOperand,$rightOperand,$scale));
    }

    /**
     * 比较两个任意精度的数字，如果两个数相等返回true
     * @param string $leftOperand
     * @param string $rightOperand
     * @param int $scale 比较的小数位数
     * @return bool
     */
    public static function equal(string $leftOperand , string $rightOperand,int $scale = 4):bool{
        return (0 === bccomp($leftOperand,$rightOperand,$scale));
    }

    /**
     * 比较两个任意精度的数字，左边的数left_operand比较右边的数right_operand小返回true
     * @param string $leftOperand
     * @param string $rightOperand
     * @param int $scale 比较的小数位数
     * @return bool
     */
    public static function less(string $leftOperand , string $rightOperand,int $scale = 4):bool{
        return (-1 === bccomp($leftOperand,$rightOperand,$scale));
    }

    /**
     * 获取精度
     * @param string $num
     * @return int
     */
    public static function getScale(string $num):int {
        $count = 0;
        $temp = explode( '.', $num);

        if (count($temp) > 1) {
            $decimal = end( $temp);
            $count = strlen($decimal);
        }

        return $count;
    }
}