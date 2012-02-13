<?
//用来快速Mysql的大数据备份
//使用前请首先按照代码注释修改要导入的SQL文件名、数据库主机名、数据库用户名、密码、数据库名
//同时将数据库文件和本文本一起ftp导网站目录，然后以web方式访问此文件即可
//落伍(www.im286.com）负翁版权所有，可随意使用，但保留版权信息
//淡淡清香弥漫世界 修改

        $file_name="data.sql";  //要导入的SQL文件名
$dbhost=SAE_MYSQL_HOST_M.':'.SAE_MYSQL_PORT; //数据库主机名
        $dbuser=SAE_MYSQL_USER; //数据库用户名
        $dbpass=SAE_MYSQL_PASS;          //数据库密码
        $dbname=SAE_MYSQL_DB;      //数据库名
      
        @set_time_limit(0); //设置超时时间为0，表示一直执行。当php在safe mode模式下无效，此时可能会导致导入超时，此时需要分段导入
        $fp = @fopen($file_name, "r") or die("不能打开SQL文件 $file_name");//打开文件
        mysql_connect($dbhost, $dbuser, $dbpass) or die("不能连接数据库 $dbhost");//连接数据库
        mysql_select_db($dbname) or die ("不能打开数据库 $dbname");//打开数据库
  mysql_query('set names utf8');
echo "正在执行安装操作...<br />";
        while($SQL=GetNextSQL()){
                if (!mysql_query($SQL)){
                        echo "执行出错：".mysql_error()."
";
                        echo "SQL语句为：
".$SQL."
";
                };
        }
echo "安装完成,默认账户密码admin,如需请打开代码编辑工具修改config.php上的密码!<br />";
echo "你也可以删除本文件以防止恶意安装!";

        fclose($fp) or die("Can’t close file $file_name");//关闭文件
        mysql_close();

        //从文件中逐条取SQL
        function GetNextSQL() {
                global $fp;
                $sql="";
                while ($line = @fgets($fp, 40960)) {
                        $line = trim($line);
                        //以下三句在高版本php中不需要，在部分低版本中也许需要修改
                  //$line = str_replace("\\\\","\\",$line);
                  //    $line = str_replace("\’","’",$line);
                  //    $line = str_replace("\\r\\n",chr(13).chr(10),$line);
//                        $line = stripcslashes($line);
                        if (strlen($line)>1) {
                                if ($line[0]=="-" && $line[1]=="-") {
                                        continue;
                                }
                        }
                        $sql.=$line.chr(13).chr(10);
                        if (strlen($line)>0){
                                if ($line[strlen($line)-1]==";"){
                                        break;
                                }
                        }
                }
                return $sql;
        }
?> 