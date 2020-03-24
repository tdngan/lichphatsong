<html>
	<head>
    	<title>Kiểm tra</title>
        <link rel="icon" type="image/png" href="LOGO-THTPCT_NEW100x100.png" />
    </head>
    <body>
	<?php include "counter.php"; ?>
		<?php
			session_start();
            
            if(isset($_POST["noidung"])) 
			{ 
				$user_number = $_POST["noidung"];
				
				// echo $user_number;
				
				// loại bỏ chữ quảng cáo, quảng bá
				$user_number=preg_replace('/(Quảng\s{1,}cáo)|(quảng\s{1,}bá)/i','qc',$user_number);
                
                //lấy ngày trong lịch
                $pattern = "/\d{2}\/\d{2}\/\d{4}/";
                $day="";
                $month="";
                $year="";
                
				// tìm kiếm chuỗi ngày tháng trong nội dung; tìm $pattern trong $user_number, trả về mảng $matches
                if (preg_match($pattern, $user_number, $matches)) 
                {
                    $dateElements = explode('/', $matches[0]);
                    $day=$dateElements[0];
                    $month=$dateElements[1];
                    $year=$dateElements[2];
                    
                    //tạo session để chuyển qua tạo thư mục trên FTP
                    $_SESSION["lich_ngay"] = $day;
                    $_SESSION["lich_thang"] = $month;
                    $_SESSION["lich_nam"] = $year;
                    			
					//đẩy nội dung vào $out
					$out="";
					
					//(\d{1,2}H\d{2})([^\n]*)([^-]*)
					//gồm 3 thành phần
					//1: giờ
					//2: cụm nội dung
					//3: cụm tên của phim tài liệu khi có xuống hàng
					
					//chuỗi này hợp lý hơn
					
					//

					// nếu dòng bắt đầu bằng thời gian phát sóng thì lấy
					//if (preg_match_all("/-(\s*)(\d{1,2}[hH]\d{2})(.*)/i", $user_number, $matches))
					
					//loại bỏ tất cả dấu ngoặc đơn và nội dung trong dấu ngoặc đơn
					//$user_number = preg_replace("/\([^)]+\)/","",$user_number);
					
					// $user_number = preg_replace("/PT-TH/","PT&TH",$user_number);

					$user_number = str_replace('  ', ' ', $user_number);
					$user_number = str_replace('\t\t', '\t', $user_number);
					
					$matchall = preg_match_all("/(\d{1,2}H\d{2})([^\n]*)([^-]*)/i", $user_number, $matches);
					
					// var_dump($matches);
					var_dump($matches[0]);
							echo "<br/><br/>";

					// echo $matchall;
					// echo "<br/><br/>";

					if ($matchall)
					{
						$i=0;
						
						while ($i<$matchall)
						{

							// var_dump($matches[$i]);
							// echo "<br/><br/>";


							//nếu có chữ QC hay QB thì không lấy
							$check1 = preg_match("/QC|QB|Trailer/i",$matches[0][$i]);
							
							//nếu có chữ CHIỀU VÀ TỐI ở đoạn giữa LPS
							$check2 = preg_match("/CHIỀU\s*VÀ\s*TỐI/i",$matches[0][$i]);
							
							//nếu có chữ "đến đây là hết ở đoạn cuối LPS
							$check3 = preg_match("/đến\s*đây\s*là\s*hết/i",$matches[0][$i]);

							// loại bỏ chữ Nhạc đồ biểu
							$check4 = preg_match("/Nhạc\s*đồ\s*biểu/i",$matches[0][$i]);

							// echo $check1;
							
							//nếu 1 trong 3 điều kiện xảy ra thì bỏ qua cụm nội dung 3
							if ($check1 || $check2 || $check3 || $check4) 
							{
								//xóa nội dung cụm 3
								$matches[0][$i] = "";
							}
							
							//dòng nào không có QC, QB thì lấy
							if (!preg_match("/QC|QB|Trailer/i",substr($matches[2][$i],3)))
							{
								// thay thế chữ H,h trong giờ phát thành dấu :
								$gio=preg_replace('/[hH]/',':',$matches[1][$i])." ";
								// echo $gio;
								$out.=$gio;
								
								//loại bỏ tất cả dấu ngoặc đơn và nội dung trong dấu ngoặc đơn (ct phát lại)
								$matches[2][$i] = preg_replace("/\([^)]+\)/","",$matches[2][$i]);
								
								$out .= substr($matches[2][$i],3);
								
								//loại bỏ tất cả dấu ngoặc đơn và nội dung trong dấu ngoặc đơn (ct phát lại)
								$matches[3][$i] = preg_replace("/\([^)]+\)/","",$matches[3][$i]);
								
								//có 2 phần, 1- khoảng trắng; 2- nội dung
								preg_match_all("/^(\s*)(.*)$/",$matches[3][$i],$result);
								//lấy 2- nội dung gắn vào
								
								var_dump($result);
								echo "<br/>";

								$out .= $result[2][0];
								$out .= PHP_EOL;
							}
							// xử lý dòng "Tin nóng 24h" (do có chữ QC, QB nên phải tách riêng)
							else if (preg_match("/tin[[:blank:]]+(nóng)[[:blank:]]+24h/i",$matches[2][$i]))
							{
							  
								// thay thế chữ H,h trong giờ phát thành dấu :
								$gio=preg_replace('/[hH]/',':',$matches[1][$i])." ";
								// echo $gio;
								$out.=$gio;
							  
								//loại bỏ tất cả dấu ngoặc đơn và nội dung trong dấu ngoặc đơn
								$matches[2][$i] = preg_replace("/\([^)]+\)/","",$matches[2][$i]);
								
								$out .= substr($matches[2][$i],3);
								$out .= PHP_EOL;
							}
							$i++;
						}	
					}
			
					//xóa các ký tự TAB, SPACE (không xóa NEWLINE) ra khỏi $out
					$out = preg_replace('/[[:blank:]]+/',' ',$out);
										
					//đặt tên file
					$downloadfile='THCT-'.$year.'-'.$month.'-'.$day.'.txt';
					
					$_SESSION["filename"]=$downloadfile;
					
					//save file
					$file=fopen($downloadfile,'w');
					file_put_contents($downloadfile, $out);
					fclose($file);
					
					
					
					?>
                    <div style="width:100%;height:100%;position:absolute;vertical-align:middle;text-align:center;">
                        <form action="up.php" method="post">
                          <p>Chỉnh sửa nội dung:</p>
                          <p><b><?php echo $downloadfile;?></b></p>
                            <p><textarea rows="40" cols="100" name="noidungfile"><?php echo preg_replace( "/\r/", "", file_get_contents($downloadfile)); ?></textarea></p>
                            <p>
                                <input type="radio" name="file_option" value="file_save">Lưu file
                                <input type="radio" name="file_option" checked="checked"  value="file_ftp">Lưu FTP
                            </p>
                                <input type="submit" value="NEXT" name="Submit" style="height:50px; width:150px;" />
                        </form>
        			</div>
            <?php
				}
				else echo 'Không tìm thấy ngày phát sóng';
			}
			else echo '<a href="index.php">Bạn thực hiện lại từ đây</a>';        
			?>
	</body>
</html>