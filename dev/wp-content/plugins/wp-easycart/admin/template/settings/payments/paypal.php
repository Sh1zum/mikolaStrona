<div class="ec_admin_paypal_row">
    <div class="ec_admin_slider_row">
        <?php wp_easycart_admin( )->preloader->print_preloader( "ec_admin_paypal_display_loader" ); ?>
        <h3><?php _e( 'PayPal', 'wp-easycart' ); ?></h3>
        <div class="ec_admin_slider_row_description">
            <div><?php _e( 'PayPal gives your customers a payment method that most are comfortable using and does not require an SSL Certificate.', 'wp-easycart' ); ?></div>
            <?php if( get_option( 'ec_option_payment_third_party' ) == 'paypal' && !get_option( 'ec_option_paypal_use_sandbox' ) && get_option( 'ec_option_paypal_enable_pay_now' ) && get_option( 'ec_option_paypal_production_merchant_id' ) != '' ){ ?>
            <a href="<?php echo wp_easycart_admin( )->available_url; ?>/paypal-v2/production_onboard.php?redirect=<?php echo urlencode( admin_url( ) . '?wpeasycart_paypal_onboard=production' ); ?>"><?php _e( 'Switch Accounts', 'wp-easycart' ); ?></a>
            <?php }else if( get_option( 'ec_option_payment_third_party' ) == 'paypal' && get_option( 'ec_option_paypal_use_sandbox' ) && get_option( 'ec_option_paypal_enable_pay_now' ) && get_option( 'ec_option_paypal_sandbox_merchant_id' ) != '' ){ ?>
            <a href="<?php echo wp_easycart_admin( )->available_url; ?>/paypal-v2/sandbox_onboard.php?redirect=<?php echo urlencode( admin_url( ) . '?wpeasycart_paypal_onboard=sandbox' ); ?>"><?php _e( 'Switch Accounts', 'wp-easycart' ); ?></a>
            <?php }?>
            <a href="#" onclick="return paypal_show_advanced( );" id="paypal_advanced_link"><?php _e( 'Advanced Options', 'wp-easycart' ); ?> &#9660;</a>
            <input type="hidden" value="<?php echo get_option( 'ec_option_paypal_email' ); ?>" name="ec_option_paypal_email" id="ec_option_paypal_email" />
            <input type="hidden" name="use_paypal" id="use_paypal" value="<?php echo ( get_option( 'ec_option_payment_third_party' ) == 'paypal' ) ? 1 : 0; ?>" />
            <input type="hidden" name="ec_option_paypal_use_sandbox" id="ec_option_paypal_use_sandbox" value="<?php echo ( get_option( 'ec_option_paypal_use_sandbox' ) ) ? 1 : 0; ?>" />
        </div>
        <div class="ec_admin_toggles_wrap">
            <div class="ec_admin_toggle">
                <span><?php _e( 'Enable Live', 'wp-easycart' ); ?>:</span>
                <?php if( !get_option( 'ec_option_paypal_enable_pay_now' ) || get_option( 'ec_option_paypal_production_merchant_id' ) == '' ){ ?>
                <a href="<?php echo wp_easycart_admin( )->available_url; ?>/paypal-v2/production_onboard.php?redirect=<?php echo urlencode( admin_url( ) . '?wpeasycart_paypal_onboard=production' ); ?>">
                <span></span>
				<?php }?>
                <label class="ec_admin_switch">
                    <input type="checkbox" onclick="return paypal_live_on_off( );" class="ec_admin_slider_checkbox" value="1" id="ec_option_paypal_enable_live"<?php if( get_option( 'ec_option_payment_third_party' ) == 'paypal' && !get_option( 'ec_option_paypal_use_sandbox' ) && get_option( 'ec_option_paypal_enable_pay_now' ) && get_option( 'ec_option_paypal_production_merchant_id' ) != '' ){ ?> checked="checked"<?php }?>>
                    <span class="ec_admin_slider round"></span>
                </label>
                <?php if( !get_option( 'ec_option_paypal_enable_pay_now' ) || get_option( 'ec_option_paypal_production_merchant_id' ) == '' ){ ?>
                </a> 
                <?php }?>
            </div>
            <div class="ec_admin_toggle">
                <span><?php _e( 'Enable Sandbox', 'wp-easycart' ); ?>:</span>
                <?php if( !get_option( 'ec_option_paypal_enable_pay_now' ) || get_option( 'ec_option_paypal_sandbox_merchant_id' ) == '' ){ ?>
                <a href="<?php echo wp_easycart_admin( )->available_url; ?>/paypal-v2/sandbox_onboard.php?redirect=<?php echo urlencode( admin_url( ) . '?wpeasycart_paypal_onboard=sandbox' ); ?>">
                <span></span>
				<?php }?>
                <label class="ec_admin_switch">
                    <input type="checkbox" onclick="return paypal_sandbox_on_off( );" class="ec_admin_slider_checkbox" value="1" id="ec_option_paypal_enable_sandbox"<?php if( get_option( 'ec_option_payment_third_party' ) == 'paypal' && get_option( 'ec_option_paypal_use_sandbox' ) && get_option( 'ec_option_paypal_enable_pay_now' ) && get_option( 'ec_option_paypal_sandbox_merchant_id' ) != '' ){ ?> checked="checked"<?php }?>>
                    <span class="ec_admin_slider round"></span>
                </label>
                <?php if( !get_option( 'ec_option_paypal_enable_pay_now' ) || get_option( 'ec_option_paypal_sandbox_merchant_id' ) == '' ){ ?>
                </a> 
                <?php }?>
            </div>
        </div>
    
        <div class="ec_admin_paypal_advanced_toggle_on" style="display:none;">
        	<div class="ec_admin_settings_input ec_admin_settings_advanced_payment_section ec_admin_settings_show">
                <div><?php _e( 'Default Currency', 'wp-easycart' ); ?>
                    <select name="ec_option_paypal_currency_code" id="ec_option_paypal_currency_code">
                        <option value="USD" <?php if (get_option('ec_option_paypal_currency_code') == 'USD') echo ' selected'; ?>>U.S. Dollar</option>
                        <option value="AUD" <?php if (get_option('ec_option_paypal_currency_code') == 'AUD') echo ' selected'; ?>>Australian Dollar</option>
                        <option value="BRL" <?php if (get_option('ec_option_paypal_currency_code') == 'BRL') echo ' selected'; ?>>Brazilian Real</option>
                        <option value="CAD" <?php if (get_option('ec_option_paypal_currency_code') == 'CAD') echo ' selected'; ?>>Canadian Dollar</option>
                        <option value="CZK" <?php if (get_option('ec_option_paypal_currency_code') == 'CZK') echo ' selected'; ?>>Czech Koruna</option>
                        <option value="DKK" <?php if (get_option('ec_option_paypal_currency_code') == 'DKK') echo ' selected'; ?>>Danish Krone</option>
                        <option value="EUR" <?php if (get_option('ec_option_paypal_currency_code') == 'EUR') echo ' selected'; ?>>Euro</option>
                        <option value="HKD" <?php if (get_option('ec_option_paypal_currency_code') == 'HKD') echo ' selected'; ?>>Hong Kong Dollar</option>
                        <option value="HUF" <?php if (get_option('ec_option_paypal_currency_code') == 'HUF') echo ' selected'; ?>>Hungarian Forint</option>
                        <option value="ILS" <?php if (get_option('ec_option_paypal_currency_code') == 'ILS') echo ' selected'; ?>>Israeli New Sheqel</option>
                        <option value="JPY" <?php if (get_option('ec_option_paypal_currency_code') == 'JPY') echo ' selected'; ?>>Japanese Yen</option>
                        <option value="MYR" <?php if (get_option('ec_option_paypal_currency_code') == 'MYR') echo ' selected'; ?>>Malaysian Ringgit</option>
                        <option value="MXN" <?php if (get_option('ec_option_paypal_currency_code') == 'MXN') echo ' selected'; ?>>Mexican Peso</option>
                        <option value="NOK" <?php if (get_option('ec_option_paypal_currency_code') == 'NOK') echo ' selected'; ?>>Norwegian Krone</option>
                        <option value="NZD" <?php if (get_option('ec_option_paypal_currency_code') == 'NZD') echo ' selected'; ?>>New Zealand Dollar</option>
                        <option value="PHP" <?php if (get_option('ec_option_paypal_currency_code') == 'PHP') echo ' selected'; ?>>Philippine Peso</option>
                        <option value="PLN" <?php if (get_option('ec_option_paypal_currency_code') == 'PLN') echo ' selected'; ?>>Polish Zloty</option>
                        <option value="GBP" <?php if (get_option('ec_option_paypal_currency_code') == 'GBP') echo ' selected'; ?>>Pound Sterling</option>
                        <option value="SGD" <?php if (get_option('ec_option_paypal_currency_code') == 'SGD') echo ' selected'; ?>>Singapore Dollar</option>
                        <option value="SEK" <?php if (get_option('ec_option_paypal_currency_code') == 'SEK') echo ' selected'; ?>>Swedish Krona</option>
                        <option value="CHF" <?php if (get_option('ec_option_paypal_currency_code') == 'CHF') echo ' selected'; ?>>Swiss Franc</option>
                        <option value="TWD" <?php if (get_option('ec_option_paypal_currency_code') == 'TWD') echo ' selected'; ?>>Taiwan New Dollar</option>
                        <option value="THB" <?php if (get_option('ec_option_paypal_currency_code') == 'THB') echo ' selected'; ?>>Thai Baht</option>
                        <option value="TRY" <?php if (get_option('ec_option_paypal_currency_code') == 'TRY') echo ' selected'; ?>>Turkish Lira</option>
                    </select>
                </div>
                <div><?php _e( 'Use Selection from Currency Widget on Checkout', 'wp-easycart' ); ?>
                    <select name="ec_option_paypal_use_selected_currency" id="ec_option_paypal_use_selected_currency">
                        <option value="1" <?php if (get_option('ec_option_paypal_use_selected_currency') == 1) echo ' selected'; ?>>Yes</option>
                        <option value="0" <?php if (get_option('ec_option_paypal_use_selected_currency') == 0) echo ' selected'; ?>>No</option>
                    </select>
                </div>
                <div><?php _e( 'Language Code', 'wp-easycart' ); ?>
                    <select name="ec_option_paypal_lc" id="ec_option_paypal_lc">
                        <option value="US" <?php if (get_option('ec_option_paypal_lc') == 'US') echo ' selected'; ?>>United States</option>
                        <option value="AU" <?php if (get_option('ec_option_paypal_lc') == 'AU') echo ' selected'; ?>>Australia</option>
                        <option value="AT" <?php if (get_option('ec_option_paypal_lc') == 'AT') echo ' selected'; ?>>Austria</option>
                        <option value="BE" <?php if (get_option('ec_option_paypal_lc') == 'BE') echo ' selected'; ?>>Belgium</option>
                        <option value="BR" <?php if (get_option('ec_option_paypal_lc') == 'BR') echo ' selected'; ?>>Brazil</option>
                        <option value="CA" <?php if (get_option('ec_option_paypal_lc') == 'CA') echo ' selected'; ?>>Canada</option>
                        <option value="CH" <?php if (get_option('ec_option_paypal_lc') == 'CH') echo ' selected'; ?>>Switzerland</option>
                        <option value="CN" <?php if (get_option('ec_option_paypal_lc') == 'CN') echo ' selected'; ?>>China</option>
                        <option value="DE" <?php if (get_option('ec_option_paypal_lc') == 'DE') echo ' selected'; ?>>Germany</option>
                        <option value="ES" <?php if (get_option('ec_option_paypal_lc') == 'ES') echo ' selected'; ?>>Spain</option>
                        <option value="GB" <?php if (get_option('ec_option_paypal_lc') == 'GB') echo ' selected'; ?>>United Kingdom</option>
                        <option value="FR" <?php if (get_option('ec_option_paypal_lc') == 'FR') echo ' selected'; ?>>France</option>
                        <option value="IT" <?php if (get_option('ec_option_paypal_lc') == 'IT') echo ' selected'; ?>>Italy</option>
                        <option value="NL" <?php if (get_option('ec_option_paypal_lc') == 'NL') echo ' selected'; ?>>Netherlands</option>
                        <option value="PL" <?php if (get_option('ec_option_paypal_lc') == 'PL') echo ' selected'; ?>>Poland</option>
                        <option value="PT" <?php if (get_option('ec_option_paypal_lc') == 'PT') echo ' selected'; ?>>Portugal</option>
                        <option value="RU" <?php if (get_option('ec_option_paypal_lc') == 'RU') echo ' selected'; ?>>Russia</option>
                        <option value="da_DK" <?php if (get_option('ec_option_paypal_lc') == 'da_DK') echo ' selected'; ?>>Danish (for Denmark only)</option>
                        <option value="he_IL" <?php if (get_option('ec_option_paypal_lc') == 'he_IL') echo ' selected'; ?>>Hebrew (all)</option>
                        <option value="id_ID" <?php if (get_option('ec_option_paypal_lc') == 'id_ID') echo ' selected'; ?>>Indonesian (for Indonesia only)</option>
                        <option value="jp_JP" <?php if (get_option('ec_option_paypal_lc') == 'jp_JP') echo ' selected'; ?>>Japanese (for Japan only)</option>
                        <option value="no_NO" <?php if (get_option('ec_option_paypal_lc') == 'no_NO') echo ' selected'; ?>>Norwegian (for Norway only)</option>
                        <option value="pt_BR" <?php if (get_option('ec_option_paypal_lc') == 'pt_BR') echo ' selected'; ?>>Brazilian Portuguese (for Portugal and Brazil only)</option>
                        <option value="ru_RU" <?php if (get_option('ec_option_paypal_lc') == 'ru_RU') echo ' selected'; ?>>Russian (for Lithuania, Latvia, and Ukraine only)</option>
                        <option value="sv_SE" <?php if (get_option('ec_option_paypal_lc') == 'sv_SE') echo ' selected'; ?>>Swedish (for Sweden only)</option>
                        <option value="th_TH" <?php if (get_option('ec_option_paypal_lc') == 'th_TH') echo ' selected'; ?>>Thai (for Thailand only)</option>
                        <option value="tr_TR" <?php if (get_option('ec_option_paypal_lc') == 'tr_TR') echo ' selected'; ?>>Turkish (for Turkey only)</option>
                        <option value="zh_CN" <?php if (get_option('ec_option_paypal_lc') == 'zh_CN') echo ' selected'; ?>>Simplified Chinese (for China only)</option>
                        <option value="zh_HK" <?php if (get_option('ec_option_paypal_lc') == 'zh_HK') echo ' selected'; ?>>Traditional Chinese (for Hong Kong only)</option>
                        <option value="zh_TW" <?php if (get_option('ec_option_paypal_lc') == 'zh_TW') echo ' selected'; ?>>Traditional Chinese (for Taiwan only)</option>
                    </select>
                </div>
                <div><?php _e( 'Character Set (UTF-8 for Most)', 'wp-easycart' ); ?>
                    <select name="ec_option_paypal_charset" id="ec_option_paypal_charset">
                        <option value="UTF-8" <?php if (get_option('ec_option_paypal_charset') == 'UTF-8') echo ' selected'; ?>>UTF-8</option>
                        <option value="Big5" <?php if (get_option('ec_option_paypal_charset') == 'Big5') echo ' selected'; ?>>Big5 (Traditional Chinese in Taiwan)</option>
                        <option value="EUC-JP" <?php if (get_option('ec_option_paypal_charset') == 'EUC-JP') echo ' selected'; ?>>EUC-JP</option>
                        <option value="EUC-KR" <?php if (get_option('ec_option_paypal_charset') == 'EUC-KR') echo ' selected'; ?>>EUC-KR</option>
                        <option value="EUC-TW" <?php if (get_option('ec_option_paypal_charset') == 'EUC-TW') echo ' selected'; ?>>EUC-TW</option>
                        <option value="gb2312" <?php if (get_option('ec_option_paypal_charset') == 'gb2312') echo ' selected'; ?>>gb2312 (Simplified Chinese)</option>
                        <option value="gbk" <?php if (get_option('ec_option_paypal_charset') == 'gbk') echo ' selected'; ?>>gbk</option>
                        <option value="HZ-GB-2312" <?php if (get_option('ec_option_paypal_charset') == 'HZ-GB-2312') echo ' selected'; ?>>HZ-GB-2312 (Traditional Chinese in Hong Kong)</option>
                        <option value="ibm-862" <?php if (get_option('ec_option_paypal_charset') == 'ibm-862') echo ' selected'; ?>>ibm-862 (Hebrew with European characters)</option>
                        <option value="ISO-2022-CN" <?php if (get_option('ec_option_paypal_charset') == 'ISO-2022-CN') echo ' selected'; ?>>ISO-2022-CN</option>
                        <option value="ISO-2022-JP" <?php if (get_option('ec_option_paypal_charset') == 'ISO-2022-JP') echo ' selected'; ?>>ISO-2022-JP</option>
                        <option value="ISO-2022-KR" <?php if (get_option('ec_option_paypal_charset') == 'ISO-2022-KR') echo ' selected'; ?>>ISO-2022-KR</option>
                        <option value="ISO-8859-1" <?php if (get_option('ec_option_paypal_charset') == 'ISO-8859-1') echo ' selected'; ?>>ISO-8859-1 (Western European Languages)</option>
                        <option value="ISO-8859-2" <?php if (get_option('ec_option_paypal_charset') == 'ISO-8859-2') echo ' selected'; ?>>ISO-8859-2</option>
                        <option value="ISO-8859-3" <?php if (get_option('ec_option_paypal_charset') == 'ISO-8859-3') echo ' selected'; ?>>ISO-8859-3</option>
                        <option value="ISO-8859-4" <?php if (get_option('ec_option_paypal_charset') == 'ISO-8859-4') echo ' selected'; ?>>ISO-8859-4</option>
                        <option value="ISO-8859-5" <?php if (get_option('ec_option_paypal_charset') == 'ISO-8859-5') echo ' selected'; ?>>ISO-8859-5</option>
                        <option value="ISO-8859-6" <?php if (get_option('ec_option_paypal_charset') == 'ISO-8859-6') echo ' selected'; ?>>ISO-8859-6</option>
                        <option value="ISO-8859-7" <?php if (get_option('ec_option_paypal_charset') == 'ISO-8859-7') echo ' selected'; ?>>ISO-8859-7</option>
                        <option value="ISO-8859-8" <?php if (get_option('ec_option_paypal_charset') == 'ISO-8859-8') echo ' selected'; ?>>ISO-8859-8</option>
                        <option value="ISO-8859-9" <?php if (get_option('ec_option_paypal_charset') == 'ISO-8859-9') echo ' selected'; ?>>ISO-8859-9</option>
                        <option value="ISO-8859-13" <?php if (get_option('ec_option_paypal_charset') == 'ISO-8859-13') echo ' selected'; ?>>ISO-8859-13</option>
                        <option value="ISO-8859-15" <?php if (get_option('ec_option_paypal_charset') == 'ISO-8859-15') echo ' selected'; ?>>ISO-8859-15</option>
                        <option value="KOI8-R" <?php if (get_option('ec_option_paypal_charset') == 'KOI8-R') echo ' selected'; ?>>KOI8-R (Cyrillic)</option>
                        <option value="Shift_JIS" <?php if (get_option('ec_option_paypal_charset') == 'Shift_JIS') echo ' selected'; ?>>Shift_JIS</option>
                        <option value="UTF-7" <?php if (get_option('ec_option_paypal_charset') == 'UTF-7') echo ' selected'; ?>>UTF-7</option>
                        <option value="UTF-8" <?php if (get_option('ec_option_paypal_charset') == 'UTF-8') echo ' selected'; ?>>UTF-8</option>
                        <option value="UTF-16" <?php if (get_option('ec_option_paypal_charset') == 'UTF-16') echo ' selected'; ?>>UTF-16</option>
                        <option value="UTF-16BE" <?php if (get_option('ec_option_paypal_charset') == 'UTF-16BE') echo ' selected'; ?>>UTF-16BE</option>
                        <option value="UTF-16LE" <?php if (get_option('ec_option_paypal_charset') == 'UTF-16LE') echo ' selected'; ?>>UTF-16LE</option>
                        <option value="UTF16_PlatformEndian" <?php if (get_option('ec_option_paypal_charset') == 'UTF16_PlatformEndian') echo ' selected'; ?>>UTF16_PlatformEndian</option>
                        <option value="UTF16_OppositeEndian" <?php if (get_option('ec_option_paypal_charset') == 'UTF16_OppositeEndian') echo ' selected'; ?>>UTF16_OppositeEndian</option>
                        <option value="UTF-32" <?php if (get_option('ec_option_paypal_charset') == 'UTF-32') echo ' selected'; ?>>UTF-32</option>
                        <option value="UTF-32BE" <?php if (get_option('ec_option_paypal_charset') == 'UTF-32BE') echo ' selected'; ?>>UTF-32BE</option>
                        <option value="UTF-32LE" <?php if (get_option('ec_option_paypal_charset') == 'UTF-32LE') echo ' selected'; ?>>UTF-32LE</option>
                        <option value="UTF32_PlatformEndian" <?php if (get_option('ec_option_paypal_charset') == 'UTF32_PlatformEndian') echo ' selected'; ?>>UTF32_PlatformEndian</option>
                        <option value="UTF32_OppositeEndian" <?php if (get_option('ec_option_paypal_charset') == 'UTF32_OppositeEndian') echo ' selected'; ?>>UTF32_OppositeEndian</option>
                        <option value="US-ASCII" <?php if (get_option('ec_option_paypal_charset') == 'US-ASCII') echo ' selected'; ?>>US-ASCII</option>
                        <option value="windows-1250" <?php if (get_option('ec_option_paypal_charset') == 'windows-1250') echo ' selected'; ?>>windows-1250</option>
                        <option value="windows-1251" <?php if (get_option('ec_option_paypal_charset') == 'windows-1251') echo ' selected'; ?>>windows-1251</option>
                        <option value="windows-1252" <?php if (get_option('ec_option_paypal_charset') == 'windows-1252') echo ' selected'; ?>>windows-1252</option>
                        <option value="windows-1253" <?php if (get_option('ec_option_paypal_charset') == 'windows-1253') echo ' selected'; ?>>windows-1253</option>
                        <option value="windows-1254" <?php if (get_option('ec_option_paypal_charset') == 'windows-1254') echo ' selected'; ?>>windows-1254</option>
                        <option value="windows-1255" <?php if (get_option('ec_option_paypal_charset') == 'windows-1255') echo ' selected'; ?>>windows-1255</option>
                        <option value="windows-1256" <?php if (get_option('ec_option_paypal_charset') == 'windows-1256') echo ' selected'; ?>>windows-1256</option>
                        <option value="windows-1257" <?php if (get_option('ec_option_paypal_charset') == 'windows-1257') echo ' selected'; ?>>windows-1257</option>
                        <option value="windows-1258" <?php if (get_option('ec_option_paypal_charset') == 'windows-1258') echo ' selected'; ?>>windows-1258</option>
                        <option value="windows-874" <?php if (get_option('ec_option_paypal_charset') == 'windows-874') echo ' selected'; ?>>windows-874 (Thai)</option>
                        <option value="windows-949" <?php if (get_option('ec_option_paypal_charset') == 'windows-949') echo ' selected'; ?>>windows-949 (Korean)</option>
                        <option value="x-mac-greek" <?php if (get_option('ec_option_paypal_charset') == 'x-mac-greek') echo ' selected'; ?>>x-mac-greek</option>
                        <option value="x-mac-turkish" <?php if (get_option('ec_option_paypal_charset') == 'x-mac-turkish') echo ' selected'; ?>>x-mac-turkish</option>
                        <option value="x-mac-centraleurroman" <?php if (get_option('ec_option_paypal_charset') == 'x-mac-centraleurroman') echo ' selected'; ?>>x-mac-centraleurroman</option>
                        <option value="x-mac-cyrillic" <?php if (get_option('ec_option_paypal_charset') == 'x-mac-cyrillic') echo ' selected'; ?>>x-mac-cyrillic</option>
                        <option value="ebcdic-cp-us" <?php if (get_option('ec_option_paypal_charset') == 'ebcdic-cp-us') echo ' selected'; ?>>ebcdic-cp-us</option>
                        <option value="ibm-1047" <?php if (get_option('ec_option_paypal_charset') == 'ibm-1047') echo ' selected'; ?>>ibm-1047</option>
                    </select>
                </div>
                <div><?php _e( 'Weight Unit', 'wp-easycart' ); ?>
                    <select name="ec_option_paypal_weight_unit" id="ec_option_paypal_weight_unit">
                        <option value="lbs" <?php if (get_option('ec_option_paypal_weight_unit') == 'lbs') echo ' selected'; ?>>LBS</option>
                        <option value="kgs" <?php if (get_option('ec_option_paypal_weight_unit') == 'kgs') echo ' selected'; ?>>KGS</option>
                    </select>
                </div>
                <input type="hidden" name="ec_option_paypal_collect_shipping" id="ec_option_paypal_collect_shipping" value="<?php echo get_option( 'ec_option_paypal_collect_shipping' ); ?>" />
                <div><?php _e( 'Advertise PayPal Credit', 'wp-easycart' ); ?>
                    <select name="ec_option_paypal_enable_credit" id="ec_option_paypal_enable_credit">
                        <option value="1" <?php if (get_option('ec_option_paypal_enable_credit') == 1) echo ' selected'; ?>><?php _e( 'Yes', 'wp-easycart' ); ?></option>
                        <option value="0" <?php if (get_option('ec_option_paypal_enable_credit') == 0) echo ' selected'; ?>><?php _e( 'No', 'wp-easycart' ); ?></option>
                    </select>
                </div>
                <div><?php _e( 'Button Color', 'wp-easycart' ); ?>
                    <select name="ec_option_paypal_button_color" id="ec_option_paypal_button_color">
                        <option value="gold" <?php if( get_option('ec_option_paypal_button_color' ) == 'gold' ) echo ' selected'; ?>><?php _e( 'Gold (Recommended)', 'wp-easycart' ); ?></option>
                        <option value="blue" <?php if( get_option('ec_option_paypal_button_color' ) == 'blue' ) echo ' selected'; ?>><?php _e( 'Blue', 'wp-easycart' ); ?></option>
                        <option value="silver" <?php if( get_option('ec_option_paypal_button_color' ) == 'silver' ) echo ' selected'; ?>><?php _e( 'Silver', 'wp-easycart' ); ?></option>
                        <option value="black" <?php if( get_option('ec_option_paypal_button_color' ) == 'black' ) echo ' selected'; ?>><?php _e( 'Black', 'wp-easycart' ); ?></option>
                    </select>
                </div>
                <div><?php _e( 'Button Shape', 'wp-easycart' ); ?>
                    <select name="ec_option_paypal_button_shape" id="ec_option_paypal_button_shape">
                        <option value="pill" <?php if( get_option('ec_option_paypal_button_shape' ) == 'pill' ) echo ' selected'; ?>><?php _e( 'Pill (Recommended)', 'wp-easycart' ); ?></option>
                        <option value="rect" <?php if( get_option('ec_option_paypal_button_shape' ) == 'rect' ) echo ' selected'; ?>><?php _e( 'Rectangular', 'wp-easycart' ); ?></option>
                    </select>
                </div>
                
                <div class="ec_admin_settings_input">
                    <input type="submit" class="ec_admin_settings_simple_button" onclick="return ec_admin_save_paypal_options( );" value="<?php _e( 'Save Options', 'wp-easycart' ); ?>" />
                </div>
            </div>
        </div>
    </div>
</div>