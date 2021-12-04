<?php
declare(strict_types=1);
namespace App;

//use App\Dd;
use App\Filter;
use DOMWrap\Document;
use GuzzleHttp\Client;

/* ---------------------------------------------
YahooサイトからHTMLを取得するURL
 --------------------------------------------- */

/* -- 「ストアコード」と「ストア会社概要」の場合 -- */
//$url = 'https://store.shopping.yahoo.co.jp/ストアコード/;
//$url = 'https://store.shopping.yahoo.co.jp/ストアコード/info.html;

class GetStore
{
	/**
	* @param array $urls
	* @return array $stores ストア情報 直列処理
	*/
	public function getStore(array $urls):array
	{
		//ストア情報用
		$stores = [];
		foreach ($urls as $line) {

			$url = $line[0] . 'info.html';

			// ステータスコード用
			$client = new Client();
			$res = $client->request( 'GET', $url,[
				"delay" => 300.0,// <= ディレイ0.3秒(拒否対策)
				"http_errors" => false
			]);


			if( $res->getStatusCode() === 404 ){
				echo "\033[0;31m" . '404のため失敗：' . $line[1] . '：' . $url . "\033[0m" . PHP_EOL;
				array_push($stores, [
					$url,
					$line[1],//$tenpo,
					'404',//$companyName,
					'404',//$tel,
					'404',//$userName,
					'404'//$staffName
				]);
				continue;
			}// -- 404対応終了 -- //

			// -------- 取得 ---------- //
			$html = (string) $res->getBody();
			$doc = new Document;
			$node = $doc->html($html);

			//会社情報用変数
			$companyName = '';
			$tel = '';
			$userName = '';
			$staffName = '';

			// -- paypayモールリダイレクト用 -- //
			if( strpos($node->find('html > head > title')->text(),'- 通販 - PayPayモール') !== false ){

				$storeInfos = $node->find('body ul.StoreInfo');
				$storeInfoOne = $storeInfos[0]->find('li > div');
				$storeInfoTwo = $storeInfos[1]->find('li > div');

				foreach($storeInfoOne as $storeIn){

					// -- 会社名 -- //
					if(	strpos($storeIn->find('p.TableRow_head')->text(),'会社名（商号）') !== false ){
						$companyName = $storeIn->find('div.TableRow_body > p')->text();
						$companyName = Filter::filter($companyName);
					}

					// -- 代表者 -- //
					elseif(	strpos($storeIn->find('p.TableRow_head')->text(),'代表者') !== false ){
						$userName = $storeIn->find('div.TableRow_body > p')->text();
						$userName = Filter::filter($userName);
					}
				}

				foreach($storeInfoTwo as $storeIn){
					// -- TEL -- //
					if(	strpos($storeIn->find('p.TableRow_head')->text(),'お問い合わせ') !== false ){
						$tel = $storeIn->find('div.TableRow_body > p')->html();
						$tel = preg_replace('/<br>.*/s','',$tel);
						$tel = Filter::filter($tel);
					}

					// -- 運営責任者 -- //
					elseif(	strpos($storeIn->find('p.TableRow_head')->text(),'運営責任者') !== false ){
						$staffName = $storeIn->find('div.TableRow_body > p')->text();
						$staffName = Filter::filter($staffName);
					}
				}
			}

			// -- ストア通常ページ用 li情報取得 -- //
			elseif( strpos($node->find('html > head > title')->text(),'Yahoo!ショッピング') !== false ){

				$ul = $node->find('#shpBody .mdInformationTable .elSection > .elSectionContent > .elRows > .elRow');
				foreach ($ul as $li) {

					// -- 会社名 -- //
					if(	strpos($li->find('.elRowHeading > .elRowHeadingText')->text(),'会社名（商号）') !== false ){
						$companyName = $li->find('.elRowContent > .elRowContentText')->text();
						$companyName = Filter::filter($companyName);
					}

					// -- TEL -- //
					elseif(	strpos($li->find('.elRowHeading > .elRowHeadingText')->text(),'お問い合わせ電話番号') !== false ){
						$tel = $li->find('.elRowContent > .elRowContentText')->text();
						$tel = Filter::filter($tel);
					}

					// -- 代表 -- //
					elseif(	strpos($li->find('.elRowHeading > .elRowHeadingText')->text(),'代表者') !== false ){
						$userName = $li->find('.elRowContent > .elRowContentText')->text();
						$userName = Filter::filter($userName);
					}

					// -- 店舗責任者 -- //
					elseif(	strpos($li->find('.elRowHeading > .elRowHeadingText')->text(),'運営責任者') !== false ){
						$staffName = $li->find('.elRowContent > .elRowContentText')->text();
						$staffName = Filter::filter($staffName);
					}
				}
			} else {
				echo "\033[0;31m" . '外部リダイレクトため失敗：' . $line[1] . '：' . $url . "\033[0m" . PHP_EOL;
				array_push($stores, [
					$url,
					$line[1],//$tenpo,
					'外部移動エラー',//$companyName,
					'外部移動エラー',//$tel,
					'外部移動エラー',//$userName,
					'外部移動エラー'//$staffName
				]);
				continue;
			}

			// -- 最後に配列に入れる -- //
			array_push($stores, [
				$url,
				$line[1],//$tenpo,
				$companyName,
				$tel,
				$userName,
				$staffName
			]);

		}
		//Dd::dd($stores);//確認用
		return $stores;
	}

}
