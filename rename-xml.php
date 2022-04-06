<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Generator-XML");

$path = '/home/bitrix/www/bitrix/catalog_export/export_test-sokr.xml';
$contentString = file_get_contents($path);
//$contentString = str_replace('<', '&lt;', $contentString);

$reader = new XMLReader();
$reader->open($path);

while ($reader->read()) {

    if($reader->nodeType == XMLReader::ELEMENT) {
        // если находим элемент <offer>
        if($reader->localName == 'offer') {

            $data = array();
            // считываем аттрибут number
            $data['id'] = $reader->getAttribute('id');

            if(CModule::IncludeModule('iblock') && $data['id']) {
                $arSelect = Array("CODE","DETAIL_PAGE_URL");
                $arFilter = Array("IBLOCK_ID"=>44, "ID"=>$data['id']);
                $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
                while($ob = $res->GetNextElement())
                {
                    $arFields = $ob->GetFields();
                    $code = $arFields["CODE"];
                    $url = $arFields["DETAIL_PAGE_URL"];
                    $newUrl = str_replace($data['id'], $code, $url);
                    $contentString = str_replace($url, $newUrl, $contentString);

//                    $newUrl = '<url><loc>' . str_replace($arr[1], $code, $href) . '</loc><lastmod>' . $time . '</lastmod></url>';
//                    $startString .= $newUrl;
                }
            }

        }
    }
}

//var_dump($contentString);

$fn = fopen('xml.xml', 'w');
fwrite($fn, $contentString);
fclose($fn);




//$contentXml = str_replace('<', '&lt;', $content);
////$contentXml = json_encode(simplexml_load_string($contentXml));
//$arrXml = json_decode($contentXml, true);
/*$startString = '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';*/
//$finishString = '</urlset>';
//
//
//var_dump($contentXml);


require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
