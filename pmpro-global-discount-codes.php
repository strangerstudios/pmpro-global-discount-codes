<?php
/*
Plugin Name: PMPro Global Discount Codes
Plugin URI: http://www.paidmembershipspro.com/pmpro-global-discount-codes/
Description: Adds the ability to create discount codes with a global % or $ off of all membership levels.
Version: .1
Author: Stranger Studios
Author URI: http://www.paidmembershipspro.com

Note: This plugin requires version 1.4.6 of Paid Memberships Pro or higher.
*/

//show the global settings on the discount code page
function pmprogdc_pmpro_discount_code_after_settings()
{
	if(!empty($_REQUEST['edit']))
		$edit = $_REQUEST['edit'];
	$pmprogdc_settings = get_option("pmprogdc_settings");
		
	if(!empty($pmprogdc_settings[$edit]['percent_off']))
		$pmprogdc_percent_off = $pmprogdc_settings[$edit]['percent_off'];
	else
		$pmprogdc_percent_off = 0;
?>
<h3>Set Global Discounts</h3>
<p>These discounts are applied across all membership levels. If you set different prices for the levels further below, those prices will also be affected by these discounts.</p>
<table class="form-table">
	<tbody>
		<tr>
			<th scope="row" valign="top"><label for="pmprogdc_percent_off">Initial Price Discount:</label></th>
			<td>
				<input name="pmprogdc_percent_off" type="text" size="10" value="<?php echo esc_attr($pmprogdc_percent_off); ?>" />
				% Off
			</td>
		</tr>
							
	</tbody>
</table>
<?php
}
add_action("pmpro_discount_code_after_settings", "pmprogdc_pmpro_discount_code_after_settings");

//save the global settings
function pmprogdc_pmpro_save_discount_code($code_id)
{	
	if(!empty($code_id))
	{
		//load global discount settings
		$pmprogdc_settings = get_option("pmprogdc_settings");
		
		//set it
		$pmprogdc_settings[$code_id] = array("percent_off" => intval($_REQUEST['pmprogdc_percent_off']));
		
		//save it
		update_option("pmprogdc_settings", $pmprogdc_settings);
	}
}
add_action("pmpro_save_discount_code", "pmprogdc_pmpro_save_discount_code");

//if there is a global discount, we don't need to check codes against levels
function pmprogdc_pmpro_check_discount_code_levels($check, $code_id)
{
	//load global discount settings
	$pmprogdc_settings = get_option("pmprogdc_settings");
	
	//in there?
	if(!empty($pmprogdc_settings[$code_id]['percent_off']))
		$pmprogdc_percent_off = $pmprogdc_settings[$code_id]['percent_off'];
	else
		$pmprogdc_percent_off = 0;
		
	if($pmprogdc_percent_off > 0)
		return false;
	else
		return $check;
}
add_filter("pmpro_check_discount_code_levels", "pmprogdc_pmpro_check_discount_code_levels", 10, 2);

//apply the discount
function pmprogdc_pmpro_discount_code_level($level, $code_id)
{
	//load global discount settings
	$pmprogdc_settings = get_option("pmprogdc_settings");
	
	//in there?
	if(!empty($pmprogdc_settings[$code_id]['percent_off']))
		$pmprogdc_percent_off = $pmprogdc_settings[$code_id]['percent_off'];
	else
		$pmprogdc_percent_off = 0;
		
	//adjust
	if($pmprogdc_percent_off > 0)
	{
		$level->initial_payment = $level->initial_payment - round(($level->initial_payment * ($pmprogdc_percent_off / 100)), 2);
	}
	
	return $level;
}
add_filter("pmpro_discount_code_level", "pmprogdc_pmpro_discount_code_level", 10, 2);