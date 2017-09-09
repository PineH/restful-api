<?php
/**
 * Created by PhpStorm.
 * User: H
 * Date: 2017/8/13
 * Time: 16:11
 *
 * 链接数据库并返回数据库连接句柄
 */
$pdo = new PDO('mysql:dbname=test;host=127.0.0.1','root','');
return $pdo;