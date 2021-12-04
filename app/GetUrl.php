<?php
declare(strict_types=1);
namespace App;

use App\Dd;
use App\Filter;
use DateTime;
use DateTimeZone;
use DOMWrap\Document;
use GuzzleHttp\Client;
// 並列処理
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Promise;

/* ---------------------------------------------
valuecommerce.ne.jpからYahoo StoreのURL一覧取得
 --------------------------------------------- */
//　ストアコード」取得サイト
// https://report.valuecommerce.ne.jp/rss/yshop/storelist.php?pg=1

class GetUrl
{
	/**
	* URL一覧を取得してCSV作成する
	* @return array $stores
	*/
	public function getUrl():array
	{
		// -- ページネーション数取得 -- //
		$context = stream_context_create([
			"http" => ["ignore_errors" => true]
		]);

		$valuecommerceHtml = file_get_contents(
			"https://report.valuecommerce.ne.jp/rss/yshop/storelist.php?pg=1",
			false,
			$context
		);

		if(strpos($http_response_header[0], '200') !== false){
			preg_match('/<a href\=\"storelist\.php\?pg\=10.*?<\/a>.*?<\/a>/s', $valuecommerceHtml, $pageLastNums);
			preg_match('/>[0-9]*?<\/a>\z/sx', $pageLastNums[0], $pageLastNum);
			$lastNum = str_replace([">","</a"], ' ', $pageLastNum[0]);
			$lastNum = Filter::filter($lastNum);
		} else {
			Dd::dd('URL対してステータスエラーがおきました。',1);
		}
		echo "\033[0;32m" . $lastNum . 'ページネーションがあります' . "\033[0m". PHP_EOL;

		// ページネーション数URL格納
		$urls = [];
		for ($i = 1; $i <= $lastNum; $i++) {
			$url = 'https://report.valuecommerce.ne.jp/rss/yshop/storelist.php?pg='. $i;
			//連想配列で渡して、$index設定（返り値をわかりやすく）
			$urls["ID_" . $i] = new Request('GET', $url);
		}

		$stores = [];// $urls8万件以上格納
		$client = new Client();
		// -------- 並列処理で繰り返し ---------- //
		$pool = new Pool($client, $urls, [
			'concurrency' => 20,//並列20ずつ

			// -- 正常なURLの場合の処理 -- //
			'fulfilled' => function (Response $response, $index) use (&$stores){

				$html = (string) $response->getBody();
				$doc = new Document;
				$node = $doc->html($html);

				// ストアtabel取得
				$tds = $node->find('body > div > table > tr > td');
				$trs = $tds[4]->find('tr');

				if( count($trs) >= 1 ){

					foreach($trs as $tr){

						// 書き込み用変数
						$storeNum = '';
						$storeUrl = '';
						$storeName = '';
						$storeCode = '';

						$tdList = $tr->find('td.list');
						if( count($tdList) === 3 ){
							$storeNum = $tdList[0]->text();
							$storeUrl = $tdList[1]->find('a')->attr('href');
							$storeName = $tdList[1]->find('a')->text();
							$storeCode = str_replace("http://store.shopping.yahoo.co.jp/", '', $storeUrl);
							$storeCode = str_replace("/", '', $storeCode);

							array_push($stores, [
								$storeNum,
								$storeUrl,
								$storeName,
								$storeCode
							]);

						} else { continue; }

					}
				} else { Dd::dd('エラーです。' . $index,1); }
			},

			// -- URLエラーの場合の処理(再404の場合) -- //
			'rejected' => function ($reason,$index) use(&$stores){
				array_push($stores, [
					'未取得',
					'未取得',
					'未取得',
					$index
				]);
			}
		]);
		$promise = $pool->promise();
		$promise->wait();

		// 多次元重複削除すべての値が一緒の場合(型が異なる場合できない)
		$stores = array_unique($stores, SORT_REGULAR);

		// -- ソート作業(番号順) -- //
		usort($stores,function ($a, $b) {
			if( (int) $a[0] === (int) $b[0] ){
				return 0;//同じなら順番同じに
			}
			return (int) $a[0] > (int) $b[0] ? 1 : -1; //小さい順に表示できる↓
		});

		//Dd::dd($stores);//確認
		return $stores;
	}// ----------- getCode()終了 ---------- //

}
