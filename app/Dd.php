<?php
declare(strict_types=1);
namespace App;

// ------------ dd用デバック用「緑色テキスト」 ---------- //
class Dd
{
	public static function dd($data, $t = null){
		if($t === null){
			echo "\033[0;32m<pre>" . PHP_EOL;
				var_dump($data);
			echo "</pre>\033[0m"; exit;
		} else {
			echo "\033[0;32m{$data}\033[0m"; exit;
		}
	}
}
