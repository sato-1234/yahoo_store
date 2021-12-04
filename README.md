# yahoo_store
'https://report.valuecommerce.ne.jp/rss/yshop/storelist.php/' から'Yahoo_shoping'のストアURL一覧を、スクレイピングしてCSV化します。
また取得した'ストアURL一覧CSV'を読み込んで'Yahoo_shoping'サイトから各ストア会社概要ページ情報を、スクレイピングしてCSV化します。スクレイピングは並列処理と直列処理の両方使用します。
<br/>

## environment,Library
* OS: macOS Catalina v10.15.7
* PHP: 7.4.22
* Composer: 2.1.14
* DOMWrap: v2.0
* GuzzleHttp: v7.3

## Batch command（cd yahoo_store）
* URL list get command（Parallel processing）
* csv folder： ./storage/csv/url/url_list_2XXX-XX-XX_XXXXXX.csv
```bash
php get_url.php
```
<br>

* Store get command（Serial processing）
* csv folder： ./storage/csv/store/store_list_2XXX-XX-XX_XXXXXX.csv
* argument： [url_list_2XXX_XX_XX_XX_XX_XX.csv]　[1列目から開始番号選択]　[1列目のから終了番号選択]
```bash
php get_store.php url_list_2XXX-XX-XX_XXXXXX.csv 1 100 
```

## Environment creation（cd yahoo_store）
1. An environment of "PHP 7.4 or higher" and "Composer" is required in advance.
```bash
ｐｈｐ -v
composer -v
```
2. Specify the version in "composer.json"
```bash
{
  "require": {
    "scotteh/php-dom-wrapper": "^2.0",
    "guzzlehttp/guzzle": "^7.3"
  },
  "autoload": {
    "psr-4": {
      "App\\" : "app/"
    }
  }
}
```
3. Install libraries
```bash
composer install
```
