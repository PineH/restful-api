<?php
/**
 * Created by PhpStorm.
 * User: H
 * Date: 2017/9/4
 * Time: 15:29
 */
class Tool{
    /**
     * json 方式封装通信接口
     * @param $message  提示信息
     * @param $data 数据
     */
    public static function json($message='',$data=''){

        $result = array(
            'message' => $message,
            'data' => $data
        );

        echo json_encode($result,JSON_UNESCAPED_UNICODE);
        exit();
    }

    /**
     * xml 方式封装通信接口
     * @param $message  信息提示
     * @param array $data   数据
     */
    public static function xmlEncode( $message, $data = array()) {
        $result = array(
            'message' => $message,
            'data' => $data,
        );

        header("Content-Type:text/xml");
        $xml = "<?xml version='1.0' encoding='UTF-8'?>\n";
        $xml .= "<root>\n";

        $xml .= self::xmlToEncode($result);

        $xml .= "</root>";
        echo $xml;
    }

    public static function xmlToEncode($data) {

        $xml = $attr = "";
        foreach($data as $key => $value) {
            if(is_numeric($key)) {
                $attr = " id='{$key}'";
                $key = "item";
            }
            $xml .= "<{$key}{$attr}>";
            $xml .= is_array($value) ? self::xmlToEncode($value) : $value;
            $xml .= "</{$key}>\n";
        }
        return $xml;
    }
}