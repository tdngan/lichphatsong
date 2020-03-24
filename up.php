<html>
	<head>
    	<title>Kiểm tra</title>
        <link rel="icon" type="image/png" href="LOGO-THTPCT_NEW100x100.png" />
    </head>
    <body>
<?php

	// kiểm tra thư mục trên FTP có tồn tại
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
	
	// lưu vào thư mục lps
	$filedir = 'lps';
	
	session_start();
	
	if (isset($_POST['Submit'])) 
	{
		// lấy nội dung trong textview
		$noidung=$_POST['noidungfile'];
		
		// $noidung=str_replace('\r\n', '', $noidung);
		
		//lấy tên file lịch đã upload
		$file=$_SESSION["filename"];
		
		// save nội dung mới vào file
		$f=fopen($filedir.'/'.$file,'w');
		file_put_contents($filedir.'/'.$file, $noidung);
		fclose($f);
		
		//lấy giá trị các session upload thư mục trên FTP
		$day = $_SESSION["lich_ngay"]; if (strlen($day)==1) $day='0'.$day;
		$month=$_SESSION["lich_thang"]; if (strlen($month)==1) $month='0'.$month;
		$year=$_SESSION["lich_nam"];
		
		// thư mục upload
		$dir='/LICHPHATSONG/NAM '.$year.'/TH/THANG '.$month.'/';
		
		if ($_POST["file_option"]=="file_ftp")
		{
			//kết nối FTP server
			$ftp_server='118.69.168.51';
			$ftp_user_name='bbt';
			$ftp_user_pass='mitsumi1qazXSW@';
			
			$ftp_conn=ftp_connect($ftp_server) or die("Could not connect to $ftp_server");
			$login=ftp_login($ftp_conn,$ftp_user_name,$ftp_user_pass);
			
			echo 'Kết nối server thành công <br/>';
			
			echo 'Upload tới thư mục '.$dir.'<br/>';
			
			// nếu thư mục ko có thì tạo mới
			if (!ftp_directory_exists($ftp_conn,$dir))
			{
				// tạo thư mục
				ftp_mkdir($ftp_conn,$dir);
				echo 'Thư mục hiện chưa có, đang tạo mới ...';
			}
	
			//đổi thư mục LICHPHATSONG
			ftp_chdir($ftp_conn,$dir);

			// kiểm tra file có tồn tại trên server? có thì upload lên ftp , ko thì báo lỗi
			if (file_exists($filedir.'/'.$file))
			{

				// to passive mode
				ftp_pasv($ftp_conn, true);
				
				//ftp_put(connection, remote, local, mode)
				if (ftp_put($ftp_conn,$file,$filedir.'/'.$file,FTP_ASCII))
				{
					echo "Upload lên FTP thành công file $file\n";
				} 
				else 
				{
					echo "Lỗi upload lên FTP file $file\n";
				}
				
				// đóng kết nối
				ftp_close($ftp_conn);
				
				echo '<p><a href="index.php">Bạn thực hiện lại từ đây</a></p>';
				
			}
			else
			{
				echo "File chưa có trên server";
			}
		}
		else // download file về máy
		{
            header('Content-type: text/txt');
			header("Content-disposition: attachment;filename=$filedir.'/'.$file");
			echo $noidung;
		}
	}
	else
	{
		echo '<a href="index.php">Bạn phải thực hiện từ đây</a>';
	}
?>
	</body>
</html>