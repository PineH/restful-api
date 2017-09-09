<?php

/**
 * Created by PhpStorm.
 * User: H
 * Date: 2017/8/13
 * Time: 17:22
 */
class ErrorCode
{
    const username_exists = 1;          //用户名已存在
    const age_cannot_empty = 2;         //用户年龄不能为空
    const username_cannot_empty = 3;    //用户名不能为空
    const sex_cannot_empty = 4;         //用户性别不能为空
    const register_fail = 5;            //注册失败
    const uname_invalid = 6;            //用户名错误
    const sele_user_all = 7;            //查询全部数据错误
    const sele_user_id  = 8;            //根据用户id查询用户失败
    const delete_user_id = 9;           //根据用户id删除用户失败
    const uptate_fail =10;              //修改失败
}