<?php

require_once('../common/common.php');

try{

$request=sanitize($_REQUEST);
$rpc = $request['rpc'];
$tfa = $request['tfa'];

//入力パラメータチェック
if(empty($rpc)){
  throw new RuntimeException("比消費電力が未設定");
} elseif (!is_numeric($rpc)) {
	throw new RuntimeException("比消費電力が不正");
}
if(empty($tfa)){
	throw new RuntimeException("床面積の合計が未設定");
} elseif (!is_numeric($tfa)){
	throw new RuntimeException("床面積の合計が不正");
}

//テーブル参照設定
// $dsn='mysql:dbname=shop;host=localhost;charset=utf8';
// $user='root';
// $password='';
// $dbh=new PDO($dsn,$user,$password);
// $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

// $dsn='ExcelPHP';
// $user='';
// $password='';
// $dbh=new PDO($dsn,$user,$password);
// $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

// $file = new SplFileObject('table1.csv');
// $file->setFlags(SplFileObject::READ_CSV);
// foreach ($file as $line) {
// 	if($line[0]!=''){
//     $records[] = $line;
// 	}
// }

$file = file('table1.csv');
foreach($file as $line){
	$line = str_replace(PHP_EOL, '', $line);
	$records[] = explode(',',$line);
}

//仮想居住人数$S=IF(tfa<30,1,IF(tfa>=120,4,tfa/30))
if($tfa<30){
	$data4=1;
}else if($tfa>=120){
	$data4=4;
}else{
	$data4=$tfa/30;
}

for($i=1;$i<=36;$i++)
{
	$data1[$i]=$tfa*1.2*$rpc*10**-3;	//全般換気
	$data2[$i]=$records[$i][2]*10**-3;//局所換気（1人）
	$data3[$i]=$records[$i][3]*10**-3;//局所換気（4人）
	$data5[$i]=$data2[$i]*(4-$data4)/(4-1)+$data3[$i]*($data4-1)/(4-1);//局所換気
	$data[$i]=$data1[$i]+$data5[$i];//消費電力量
}

$json_array=$data;

if (empty($json_array)) {
	header("HTTP/1.1 404 Not Found");
	exit(0);
}

//JSONでレスポンスを返す
returnJson($json_array);

} catch (RuntimeException $e) {
  header("HTTP/1.1 400 Bad Request");
  exit(0);
} catch (Exception $e) {
  header("HTTP/1.1 500 Internal Server Error");
  exit(0);
}

function returnJson($resultArray){

if(array_key_exists('callback', $_GET)){
	$json = $_GET['callback'] . "(" . json_encode($resultArray) . ");";
}else{
	$json = json_encode($resultArray);
}
header('Content-Type: text/html; charset=utf-8');
echo  $json;
exit(0);

}

?>
