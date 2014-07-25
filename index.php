<?php

$rpc_user="";
$rpc_pass="";
$rpc_host="";
$rpc_port=8332;

require_once("jsonrpcphp/includes/jsonRPCClient.php");
$rpc=new jsonRPCClient('https://'.$rpc_user.':'.$rpc_pass.'@'.$rpc_host.':'.$rpc_port.'/');

if(isset($_POST['submit'])){
 try{
  echo "Address: ".$_POST['address']."<BR>\n";
  echo "Signature: ".$_POST['signature']."<BR>\n";
  echo "Message: ".$_POST['message']."<BR>\n";
  if($rpc->verifymessage($_POST['address'],$_POST['signature'],$_POST['message'])){
   echo "Valid.<BR>";
  }else{
   echo "Invalid.<BR>";
  }
 }catch(Exception $e){
  print_r($e);
  echo "Error.\n";
 }
  echo "<HR>";
}

echo "<HTML><BODY>\n";
echo "<FORM method=POST>\n";
echo "Address: <INPUT type=text size=40 name=address";
 if(isset($_POST['address']))echo " value=\"".$_POST['address']."\"";
echo "><BR>\n";
echo "Signature: <INPUT type=text size=40 name=signature";
 if(isset($_POST['signature']))echo " value=\"".$_POST['signature']."\"";
echo "><BR>\n";
echo "Message: <TEXTAREA name=message>";
 if(isset($_POST['signature']))echo $_POST['message'];
echo "</TEXTAREA><BR>\n";
echo "<INPUT type=submit name=submit value=\"Verify\">\n";
echo "</FORM>\n";
echo "</BODY></HTML>\n";
?>