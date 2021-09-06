<html>


    <head>


    <title><?php echo $GLOBALS['language']->get_text( "account_forgot_password_email", "account_forgot_password_email_title" ); ?></title>


    <style type='text/css'>


<!--


.style20 {


	font-family: Arial, Helvetica, sans-serif;


	font-weight: bold;


	font-size: 12px;


}


.style22 {


	font-family: Arial, Helvetica, sans-serif;


	font-size: 12px;


}


-->


</style>


    </head>


    <body>


    <table width='539' border='0' align='center'>


      <tr>


        <td colspan='4' align='left' class='style22'>
        
        	<img src='<?php echo $email_logo_url; ?>' style="max-height:250px; max-width:100%; height:auto;">
            
        </td>


      </tr>


      <tr>


        <td colspan='4' align='left' class='style22'><p><br>


            <?php echo $GLOBALS['language']->get_text( "account_forgot_password_email", "account_forgot_password_email_dear" ); ?> <?php echo $user->first_name; ?> <?php echo $user->last_name; ?>:</p>


          <p><?php echo $GLOBALS['language']->get_text( "account_forgot_password_email", "account_forgot_password_email_your_new_password" ); ?> <strong><?php echo $new_password; ?></strong></p>


          <p><?php echo $GLOBALS['language']->get_text( "account_forgot_password_email", "account_forgot_password_email_change_password" ); ?></p></td>


      </tr>


      <tr>


        <td colspan='4' class='style22'><p><br>


            <?php echo $GLOBALS['language']->get_text( "account_forgot_password_email", "account_forgot_password_email_thank_you" ); ?></p>


          <p>&nbsp;</p></td>


      </tr>


    </table>


</body>


</html>