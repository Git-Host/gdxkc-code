<?
//��������Mysql�Ĵ����ݱ���
//ʹ��ǰ�����Ȱ��մ���ע���޸�Ҫ�����SQL�ļ��������ݿ������������ݿ��û��������롢���ݿ���
//ͬʱ�����ݿ��ļ��ͱ��ı�һ��ftp����վĿ¼��Ȼ����web��ʽ���ʴ��ļ�����
//����(www.im286.com�����̰�Ȩ���У�������ʹ�ã���������Ȩ��Ϣ
//���������������� �޸�

        $file_name="data.sql";  //Ҫ�����SQL�ļ���
$dbhost=SAE_MYSQL_HOST_M.':'.SAE_MYSQL_PORT; //���ݿ�������
        $dbuser=SAE_MYSQL_USER; //���ݿ��û���
        $dbpass=SAE_MYSQL_PASS;          //���ݿ�����
        $dbname=SAE_MYSQL_DB;      //���ݿ���
      
        @set_time_limit(0); //���ó�ʱʱ��Ϊ0����ʾһֱִ�С���php��safe modeģʽ����Ч����ʱ���ܻᵼ�µ��볬ʱ����ʱ��Ҫ�ֶε���
        $fp = @fopen($file_name, "r") or die("���ܴ�SQL�ļ� $file_name");//���ļ�
        mysql_connect($dbhost, $dbuser, $dbpass) or die("�����������ݿ� $dbhost");//�������ݿ�
        mysql_select_db($dbname) or die ("���ܴ����ݿ� $dbname");//�����ݿ�
  mysql_query('set names utf8');
echo "����ִ�а�װ����...<br />";
        while($SQL=GetNextSQL()){
                if (!mysql_query($SQL)){
                        echo "ִ�г���".mysql_error()."
";
                        echo "SQL���Ϊ��
".$SQL."
";
                };
        }
echo "��װ���,Ĭ���˻�����admin,������򿪴���༭�����޸�config.php�ϵ�����!<br />";
echo "��Ҳ����ɾ�����ļ��Է�ֹ���ⰲװ!";

        fclose($fp) or die("Can��t close file $file_name");//�ر��ļ�
        mysql_close();

        //���ļ�������ȡSQL
        function GetNextSQL() {
                global $fp;
                $sql="";
                while ($line = @fgets($fp, 40960)) {
                        $line = trim($line);
                        //���������ڸ߰汾php�в���Ҫ���ڲ��ֵͰ汾��Ҳ����Ҫ�޸�
                  //$line = str_replace("\\\\","\\",$line);
                  //    $line = str_replace("\��","��",$line);
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