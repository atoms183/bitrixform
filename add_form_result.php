<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
?>
<?$tmp = implode(",",$_POST);
print_r($tmp); //�������� ��������
?>


<?
    CModule::IncludeModule('iblock');
    //�������
    $el = new CIBlockElement;
    $iblock_id = 4;
    $section_id = false;
    $section_id[$i] = $_POST['section_id']; //������� ��� ����������

    //��������
    $PROP = array();

    $PROP['SUMMA'] = $_POST['credit']; //�������� �����
    $PROP['EMAIL'] = $_POST['mail']; //�������� ������
	$PROP['SURNAME'] = $_POST['user']; //�������� ������



    //�������� ���� ��������
    $fields = array(
         "DATE_CREATE" => date("d.m.Y H:i:s"), //�������� ���� ��������
    "CREATED_BY" => $GLOBALS['USER']->GetID(),    //�������� ID ������������ ��� ���������
    "IBLOCK_SECTION" => false, //ID ����������� ��� ���������� �������
    "IBLOCK_ID" => 4, //ID ��������������� ����� �� 4-��
    "PROPERTY_VALUES" => $PROP, // �������� ������ �������� ��� �������
    "NAME" => $_POST['user'],
    "ACTIVE" => "Y", //����������� ������ �������� ��� ������ N ��� ���������� �����������
    );
    print_r ($fields); //�������� ��������
   $el->Add($fields);
//}


// CRM server conection data
define('CRM_HOST', 'ajstyles.bitrix24.ru'); // your CRM domain name
define('CRM_PORT', '443'); // CRM server port
define('CRM_PATH', '/crm/configs/import/lead.php'); // CRM server REST service path

// CRM server authorization data
define('CRM_LOGIN', 'sony183@bk.ru'); // login of a CRM user able to manage leads
define('CRM_PASSWORD', '1q2w3e4r'); // password of a CRM user
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