<?php
	//tìm chuỗi ngày của lịch
	function findDate($date) {
		$pattern = "/\d{2}\/\d{2}\/\d{4}/";
		if (preg_match($pattern, $date, $matches)) 
		{
			$dateElements = explode('/', $matches[0]);
			// $day=$dateElements[0];
			// $month=$dateElements[1];
			// $year=$dateElements[2];
			return $dateElements;
		}
		return false;
	}
	function checkLineOK($line) {

		$condition_one = preg_match("/QC|QB|Trailer/", $line);
		$condition_two = preg_match("/CHIỀU\s*VÀ\s*TỐI/", $line);
		$condition_three = preg_match("/đến\s*đây\s*là\s*hết/", $line);
		$condition_four = preg_match("/Nhạc\s*đồ\s*biểu/", $line);

		if ($condition_one || $condition_two || $condition_three || $condition_four) {
			return false;
		}
		return true;
	}
?>


<html>
	<head>
    	<title>Kiểm tra</title>
        <link rel="icon" type="image/png" href="LOGO-THTPCT_NEW100x100.png" />
    </head>
    <body>
	<?php include "counter.php"; ?>
		<?php
			session_start();
			
			$source = $_POST["noidung"];

			//tìm ngày trong lịch
			$dates = findDate($source);
			
			if(isset($source) && $dates) 
			{
				//ngày - tháng - năm của lịch
				$day = $dates[0];
				$month = $dates[1];
				$year = $dates[2];

				$iTime = -1; // index mảng ghi thời gian
				$iContent = -1; // index mảng ghi nội dung tương ứng

				$arrTime = array();
				$arrContent = array();

				//tách nội dung thành từng dòng
				$rows = preg_split("/\r\n|\n|\r/", $source);
				// var_dump($array);
				
				//xử lý trên dòng
				foreach ($rows as $row) {

					// kiểm tra điều kiện trên dòng, nếu OK thì tìm thời gian và nội dung
					if (!checkLineOK($row)) {

						// tìm thời gian trên dòng, nếu có thì lưu vào $arrTime
						if (preg_match("/(\d{1,2}H\d{2})/", $row, $matches)) {
							$iTime++;
							$arrTime[$iTime] = $matches[0];
						}

						// tìm nội dung trên dòng
						// loại bỏ đoạn thời gian
						preg_replace("/(\d{1,2}H\d{2})/",'',$row);
						// loại bỏ dấu phẩy , gạch và khoảng trắng
						preg_replace("/-[[:blank:]]*’[[:blank:]]*/",'',$row);
						// lấy tất cả đoạn còn lại
						if (preg_match("/(\X+)/", $row, $matches)) {
							$iContent++;
							$arrContent[$iContent] = $matches[0];
						}
						

					} 

					// var_dump($row);
					
					echo "<br/>";			
				}
				var_dump($arrTime);
				echo "<br/>";
				var_dump($arrContent);
				echo "<br/>";
				
			}
			else {
				echo 'Không tìm thấy nội dung';
			}
		?>
	</body>
</html>