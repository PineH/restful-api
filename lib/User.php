<?php
/**
 * Created by PhpStorm.
 * User: H
 * Date: 2017/8/13
 * Time: 16:11
 */
require __DIR__.'/ErrorCode.php';

class User{
    /**
     * 数据库连接句柄
     * @var
     */
    private $_db;

    /**
     * 构造方法
     * User constructor.
     * @param PDO $_db PDO连接句柄
     */
    public function __construct($_db)
    {
        $this->_db=$_db;
    }

    /**
     * 用户登录
     * @param $username
     */
    public function login($username){
        if (empty($username)){
            throw new Exception('用户名不能为空',ErrorCode::username_cannot_empty);
        }
        $sql = 'select * from `user` where `name`=:username';

        $stmt = $this->_db->prepare($sql);

        $stmt ->bindParam(':username',$username);

        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (empty($user)){
            throw new Exception('用户名错误！',ErrorCode::uname_invalid);
        }
        return $user;
    }

    /**
     * 查询用户全部数据
     * @return array
     * @throws Exception
     */
    public function selectall(){
        $sql = 'select * from `user`';

        $stmt = $this->_db->prepare($sql);

        $stmt->execute();

        $user = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($user)){
            throw new Exception('查询数据失败！',ErrorCode::sele_user_all);
        }

        return $user;
    }

    /**
     * 根据用户id查询用户数据
     * @param $id
     * @return mixed
     * @throws Exception
     */
    public function selectgetid($id){
        $sql = "select * from `user` where `id`=:id";

        $stmt = $this->_db->prepare($sql);

        $stmt->bindParam(':id',$id);

        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (empty($user)){
            throw new Exception('根据用户id查询数据失败！',ErrorCode::sele_user_id);
        }

        return $user;
    }

    /**
     * 用户注册
     * @param $username 用户名
     * @param $uage 用户年龄
     * @param $sex 用户性别
     */

    public function register($username,$uage,$sex){
        if (empty($username)){
            throw new Exception('用户名不能为空',ErrorCode::username_cannot_empty);
        }

        if (empty($uage)){
            throw new Exception('年龄不能为空',ErrorCode::age_cannot_empty);
        }

        if (empty($sex)){
            throw new Exception('性别不能为空',ErrorCode::sex_cannot_empty);
        }

        if ($this->_isUsernameExists($username)){
            throw new Exception('用户已存在',ErrorCode::username_exists);
        }

        //写入数据库
        $sql = 'insert into `user`( `name`, `age`, `sex`) values (:name,:age,:sex)';

        $stmt = $this->_db->prepare($sql);

        $stmt ->bindParam(':name',$username);
        $stmt ->bindParam(':age',$uage);
        $stmt ->bindParam(':sex',$sex);

        if (!$stmt->execute()){
            Tool::json('注册失败！');
        }
        return [
            'userid' => $this->_db->lastInsertId(),
            'uname'  => $username,
            'uage'   => $uage,
            'usex'   => $sex
        ];

    }

    /**
     * 根据用户id删除用户
     * @param $id
     * @return mixed
     * @throws Exception
     */
    public function deleteid($id){
        $sql ="delete from `user` where `id`=".$id;

        $rest = $this->_db->exec($sql);

        if ($rest){
            Tool::json("根据用户id删除数据成功！","删除的用户id为".$id);
        }else{
            Tool::json("根据用户id删除数据失败 ");
        }
    }

    /**
     * 根据用户id修改用户信息
     * @param $id
     * @param $uname
     * @param $uage
     * @param $usex
     * @return array
     * @throws Exception
     */
    public function updatagetid($id,$uname,$uage,$usex){
        if (empty($uname)){
            throw new Exception('用户名不能为空',ErrorCode::username_cannot_empty);
        }

        if (empty($uage)){
            throw new Exception('年龄不能为空',ErrorCode::age_cannot_empty);
        }

        if (empty($usex)){
            throw new Exception('性别不能为空',ErrorCode::sex_cannot_empty);
        }

        $sql ="UPDATE user SET name='".$uname."',age='".$uage."',sex='".$usex."' WHERE id=".$id;

        $rest = $this->_db->exec($sql);

        if ($rest){
            $data   =   array(
                'uid'   =>  $id,
                'uname' =>  $uname,
                'uage'  =>  $uage,
                'usex'  =>  $usex
            );
            Tool::json('修改用户信息成功！' ,$data);
        }else{
            Tool::json('修改用户信息失败!');
        }
    }
    /**
     * MD5加密
     * @param $string
     * @param string $key
     */
    private function md5($string,$key = 'imooc'){
        return md5($string.$key);
    }

    /**
     * 检测用户名是否存在
     * @param $username
     * @return bool
     * sql中的=: :防止sql注入
     */
    private function _isUsernameExists($username){
        $sql = 'select * from `user` where `name`=:username';
        $stmt = $this->_db->prepare($sql);

        $stmt->bindParam(':username',$username);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return !empty($result);
    }
}