<?

$arPaths = [
	'/home/bitrix/ext_www/mbbo.ru/sitemap-iblock-44.xml',
	'/home/bitrix/ext_www/mbbo.ru/sitemap-iblock-44.part1.xml',
	'/home/bitrix/ext_www/mbbo.ru/sitemap-iblock-44.part2.xml',
	'/home/bitrix/ext_www/mbbo.ru/sitemap-iblock-44.part3.xml',
	'/home/bitrix/ext_www/mbbo.ru/sitemap-iblock-44.part4.xml',
	'/home/bitrix/ext_www/mbbo.ru/sitemap-iblock-44.part5.xml',
	'/home/bitrix/ext_www/mbbo.ru/sitemap-iblock-44.part6.xml'
];

//$arPaths = [
//    '/home/bitrix/ext_www/mbbo.ru/sitemap-iblock-44.part1.xml'
//];


foreach ($arPaths as $path) {
    $content = file_get_contents($path);
    preg_match_all("/https:\/\/mbbo\.ru\/catalog\/([a-z0-9-_\/]{2,}\/[0-9]{3,})/", $content, $arUrls);

    $startString = '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
    $finishString = '</urlset>';

    foreach ($arUrls[1] as $key=> $url) {

        preg_match('/[a-z0-9-\/]\/([0-9]{2,})/', $url, $arr);

        if(CModule::IncludeModule('iblock')) {
            $arSelect = Array("CODE");
            $arFilter = Array("IBLOCK_ID"=>44, "ID"=>$arr[1]);
            $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
            while($ob = $res->GetNextElement())
            {
                $arFields = $ob->GetFields();
                $code = $arFields["CODE"];

                $newUrl = '<url><loc>https://mbbo.ru/catalog/' . str_replace($arr[1], $code, $url) . '</loc><lastmod>2021-05-31T15:43:32+03:00</lastmod></url>';

                $startString .= $newUrl;

            }
        }
    }

    $fullString = $startString . $finishString;

    $fn = fopen($path, 'w');
    fwrite($fn, $fullString);
    fclose($fn);


}
?>
