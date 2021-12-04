<?php
declare(strict_types=1);
namespace App;

// ------------ 共通フィルター 共通空白削除など ---------- //
class Filter
{
	public static function filter($row)
	{
		// 改行置き換える(改行なくす)
		$row = str_replace(["\r\n", "\r", "\n"], '', $row);
		// "\t"と全角は半角スペース
		$row = str_replace(["\t","　"], ' ', $row);

		// 連続スペースを1個の半角に
		while( preg_match('/[\s]{2,}/', $row) ){
			$row = preg_replace('/[\s]{2,}/', ' ', $row);
		}

		//先頭と末が「半角全角スペースの場合」先頭と末スペース削除(また変な空白も)
		$row = trim($row);
		while( preg_match('/\A\s/', $row) ){
			$row = mb_substr($row, 1);
		}
		while( preg_match('/\s\z/', $row) ){
			$row = mb_substr($row, 0,-1);
		}

		return $row;
	}
}
