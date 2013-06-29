<?php
/* 
this example is used to convert any doc format to text
author: Gourav Mehta
author's email: gouravmehta@gmail.com
author's phone: +91-9888316141
*/ 
$client = new SoapClient('http://salim.bpopower.com/api/soap/?wsdl');

// If some stuff requires api authentification,
// then get a session token
$session = $client->login('admin', 'Admin786!@#');

// get attribute set
$attributeSets = $client->call($session, 'product_attribute_set.list');
$attributeSet = $attributeSets[1];
//var_dump($attributeSet);

require("doc2txt.class.php");

$docObj = new Doc2Txt("DHAKA_ZONE.docx");
//$docObj = new Doc2Txt("test.doc");

$txt = $docObj->convertToText();
//echo $txt;

function clean_space($text=''){
  for($i=1;$i<=50;$i++){
    $text = str_ireplace('  ',' ',$text);
  }
  return $text;
}
$product_sku = 1;
for($i=1; true; $i++){
$j = $i+1;
$c = array();
preg_match_all('/(?:\('.$i.'\))(.*)(?:\('.$j.'\))/si',$txt,$c);

if(!isset($c[1][0])) break;
$value =  trim($c[1][0]);

/*
preg_match('/^(.*)Tel *?\(Off\)/si',$value,$address); // address
preg_match('/Tel *?\(Off\):(.*)Tel *?\(Factory\):/si',$value,$office_tel);
preg_match('/Tel *?\(Factory\):(.*)Fax *?\(Off\):/si',$value,$factory_tel);
preg_match('/Fax *?\(Off\):(.*)Email:/si',$value,$fax);
preg_match('/Email:(.*)Website:/si',$value,$email);
preg_match('/Website:(.*)Employee *?No\.:/si',$value,$website);
preg_match('/Employee *?No\.:(.*)Machine *?No\.:/si',$value,$em_no);
preg_match('/Machine *?No\.:(.*)Product:/si',$value,$machine_no);
preg_match('/Product:(.*)Production *?\(Doz\) *?Yr\.:/si',$value,$product);
preg_match('/Production *?\(Doz\) *?Yr\.:(.*)$/si',$value,$production_doz_yr);
var_dump($address[1]);
var_dump($office_tel[1]);
var_dump($factory_tel[1]);
var_dump($fax[1]);
var_dump($email[1]);
var_dump($website[1]);
var_dump($em_no[1]);
var_dump($machine_no[1]);
var_dump($product[1]);
var_dump($production_doz_yr[1]);*/

$data = explode("\n", $value);
$product_sku++;
$address="";
$tel_office="";
$tel_factory="";
$fax="";
$email="";
$website="";
$em_no="";
$machine_no="";
$production_doz_yr="";
$product = "";
$title =  $data[0];
foreach($data as $textByLine) {
  clean_space($textByLine);
  if(preg_match('/Tel *?\(Off\)/',$textByLine))	$tel_office = str_ireplace("Tel (off):","",$textByLine);
  else if(preg_match('/Tel *?\(Factory\)/',$textByLine))	$tel_factory = str_ireplace("Tel (off):","",$textByLine);
  else if(preg_match('/Fax *?\(Off\)/',$textByLine))	$fax = str_ireplace("Fax (Off):","",$textByLine);
  else if(preg_match('/Email:/',$textByLine))	$email = str_ireplace("Email:","",$textByLine);
  else if(preg_match('/Website:/',$textByLine))	$website = str_ireplace("Website:","",$textByLine);
  else if(preg_match('/Employee No\.:/',$textByLine))	$em_no = str_ireplace("Employee No.:","",$textByLine);
  else if(preg_match('/Machine No\.:/',$textByLine))	$machine_no = str_ireplace("Machine No.:","",$textByLine);
  else if(preg_match('/Product:/',$textByLine))	$product = str_ireplace("Product:","",$textByLine);
  else if(preg_match('/Production \(Doz\) Yr\.:/',$textByLine))	$production_doz_yr = str_ireplace("Production (Doz) Yr.:","",$textByLine);
  else $address.=$textByLine;
  $address = str_ireplace($title,"",$address);
  }

$result = $client->call($session, 'catalog_product.create', array('simple', $attributeSet['set_id'], 'factory'.$product_sku, array(
    'categories' => array(3),
    'websites' => array(1),
    'name' => $title,
    'email' => $email,
    'machine_no' => $machine_no,
	'em_no' => $em_no,
	'fax' => $fax,
	'address_line' => $address,
	'telephone' => $tel_office,
	'cellphone' => $tel_factory,
	'production' => $production_doz_yr,
    'status' => '1',
    'visibility' => '4',
    'tax_class_id' => 1,
	'stock_data' => array(
	'qty' => 10,
	'is_in_stock' => 1
))));

var_dump ($result); 
}
?>
