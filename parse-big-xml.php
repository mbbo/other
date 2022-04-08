<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Generator Big XML");

$path = '/home/bitrix/www/bitrix/catalog_export/export_pkN.xml';
$emptyPath = '/home/bitrix/www/bitrix/catalog_export/empty.xml';
$contentString = file_get_contents($path);
$finishString = '</offers></shop></yml_catalog>';
//$contentString = str_replace('<', '&lt;', $contentString);
$offerString = '';

$reader = new XMLReader();
$reader->open($path);

$i = 1;
while ($reader->read()) {

    if($reader->nodeType == XMLReader::ELEMENT) {
        // если находим элемент <offer>
        if($reader->localName == 'offer') {

            $value = $reader->expand(new DOMDocument());
            $sx = simplexml_import_dom($value);

            $arrPrice = (array)$sx->price;
            $stringPrice = $arrPrice[0];

            $arrCategory = (array)$sx->categoryId;
            $stringCategory = $arrCategory[0];

            $arrPict = (array)$sx->picture;
            $stringPict = $arrPict[0];

            $arrModel = (array)$sx->model;
            $stringModel = $arrModel[0];

            $data = array();
            $data['id'] = $reader->getAttribute('id');

            $allString = $value->textContent;
            $arrDescr = explode('MBBO' . $data['id'], $allString);

//            if ($data['id'] != 129995) {
//                continue;
//            }
//            var_dump(trim($arrDescr[1]));
            $stringDescr = htmlspecialchars(trim($arrDescr[1]));

            $offerString .= '<offer id="' . $data['id'] . '" type="vendor.model" available="true">';

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
                }
                $offerString .= '<url>https://mbbo.ru' . $newUrl . '</url>';

            }

            $offerString .= '<price>' . $stringPrice . '</price><currencyId>RUR</currencyId>';
            $offerString .= '<categoryId>' . $stringCategory . '</categoryId>';
            $offerString .= '<picture>' . $stringPict . '</picture><vendor>MBBO</vendor>';
            $offerString .= '<model>' . $stringModel . '</model>';
            $offerString .= '<description>' . $stringDescr . '</description></offer>';
        }

    }
    if ($i % 380000 == 0) {
        $contentStringEmpty = file_get_contents($emptyPath);
        $count = $i / 380000;
        $fileName = 'shop-' . $count . '.xml';
        $fn = fopen($fileName, 'w');
        $contentStringEmpty .= $offerString;

        $fullString = $contentStringEmpty . $finishString;
        fwrite($fn, $fullString);
        $offerString = '';

        fclose($fn);
    }
    $i++;
}

$reader->close();

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
