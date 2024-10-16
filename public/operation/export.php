<?php
date_default_timezone_set('Asia/Dhaka');

$servername     = "192.168.20.14:3306";
$username       = "root";
$password       = "351f0*57034e1a025#";
$dbname         = "gp_global";

$conn = mysqli_connect($servername, $username, $password, $dbname); 


$sql = "SELECT t.click_id,b.subs_date,b.unsubs_date FROM `bdg_analysis` b left join traffic_analysis t on b.msisdn=t.msisdn WHERE b.`renew_count` > 10 and t.callback_sent_status=1";
/*$sql = "SELECT t.click_id,b.subs_date,b.unsubs_date from bdg_analysis b
left join traffic_analysis t on b.msisdn=t.msisdn
where b.status = 0 and date_format(subs_date,'%Y-%m-%d')=date_format(unsubs_date,'%Y-%m-%d') and t.callback_sent_status='1'";
*/
$data = mysqli_query($conn,$sql);

// File path where CSV will be saved
$csvFilePath = '10_plus_renewed.csv';
 
// Open file pointer to write to CSV file
$file = fopen($csvFilePath, 'w');
$row[0] = 'Click Id';
$row[1] = 'Subs Date';
$row[2] = 'Unsubs Date';
fputcsv($file, $row);

// Loop through each row of the data array and write to CSV file
while ($row_data = mysqli_fetch_array($data)) {
	$row[0] = $row_data['click_id'];
	$row[1] = $row_data['subs_date'];
	$row[2] = $row_data['unsubs_date'];
    fputcsv($file, $row);
    echo "Row added to CSV file: " . implode(',', $row) . "\n";
}

// Close file pointer
fclose($file);

echo "CSV file has been successfully generated!";