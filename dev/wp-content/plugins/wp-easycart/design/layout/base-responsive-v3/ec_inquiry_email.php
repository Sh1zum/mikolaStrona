<html>

<head>

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <style type='text/css'>

    <!--

		.style20 {font-family: Arial, Helvetica, sans-serif; font-weight: bold; font-size: 12px; }

        .style22 {font-family: Arial, Helvetica, sans-serif; font-size: 12px; }

		.ec_option_label{font-family: Arial, Helvetica, sans-serif; font-size:11px; font-weight:bold; }

		.ec_option_name{font-family: Arial, Helvetica, sans-serif; font-size:11px; }

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

			<td colspan='4' align='left' class='style22'>

				<h3><?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_inquiry_title' ); ?></h3>

            </td>

        </tr>

        <tr>

			<td colspan='4' align='left' class='style22'>

				&nbsp;&nbsp;&nbsp;

            </td>

        </tr>

        <tr>

			<td colspan='4' align='left' class='style22'>

                <span class='style22'><?php echo $product->title; ?> (<?php echo $product->model_number; ?>)</span>

            </td>

        </tr>

        <tr>

			<td colspan='4' align='left' class='style22'>

                <span class='style22'><?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_inquiry_name' ); ?> <?php echo stripslashes( $inquiry_name ); ?></span>

            </td>

        </tr>

        <tr>

			<td colspan='4' align='left' class='style22'>

                <span class='style22'><?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_inquiry_email' ); ?> <?php echo stripslashes( $inquiry_email ); ?></span>

            </td>

        </tr>

        <?php 
		if( $product->use_advanced_optionset ){
			
			foreach( $option_vals as $advanced_option ){
				
				if( $advanced_option['option_type'] == "file" ){
											
					echo "<tr><td><span class=\"ec_option_label\">" . $advanced_option['option_label'] . ":</span> <span class=\"ec_option_name\"><a href=\"" . plugins_url( "/wp-easycart-data/products/uploads/" . $file_temp_num . "/" . $advanced_option['optionitem_value'] ) . "\">" . $advanced_option['optionitem_value'] . "</a></span></td></tr>";

				}else if( $advanced_option['option_type'] == "grid" ){

					echo "<tr><td><span class=\"ec_option_label\">" . $advanced_option['option_label'] . ":</span> <span class=\"ec_option_name\">" . $advanced_option['optionitem_name'] . " (" . $advanced_option['optionitem_value'] . ")" . "</span></td></tr>";

				}else{

					echo "<tr><td><span class=\"ec_option_label\">" . $advanced_option['option_label'] . ":</span> <span class=\"ec_option_name\">" . htmlspecialchars( $advanced_option['optionitem_value'], ENT_QUOTES ) . "</span></td></tr>";

				}

			}

		}else{

			if( $option1 ){

				echo "<tr><td><span class=\"ec_option_name\">" . $option1->optionitem_name . "</span></td></tr>";

			}
			
			if( $option2 ){

				echo "<tr><td><span class=\"ec_option_name\">" . $option2->optionitem_name . "</span></td></tr>";

			}
			
			if( $option3 ){

				echo "<tr><td><span class=\"ec_option_name\">" . $option3->optionitem_name . "</span></td></tr>";

			}
			
			if( $option4 ){

				echo "<tr><td><span class=\"ec_option_name\">" . $option4->optionitem_name . "</span></td></tr>";

			}
			
			if( $option5 ){

				echo "<tr><td><span class=\"ec_option_name\">" . $option5->optionitem_name . "</span></td></tr>";

			}

		}// Close basic options
		?>

        <tr>

			<td colspan='4' align='left' class='style22'>

                <span class='style22'><?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_inquiry_message' ); ?> <?php echo nl2br( stripslashes( $inquiry_message ) ); ?></span>

            </td>

        </tr>

        <tr>

			<td colspan='4' align='left' class='style22'>

				&nbsp;&nbsp;&nbsp;

            </td>

        </tr>

        <tr>

			<td colspan='4' align='left' class='style22'>

				<span class='style22'><?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_inquiry_thank_you' ); ?></span>

            </td>

        </tr>

    </table>

</body>

</html>