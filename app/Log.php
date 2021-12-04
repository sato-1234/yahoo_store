<?php
declare(strict_types=1);
namespace App;

use DateTime;
use DateTimeZone;

// --------- ログ出力(メモリ確認) ---------- //
class Log
{
	/**
	* @param string $logType
	*/
	public static function startLog(string $logType): void
	{
		// タイムゾーン設定
		$dtz = new DateTimeZone("Asia/Tokyo");
		$dt = new DateTime("now",$dtz);
		$log = $dt->format("Y年m月d日 H時i分s秒") . PHP_EOL;
		$log .= "取得開始！" . PHP_EOL;
		error_log($log, 3, "storage/log/" . $logType . "_log.txt");
	}

	/**
	* ログ出力関数
	* @param string $logType
	* @param float $memory
	* @param float $memoryMax
	* @param float $time
	*/
	public static function endLog(string $logType, float $memory, float $memoryMax, float $time): void
	{
		// タイムゾーン設定
		$dtz = new DateTimeZone("Asia/Tokyo");
		$dt = new DateTime("now",$dtz);
		$log = $dt->format("Y年m月d日 H時i分s秒") . PHP_EOL;
		$log .= "取得終了！" . PHP_EOL;
		$log .= "開始前メモリ:" . $memory . "MB" . PHP_EOL;
		$log .= "処理中の最大メモリ:" . $memoryMax ."MB" . PHP_EOL;
		$log .= "処理速度:" . $time . "秒" . PHP_EOL . PHP_EOL;
		error_log($log, 3, "storage/log/" . $logType . "_log.txt");
	}
}
