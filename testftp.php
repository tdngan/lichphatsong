<?php

function ftp_directory_exists($ftp, $dir) 
{ 
    // Get the current working directory 
    $origin = ftp_pwd($ftp); 

    // Attempt to change directory, suppress errors 
    if (@ftp_chdir($ftp, $dir)) 
    { 
        // If the directory exists, set back to origin 
        ftp_chdir($ftp, $origin);    
        return true; 
    } 

    // Directory does not exist 
    return false; 
} 
//kết nối FTP server
			$ftp_server='';
			$ftp_user_name='';
			$ftp_user_pass='';
			
			$ftp_conn=ftp_connect($ftp_server) or die("Could not connect to $ftp_server");
			$login=ftp_login($ftp_conn,$ftp_user_name,$ftp_user_pass);
			
			echo 'Kết nối server thành công <br/>';
			
			$dir='/LICHPHATSONG/NAM 2018/THANG 08/';
			
			/*if (!ftp_directory_exists($ftp_conn,$dir))
			{
				// tạo thư mục
				ftp_mkdir($ftp_conn,$dir);
				echo 'Thư mục hiện chưa có, đang tạo mới ...';
			}*/
			if (ftp_directory_exists($ftp_conn,$dir))
			{
				echo 'có thư mục này rồi';
			}
			else
			{
				echo 'chưa có, đg tạo';
				ftp_mkdir($ftp_conn,$dir);
				
			}
			echo 'up thử';
				$file = 'THCT-2018-08-17.txt';
				//ini_set('max_execution_time', 300);
				ftp_pasv($ftp_conn, true);
				if (ftp_put($ftp_conn,$file,$file,FTP_ASCII))
				{
					echo 'up ok';
				}
				else 
				{
					echo 'up lỗi';
				}
			ftp_close($ftp_conn);
?>