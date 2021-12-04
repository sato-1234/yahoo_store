<?php
declare(strict_types=1);
namespace App;

// ------ 引数バリデーション ------ //
class StoreValidation
{
	/**
	* get_store.phpの引数バリデーション
	* @param array $newArgv
	* @return array $newArgv
	*/
	public function checkArgument(array $newArgv): array
	{
		// 引数設定（引数0~1個のとき）
		if( count($newArgv) !== 4 ){
			echo "\033[0;31m" . "3つの引数を指定してください" . "\033[0m"; exit;
		}

		// 有効な第1引数の判断:CSV名
		if( preg_match('/\A[a-zA-Z0-9\_\-]{1,}\.csv\z/u', $newArgv[1]) !== 1 ){
			echo "\033[0;31m" . "urlフォルダのCSVファイル名（半角英数字と一部の記号のみ使用可能）を指定してください" . "\033[0m"; exit;
		}

		// 有効な第2引数の判断:開始番号(1列目)
		if( preg_match('/\A[1-9]{1}[0-9]*\z/u', $newArgv[2]) !== 1 ){
			echo "\033[0;31m" . "第2引数は1以上の整数で指定してください" . "\033[0m"; exit;
		}
		$newArgv[2] = (int) $newArgv[2];

		// 有効な第3引数の判断:終了番号(1列目)
		if( preg_match('/\A[1-9]{1}[0-9]*\z/u', $newArgv[3]) !== 1 ){
			echo "\033[0;31m" . "第3引数は1以上の整数で指定してください" . "\033[0m"; exit;
		}
		$newArgv[3] = (int) $newArgv[3];

		if( $newArgv[3] < $newArgv[2] ){
			echo "\033[0;31m" . "第3引数は第引数2の整数以上で指定してください" . "\033[0m"; exit;
		}

		return $newArgv;
	}

}
