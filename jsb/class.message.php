<?php
/**
 * @author gdxkc
 * @copyright 2012
 */

class message {
    public $server = 'localhost';
    public $user   = 'root';
    public $password = '';
    public $db = '';
    /**
     * mysql 数据库表结构
     * id|guest|message|postime
     */ 
    private $table = 'message';
     
    /**
     * 配置mysql连接信息
     */  
    public function config($server,$user,$password,$db) {
        $this->server = $server;
        $this->user = $user;
        $this->password = $password;
        $this->db = $db;
    }
    /**
     * 连接mysql,选择数据库
     */ 
    public function connect() {
        if(mysql_connect($this->server,$this->user,$this->password)) {
            if(mysql_select_db($this->db)) {
                mysql_query("set names utf8;");
                return true;
            }else {
                return false;
            }
        }else {
            return false;
        }
    }
    /**
     * 插入新记录
     */
    public function addItem($guest,$message) {
        $sql = "insert into ".$this->table." values (null,'".$guest."','".$message."',now())";
        if(mysql_query($sql)) {
            return true;
        }else {
            return false;
        }
    }
    /**
     * 获取所有内容
     */ 
    public function getAll() {
        $sql = "select * from ".$this->table." order by id desc";
        if($result = mysql_query($sql)) {
            while ($row = mysql_fetch_array($result)) {
                $array[] = array('id'=>$row['id'],
                                 'guest'=>$row['guest'],
								 'message'=>$row['message'],
                                 'postime'=>$row['postime']);
            }  
            return $array;          
        }
    }
    /**
     * 删除内容
     */
    public function deleteItem($id) {
        $sql = "delete from ".$this->table." where id=".$id;
        if(mysql_query($sql)) {
            return true;
        }else {
            return false;
        }
    }
}
?>