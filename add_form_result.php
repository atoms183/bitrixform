<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
?>
<?$tmp = implode(",",$_POST);
print_r($tmp); //êîíòðîëü ïåðåäà÷è
?>


<?
    CModule::IncludeModule('iblock');
    //Ïîãíàëè
    $el = new CIBlockElement;
    $iblock_id = 4;
    $section_id = false;
    $section_id[$i] = $_POST['section_id']; //Ðàçäåëû äëÿ äîáàâëåíèÿ

    //Ñâîéñòâà
    $PROP = array();

    $PROP['SUMMA'] = $_POST['credit']; //Ñâîéñòâî ÷èñëî
    $PROP['EMAIL'] = $_POST['mail']; //Ñâîéñòâî ñïèñîê
	$PROP['SURNAME'] = $_POST['user']; //Ñâîéñòâî ñïèñîê



    //Îñíîâíûå ïîëÿ ýëåìåíòà
    $fields = array(
         "DATE_CREATE" => date("d.m.Y H:i:s"), //Ïåðåäàåì äàòà ñîçäàíèÿ
    "CREATED_BY" => $GLOBALS['USER']->GetID(),    //Ïåðåäàåì ID ïîëüçîâàòåëÿ êòî äîáàâëÿåò
    "IBLOCK_SECTION" => false, //ID ðàçäåëîâèáî íåò îòäåëüíîãî ðàçäåëà
    "IBLOCK_ID" => 4, //ID èíôîðìàöèîííîãî áëîêà îí 4-ûé
    "PROPERTY_VALUES" => $PROP, // Ïåðåäàåì ìàññèâ çíà÷åíèè äëÿ ñâîéñòâ
    "NAME" => $_POST['user'],
    "ACTIVE" => "Y", //ïîóìîë÷àíèþ äåëàåì àêòèâíûì èëè ñòàâèì N äëÿ îòêëþ÷åíèè ïîóìîë÷àíèþ
    );
    print_r ($fields); //êîíòðîëü ïåðåäà÷è
   $el->Add($fields);
//}


// CRM server conection data
define('CRM_HOST', ''); // your CRM domain name
define('CRM_PORT', '443'); // CRM server port
define('CRM_PATH', '/crm/configs/import/lead.php'); // CRM server REST service path

// CRM server authorization data
define('CRM_LOGIN', ''); // login of a CRM user able to manage leads
define('CRM_PASSWORD', ''); // password of a CRM user
// OR you can send special authorization hash which is sent by server after first successful connection with login and password
//define('CRM_AUTH', 'e54ec19f0c5f092ea11145b80f465e1a'); // authorization hash

/********************************************************************************************/

// POST processing
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	$leadData = $_POST['DATA'];

	// get lead data from the form
	$postData = array(
		'TITLE' => $_POST['user'],
		'UF_CRM_1482483191'=> $_POST['user'],
		'UF_CRM_1482483218'=> $_POST['credit'],
		'UF_CRM_1482483235'=> $_POST['mail'],
	);
	print_r ($postData);

	// append authorization data
	if (defined('CRM_AUTH'))
	{
		$postData['AUTH'] = CRM_AUTH;
	}
	else
	{
		$postData['LOGIN'] = CRM_LOGIN;
		$postData['PASSWORD'] = CRM_PASSWORD;
	}

	// open socket to CRM
	$fp = fsockopen("ssl://".CRM_HOST, CRM_PORT, $errno, $errstr, 30);
	if ($fp)
	{
		// prepare POST data
		$strPostData = '';
		foreach ($postData as $key => $value)
			$strPostData .= ($strPostData == '' ? '' : '&').$key.'='.urlencode($value);

		// prepare POST headers
		$str = "POST ".CRM_PATH." HTTP/1.0\r\n";
		$str .= "Host: ".CRM_HOST."\r\n";
		$str .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$str .= "Content-Length: ".strlen($strPostData)."\r\n";
		$str .= "Connection: close\r\n\r\n";

		$str .= $strPostData;

		// send POST to CRM
		fwrite($fp, $str);

		// get CRM headers
		$result = '';
		while (!feof($fp))
		{
			$result .= fgets($fp, 128);
		}
		fclose($fp);

		// cut response headers
		$response = explode("\r\n\r\n", $result);

		$output = '<pre>'.print_r($response[1], 1).'</pre>';
	}
	else
	{
		echo 'Connection Failed! '.$errstr.' ('.$errno.')';
	}
}
else
{
	$output = '';
}
?>

<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>
