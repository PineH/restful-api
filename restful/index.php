<?php
/**
 * Created by PhpStorm.
 * User: H
 * Date: 2017/8/13
 * Time: 20:21
 */

require __DIR__.'/../lib/Tool.php';
require __DIR__.'/../lib/User.php';
$pdo = require __DIR__.'/../lib/db.php';
class Restful{
    /**
     * @var User
     */
    private $_user;

    /**
     * 请求方法
     * @var String
     */
    private $_requestMethod;

    /**
     * 请求资源名称
     * @var String
     */
    private $_resourceName;

    /**
     * 请求的资源id
     * @var String
     */
    private $_id;

    /**
     * 允许请求的资源列表
     * @var array
     */
    private $_allowResources = ['user'];

    /**
     * 允许请求的HTTP方法
     * @var array
     */
    private $_allowRequestMethods = ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'];

    /**
     * 常用状态码
     * @var array
     */
    private $_statusCodes = [
        200 => 'ok',
        204 => 'NO Content',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        500 => 'Server Internal Error',
    ];
    /**
     * Restful constructor.
     * @param User $_user
     */
    public function __construct(User $_user)
    {
        $this->_user=$_user;
    }

    public function run(){
        try{
            $this->_setupRequesMethod();
            $this->_setupResource();

            if ($this->_resourceName == 'user'){
                return $this->_json($this->_handleUser());
            }else{
                return $this->_getBodyParams();
            }
        }catch (Exception $e){
            $this->_json(['error' => $e->getMessage()],$e->getCode());
        }

    }

    /**
     * 初始化请求方法
     */
    private function _setupRequesMethod(){
        $this->_requestMethod = $_SERVER['REQUEST_METHOD'];
        if (!in_array($this->_requestMethod,$this->_allowRequestMethods)){
            throw new Exception('请求方法不被允许',405);
        }
    }

    /**
     * 请求资源
     */
    private function _setupResource(){
        $path = $_SERVER['PATH_INFO'];
        $params = explode('/',$path);
        $this->_resourceName = $params[1];

        if (!in_array($this->_resourceName, $this->_allowResources)){
            throw new Exception('请求资源不被允许！',400);
        }

        if (!empty($params[2])){
            $this->_id = $params[2];
        }
    }

    /**
     * 输出json
     * @param $array
     * @param $code
     */
    private function _json($array,$code = 0){
        if ($code > 0 && $code != 200 && $code !=204 ){
            header("HTTP/1.1 ".$code." ".$this->_statusCodes[$code]);
        }else{

        }
        header('Content-Type:application/json;charset=utf-8');
        echo json_encode($array,JSON_UNESCAPED_UNICODE);
        exit();
    }

    /**
     * 请求用户
     * @return array
     * @throws Exception
     */
    private function _handleUser(){
        /**
         * POST请求
         */
        if($this->_requestMethod == 'POST'){
            $body = $this->_getBodyParams();
            if(empty($body['name'])){
                throw new Exception('用户名不能为空！',400);
            }

            $data = $this->_user->register($body['name'],$body['age'],$body['sex']);
            Tool::json('添加用户成功！',$data);
            exit(0);
        }

        /**
         * GET请求
         */
        if ($this->_requestMethod == 'GET') {
            /**
             * 判断
             * 当get id为空时查询全部数据
             * 当get id有值时根据用户id查询数据
             */
            if(empty($this->_id)){
                $data = $this->_user->selectall();
                Tool::xmlEncode('获取用户数据成功！',$data);
                exit();
            }elseif ($this->_id<0){
                throw new Exception('用户id不能为负数！');
            }elseif (!is_numeric($this->_id)){
                throw new Exception('用户id只能为数字！');
            }else{
                $id = $this->_id;
                $data = $this->_user->selectgetid($id);
                Tool::xmlEncode('根据id获取用户数据成功！',$data);
                exit();
            }
        }

        /**
         * PUT请求
         */
        if ($this->_requestMethod == "PUT"){
            $body = $this->_getBodyParams();
            if (!isset($this->_id)){
                throw new Exception('用户id值不能为空！',400);
            }else if (!is_numeric($this->_id)){
                throw new Exception('用户id值只能为数字！',400);
            }else if($this->_id<0){
                throw new Exception('用户id不能为负数！');
            }else{
                $id = $this->_id;
                $data =$this->_user->updatagetid($id,$body['name'],$body['age'],$body['sex']);
                exit();
            }
        }

        /**
         * DELETE请求
         */
        if ($this->_requestMethod == "DELETE"){
            if (empty($this->_id)){
                throw new Exception('用户id不能为空！',400);
            }elseif (!is_numeric($this->_id)){
                throw new Exception("用户id值只能为数字！",400);
            }elseif ($this->_id<0){
                throw new Exception('用户id不能为负数！');
            }else{
                $id = $this->_id;
                $data = $this->_user->deleteid($id);
                exit();
            }
        }
    }

    /**
     * 请求文章资源
     */
    private function _handleArticle(){

    }

    /**
     * 获取请求参数
     * @return mixed
     * @throws Exception
     */
   private function _getBodyParams(){
       $raw = file_get_contents('php://input');
       if (empty($raw)){
            throw new Exception('请求参数错误！',400);
       }
       return json_decode($raw,true);
   }
}

$user = new User($pdo);

$restful = new Restful($user);

$restful->run();