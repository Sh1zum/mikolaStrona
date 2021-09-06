<?php
/*
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//All Code and Design is copyrighted by Level Four Development, llc
//
//Level Four Development, LLC provides this code "as is" without warranty of any kind, either express or implied,     
//including but not limited to the implied warranties of merchantability and/or fitness for a particular purpose.         
//
//Only licnesed users may use this code and storfront for live purposes. All other use is prohibited and may be 
//subject to copyright violation laws. If you have any questions regarding proper use of this code, please
//contact Level Four Development, llc and EasyCart prior to use.
//
//All use of this storefront is subject to our terms of agreement found on Level Four Development, llc's  website.
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
*/

$minimumversion = "5.3.0";

$minimummysqlversion = "3.23.58";



?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Current Server Configuration</title>
<style type="text/css">
<!--
.passed {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
	font-weight: bold;
	text-transform: capitalize;
	color: #009933;
	border-top-width: 1px;
	border-bottom-width: 1px;
	border-top-style: solid;
	border-bottom-style: solid;
	border-top-color: #CCCCCC;
	border-right-color: #CCCCCC;
	border-bottom-color: #CCCCCC;
	border-left-color: #CCCCCC;
	text-align: right;
}
.failed {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
	font-weight: bold;
	text-transform: capitalize;
	color: #CC0000;
	border-top-width: 1px;
	border-bottom-width: 1px;
	border-top-style: solid;
	border-bottom-style: solid;
	border-top-color: #CCCCCC;
	border-right-color: #CCCCCC;
	border-bottom-color: #CCCCCC;
	border-left-color: #CCCCCC;
	text-align: right;
}
.label {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
	font-weight: bold;
	color: #333333;
	border-top-width: 1px;
	border-bottom-width: 1px;
	border-top-style: solid;
	border-bottom-style: solid;
	border-top-color: #CCCCCC;
	border-right-color: #CCCCCC;
	border-bottom-color: #CCCCCC;
	border-left-color: #CCCCCC;
}
.style1 {
	font-family: Arial, Helvetica, sans-serif;
	font-weight: bold;
}
.style2 {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
}
a:link {
	color: #666666;
}
a:visited {
	color: #666666;
}
a:hover {
	color: #333333;
}
a:active {
	color: #666666;
}
.style3 {
	font-family: Arial, Helvetica, sans-serif
}
-->
</style>
</head>

<body>
<table width="929" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><table width="90%" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
          <td width="84%">&nbsp;</td>
          <td width="16%">&nbsp;</td>
        </tr>
        <tr>
          <td height="41" class="tableheadingbg"><span class="style1">Server Compatability Check </span><span class="style2">- v3.0</span></td>
          <td height="41" class="style2">&nbsp;</td>
        </tr>
        <tr>
          <td colspan="2" class="productheading style2">This utility is to check whether common extensions are enabled and that your server platform is compatible with the necessary settings to operate the storefront. There may be other extensions and settings beyond the scope of this test that are required.</td>
        </tr>
        <tr>
          <td class="productheading">&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td class="label">PHP: Version Minimum (<?php echo $minimumversion ?>) - Currently</td>
          <?php

                        	// ======= Version Check =======

                            $phpversionresult = version_compare(phpversion(), $minimumversion);

                            

                            if ($phpversionresult == -1)

                            {

                               echo "<td class='failed'>Failed</td>"; 

                            } else {

                                echo "<td class='passed'>Passed</td>";

                            }

						?>
        </tr>
        <tr>
          <td class="productheading">&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td class="label">PHP: Short_Open_Tag Enabled</td>
          <?php

                        	// ======= Short Tags =======

							if (ini_get("short_open_tag") != "1")

                            {

                               echo "<td class='failed'>Failed</td>"; 

                            } else {

                                echo "<td class='passed'>Passed</td>";

                            }

						?>
        </tr>
        <tr>
          <td class="productheading">&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td class="label">PHP: File_Uploads Enabled</td>
          <?php

                        	// ======= File Uploads =======

							if (ini_get("file_uploads") != "1")

                            {

                               echo "<td class='failed'>Failed</td>"; 

                            } else {

                                echo "<td class='passed'>Passed</td>";

                            }

						?>
        </tr>
        <tr>
          <td class="productheading">&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td class="label">PHP: mbstring extension Enabled</td>
          <?php

                        	// ======= MbString =======

							$ismbstring = extension_loaded("mbstring");

							if (!$ismbstring)

                            {

                               echo "<td class='failed'>Failed</td>"; 

                            } else {

                                echo "<td class='passed'>Passed</td>";

                            }

						?>
        </tr>
        <tr>
          <td class="productheading">&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td class="label">PHP: openSSL extension Enabled</td>
          <?php

							// ======= openSSL =======

							$isopenssl = extension_loaded("openssl");

							if (!$isopenssl)

                            {

                               echo "<td class='failed'>Failed</td>"; 

                            } else {

                                echo "<td class='passed'>Passed</td>";

                            }

						?>
        </tr>
        <tr>
          <td class="productheading">&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td class="label">PHP: CURL extension Enabled</td>
          <?php

                        	// ======= Curl =======

							$iscurl = extension_loaded("curl");

							if (!$iscurl)

                            {

                               echo "<td class='failed'>Failed</td>"; 

                            } else {

                                echo "<td class='passed'>Passed</td>";

                            }

						?>
        </tr>
        <tr>
          <td class="productheading">&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td class="label">PHP: GD Image extension Enabled</td>
          <?php

                        	// ======= GD =======

							$isgd = extension_loaded("gd");

							if (!$isgd)

                            {

                               echo "<td class='failed'>Failed</td>"; 

                            } else {

                                echo "<td class='passed'>Passed</td>";

                            }

						?>
        </tr>
        <tr>
          <td class="productheading">&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td class="label">PHP: MCrypt extension Enabled</td>
          <?php

                        	// ======= mcrypt =======

							$ismcrypt = extension_loaded("mcrypt");

							if (!$ismcrypt)

                            {

                               echo "<td class='failed'>Failed</td>"; 

                            } else {

                                echo "<td class='passed'>Passed</td>";

                            }

						?>
        </tr>
        <tr>
          <td class="productheading">&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td class="label">PHP: SOAP extension Enabled</td>
          <?php

                        	// ======= SOAP =======

							$isSOAP = extension_loaded("SOAP");

							if (!$isSOAP)

                            {

                               echo "<td class='failed'>Failed</td>"; 

                            } else {

                                echo "<td class='passed'>Passed</td>";

                            }

						?>
        </tr>
        <tr>
          <td class="productheading">&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td class="label">PHP: MySQL extension Enabled</td>
          <?php

                        	// ======= MySQL =======

							$isMySQL = extension_loaded("MySQL");

							if (!$isMySQL)

                            {

                               echo "<td class='failed'>Failed</td>"; 

                            } else {

                                echo "<td class='passed'>Passed</td>";

                            }

						?>
        </tr>
        <tr>
          <td class="productheading">&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td class="label">PHP: XMLRPC extension Enabled </td>
          <?php

                        	// ======= XMLRPC =======

							$isXMLRPC = extension_loaded("XMLRPC");

							if (!$isXMLRPC)

                            {

                               echo "<td class='failed'>Failed</td>"; 

                            } else {

                                echo "<td class='passed'>Passed</td>";

                            }

						?>
        </tr>
        <tr>
          <td class="productheading">&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td class="label">PHP: allow_call_time_pass_reference Enabled (Eway Only)</td>
          <?php

                        	// ======= allow_call_time_pass_reference (eway) =======

							if (ini_get("allow_call_time_pass_reference") != "1")

                            {

                               echo "<td class='failed'>Failed</td>"; 

                            } else {

                                echo "<td class='passed'>Passed</td>";

                            }

						?>
        </tr>
        <tr>
          <td class="productheading">&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td class="label">Maximum Upload File Size (php.ini setting)</td>
          <td class='passed'><?php

                        	// ======= Upload Max Size =======

							echo ini_get("upload_max_filesize");

						?></td>
        </tr>
        <tr>
          <td class="productheading">&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td class="productheading">&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
      </table>
      <table width="90%" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
          <td colspan="5" class="style1">&nbsp;</td>
        </tr>
        <tr>
          <td width="20%" class="label">&nbsp;</td>
          <td width="20%" class="label">&nbsp;</td>
          <td width="20%" class="label">&nbsp;</td>
          <td width="20%" class="label">&nbsp;</td>
          <td width="20%" class="label">&nbsp;</td>
        </tr>
        <tr>
          <td width="20%">&nbsp;</td>
          <td width="20%">&nbsp;</td>
          <td width="20%">&nbsp;</td>
          <td width="20%">&nbsp;</td>
          <td width="20%">&nbsp;</td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td width="20%" class="label">&nbsp;</td>
          <td width="20%" class="label">&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td width="20%">&nbsp;</td>
          <td width="20%">&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
      </table>
      <p>&nbsp;</p></td>
  </tr>
</table>
</body>
</html>