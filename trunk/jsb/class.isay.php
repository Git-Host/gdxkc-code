<?php
/**
 * @author gdxkc
 * @copyright 2012
 */
error_reporting(0);
class isay {
    public $server = 'localhost';
    public $user   = 'root';
    public $password = '';
    public $db = '';
    /**
     * mysql 数据库表结构
     * id|content|postime
     */ 
    private $table = 'isay';
     
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
    public function addItem($content) {
        $sql = "insert into ".$this->table." values (null,'".$content."',now())";
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
                                 'content'=>$row['content'],
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
    /**
     * 通过id获取内容
     */
    public function getItemById($id) {
        $sql = "select * from ".$this->table." where id = '".$id."'";
        if($result = mysql_query($sql)) {
            $row = mysql_fetch_array($result);
            return $row;                      
        }
    }
    /**
     * 修改内容
     */
    public function changeItem($id,$content) {
        $sql = "update ".$this->table." set content='".$content."' where id=".$id;
        if(mysql_query($sql)) {
            return true;
        }else {
            return false;
        }
    }
    /**
     * 多关键字搜索
     */
    public function search($array) {
        $sql = "select * from ".$this->table." where ";
        for($num = 0;$num<count($array);$num++) {
            if($num == count($array)-1){
                $str = $str."content like '%".$array[$num]."%'";
            }else {
                $str = $str."content like '%".$array[$num]."%' or ";
            }      
        }
        $sql = $sql.$str.' order by id desc';
        
        if($result = mysql_query($sql)) {
            while ($row = mysql_fetch_array($result)) {
                $search[] = array('id'=>$row['id'],
                                 'content'=>$row['content'],
                                 'postime'=>$row['postime']);
            }  
            return $search;          
        }
    }
}
?>