<?php
declare(strict_types=1);
require_once __DIR__ . '/vendor/autoload.php';

use App\Log;
use App\GetUrl;
use App\CreateCsv;

const TYPE = 'url';
const HEADER_LINE = ['番号','ストアURL','ストア名','ストアID'];
// 開始ログ出力
Log::startLog(TYPE);

// php.iniで最大メモリ確認と設定(最大メモリ測定より高く設定)
// 速度測定開始、開始前メモリ測定
$memory = memory_get_usage() / (1024 * 1024);
$timeStart = microtime(true);


// ----------- メイン処理開始(処理完了後CSV作成) --------- //

$objGetUrl = new GetUrl;
$stores = $objGetUrl->getUrl();

$objCreateCsv = new CreateCsv;
echo $objCreateCsv->createCsv($stores,TYPE,HEADER_LINE);

// ----------- メイン処理終了 ----------- //


// 速度測定終了、最大メモリ測定、終了ログ出力
$time = microtime(true) - $timeStart;
$memoryMax = memory_get_peak_usage() / (1024 * 1024);

// 終了ログ出力
Log::endLog(TYPE,$memory,$memoryMax,$time);
