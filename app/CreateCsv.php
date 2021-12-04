<?php
declare(strict_types=1);
namespace App;

use DateTime;
use DateTimeZone;

class CreateCsv
{
	/**
	* CSV作成用関数
	*
	* @param array $stores CSV化データ
	* @param string $pathType フォルダ指定
	* @param array $headerLine CSVのheader項目
	* @return string CSV作成完了
	*/
	public function createCsv(array $stores, string $pathType, array $headerLine): string
	{
		// タイムゾーンからファイルname設定
		$dtz = new DateTimeZone("Asia/Tokyo");
		$dt = new DateTime("now",$dtz);
		$path = './storage/csv/' . $pathType . '/';
		$path .= $pathType . '_list_' . $dt->format("Y-m-d_His") . ".csv";

		$f = fopen($path, 'w');
		mb_convert_variables('SJIS-win', 'UTF-8', $headerLine);
		fputcsv($f, $headerLine);//1行目の項目記述

		//2行目以降の$store情報を追記
		foreach ($stores as $line) {
			mb_convert_variables('SJIS-win', 'UTF-8', $line);
			fputcsv($f, $line);
		}
		//rewind($f);// ポインタを戻す
		fclose($f);

		return "\033[0;32m" . 'CSV作成完了しました。' . "\033[0m";
	}
}
