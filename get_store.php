<?php
declare(strict_types=1);
require_once __DIR__ . '/vendor/autoload.php';

use App\Dd;
use App\Log;
use App\StoreValidation;
use App\LoadCsv;
use App\GetStore;
use App\CreateCsv;

const TYPE = 'store';
const HEADER_LINE = ['会社概要URL','ストア名','会社名','電話番号','代表者','運営責任者'];
// 開始ログ出力
Log::startLog(TYPE);

// php.iniで最大メモリ確認と設定(最大メモリ測定より高く設定)
// 速度測定開始、開始前メモリ測定
$memory = memory_get_usage() / (1024 * 1024);
$timeStart = microtime(true);

// ----------- 引数バリデ CSV読み込み ----------- //
// エスケープ
$newArgv = array_map(function($n) {
	return htmlspecialchars($n, ENT_QUOTES, 'UTF-8');
},$argv);

// バリデ
$objValidation = new StoreValidation;
$newArgv = $objValidation->checkArgument($newArgv);


// ----------- メイン処理 -------------- //

// CSV読み込み
$objLoadCsv = new LoadCsv;
$urls = $objLoadCsv->loadCsv($newArgv[1],(int)$newArgv[2],(int)$newArgv[3]);

$objGetStore = new GetStore();
$stores = $objGetStore->getStore($urls);

// CSV作成
$objCreateCsv = new CreateCsv;
echo $objCreateCsv->createCsv($stores,TYPE,HEADER_LINE);

// ----------- メイン処理終了 ----------- //


// 速度測定終了、最大メモリ測定、終了ログ出力
$time = microtime(true) - $timeStart;
$memoryMax = memory_get_peak_usage() / (1024 * 1024);

// 終了ログ出力
Log::endLog(TYPE,$memory,$memoryMax,$time);
