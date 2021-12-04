<?php
declare(strict_types=1);
namespace App;

//use App\Dd;
class LoadCsv
{
	/**
	* urlフォルダのCSVファイルを読み込み
	* @param string $name CSV名
	* @param int $start CSV読み込み開始番号(項目除く。1列目の番号)
	* @param int $last CSV読み込み終了番号(項目除く。1列目の番号)
	* @return array $urls
	*/
	public function loadCsv(string $name, int $start, int $last):array
	{
		// -- CSVファイル確認 -- //
		$filename = './storage/csv/url/' . $name;
		if ( file_exists($filename) === false ) {
			echo "\033[0;31m" . $name . "は指定フォルダに存在しません。" . "\033[0m"; exit;
		}

		// [引数開始番号]がCSVの最終番号以下か確認：項目を引くのでマイナス1
		$boolNum = (sizeof(file($filename)) - 1) < $start;
		if($boolNum){
			echo "\033[0;31m" . "第2引数は「1列目の末番号」以下で指定してください。" . "\033[0m"; exit;
		}

		// [引数終了番号]がCSVの最終番号以下か確認：項目を引くのでマイナス1
		$boolNum = (sizeof(file($filename)) - 1) < $last;
		if($boolNum){
			echo "\033[0;31m" . "第3引数は「1列目の末番号」以下で指定してください。" . "\033[0m"; exit;
		}

		$urls = [];
		$start = $start + 1;//実際は行基準なので、プラス1
		$last = $last + 1;//実際は行基準なので、プラス1
		$counter = 0;
		$fp = fopen($filename, 'r');
		while ( ($data = fgetcsv($fp)) !== false) {
			$counter++;//ループ回数をカウント
			//開始番号未満なら。また項目は含めない
			if($counter < $start){continue;}

			mb_convert_variables('UTF-8','SJIS-win', $data);
			$urls[] = [$data[1],$data[2]];

			if($counter === $last){break;}
		}
		fclose($fp);

		return $urls;
	}
}
