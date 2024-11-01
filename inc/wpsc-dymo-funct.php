<?php
/*admin messages*/
if ( !function_exists( 'showWPSCMessage' ) ) {
  function showWPSCMessage($message, $errormsg = false)
  {
	if ($errormsg) { 
	  echo '<div id="message" class="error">';
	} else {
	  echo '<div id="message" class="updated fade">';
	}
	echo "<p><strong>$message</strong></p></div>";
  } 
}   
function showWPSCAdminMessages() {showWPSCMessage(__( 'WP E-Commerce is not active. Please activate plugin before using WP E-Commerce DYMO Print plugin.', 'wpsc-dymo'), true);}

/**
* Plugin Settings Menu & Page
*/
function wpsc_dymo_admin_menu() {
	$page=add_options_page(__( 'DYMO Print', 'wpsc-dymo' ),__( 'DYMO Print', 'wpsc-dymo' ), 'manage_options', 'wpsc_dymo', 'wpsc_dymo_page');
}

/**
* Add links to order detail page
*/
function wpsc_dymo_action() {
	$output = '
	<img src="' . plugins_url( '/img/icon-print-shipping.png', dirname(__FILE__) ).'" alt="printer icon" width=16 />&ensp;<a class="wpsc-dymo-link" href="'.wp_nonce_url(admin_url('?wpsc_print_dymo=true&purch='.$_GET['id'].'&type=print_shipping_label'), 'print-dymo'). '">' . __( "Print Shipping label", "wpsc-dymo" ) . '</a><br /><br class="small" />
	';
	echo $output;
}
add_action( 'wpsc_purchlogitem_links_start', 'wpsc_dymo_action' );

/***********************************
* Add column to sales page
* Thanks to: http://haet.at/add-column-wp-ecommerce-purchase-logs/
***********************************/
function addPurchaseLogColumnHead( $columns ){
$columns['wpscdymoprint']=__('DYMO Print','wpsc-dymo');
return $columns;
}
add_filter( 'manage_dashboard_page_wpsc-purchase-logs_columns', 'addPurchaseLogColumnHead');
 
/***********************************
*add content to the new column
***********************************/
function addPurchaseLogColumnContent( $default, $column_name, $item ){
    if($column_name=='wpscdymoprint'){
        echo '<img src="' . plugins_url( '/img/icon-print-shipping.png', dirname(__FILE__) ).'" alt="printer icon" width=16 />&ensp;<a class="wpsc-dymo-link" href="'.wp_nonce_url(admin_url('?wpsc_print_dymo=true&purch='.$item->id.'&type=print_shipping_label'), 'print-dymo'). '">' . __( "Print Shipping label", "wpsc-dymo" ) . '</a><br /><br class="small" />';
    }
}
add_filter( 'wpsc_manage_purchase_logs_custom_column', 'addPurchaseLogColumnContent',10,3 );


/**
* DYMO print screen
*/
function wpsc_dymo_window() {
	global $purchlogitem;
	if (isset($_GET['wpsc_print_dymo'])) {
		$nonce = $_REQUEST['_wpnonce'];
  		if (!wp_verify_nonce($nonce, 'print-dymo') || !is_user_logged_in() ) die('You are not allowed to view this page.');
		$purchlogitem="";
		$purchlogitem= ob_get_clean();
		$mypost=ob_get_clean();
		$mypost='';
		$mypost=$_GET['purch'];
    	$orders = explode(',', $mypost);
		$action = $_GET['type'];
/* here print flow*/
?>
<!DOCTYPE HTML>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title><?php _e('Print DYMO labels', 'wpsc-dymo'); ?></title>
	<link href="<?php echo plugins_url( '/css/wpsc-dymo.css', dirname(__FILE__));?>"  rel="stylesheet" type="text/css" media="screen,print" />
	<script charset="UTF-8" type="text/javascript" src="<?php echo plugins_url( '/js/wpsc-dymo-print.js', dirname(__FILE__));?>"> </script>
</head>
<body>
<script type="text/javascript">template = '<? echo '<?xml version="1.0" encoding="utf-8"?>'; ?>' + '<?php echo preg_replace('/\s\s+/', '\' + \'', get_option('wpsc_dymo_label','')); ?>';</script>
	<?php if ($action == 'print_billing_label') { $actie=__('Billing Label', 'wpsc-dymo'); } else { $actie=__('Shipping Label', 'wpsc-dymo'); } echo '<h1>'.sprintf( __('Print DYMO %s' , 'wpsc-dymo') , $actie ).'</h1>'; 
	$content = ob_get_clean();
	$i=0;
	foreach ($orders as $purchlogitem) {
		$purchlogitem = new wpsc_purchaselogs_items($purchlogitem);

  		  // Read the file
  		  //ob_start();?>
	
		  <div class=printing id=id_<?php echo $i;?>><p><strong><?php _e('Printing label for order:', 'wpsc-dymo');?>  <?php echo $purchlogitem->purchlogid; ?></strong></p><p>
			<?php 
			if ($action == 'print_billing_label') { 
				#echo $purchlogitem->get_formatted_billing_address(); 

				$address= wpsc_display_purchlog_buyers_name() . '|';
				$address.= ( wpsc_display_purchlog_buyers_address() != ""            ) ? wpsc_display_purchlog_buyers_address() . "|"            : '' ; 
				$address.= ( wpsc_display_purchlog_buyers_state_and_postcode() != "" ) ? wpsc_display_purchlog_buyers_state_and_postcode() . " " : '' ; 
				$address.= ( wpsc_display_purchlog_buyers_city() != ""               ) ? wpsc_display_purchlog_buyers_city() . " "               : '' ; 
				if($purchlogitem->extrainfo->billing_country!=get_option( 'base_country' )) { $address.= "|".wpsc_display_purchlog_buyers_country();}
				
				echo '<strong>'. wpsc_display_purchlog_buyers_name() . '</strong><br>';
				echo ( wpsc_display_purchlog_buyers_address() != ""            ) ? wpsc_display_purchlog_buyers_address() . "<br>"            : '' ; 
				echo ( wpsc_display_purchlog_buyers_state_and_postcode() != "" ) ? wpsc_display_purchlog_buyers_state_and_postcode() . " " : '' ; 
				echo ( wpsc_display_purchlog_buyers_city() != ""               ) ? wpsc_display_purchlog_buyers_city() . "<br>"               : '' ; 
				if($purchlogitem->extrainfo->billing_country!=get_option( 'base_country' )) { echo wpsc_display_purchlog_buyers_country();}
			} else { 
				$address= wpsc_display_purchlog_shipping_name() . '|';
				$address.= ( wpsc_display_purchlog_shipping_address() != ""            ) ? wpsc_display_purchlog_shipping_address() . "|"            : '' ; 
				$address.= ( wpsc_display_purchlog_shipping_state_and_postcode() != "" ) ? wpsc_display_purchlog_shipping_state_and_postcode() . " " : '' ; 
				$address.= ( wpsc_display_purchlog_shipping_city() != ""               ) ? wpsc_display_purchlog_shipping_city() . " "               : '' ; 
				if($purchlogitem->shippinginfo['shippingcountry']['value']!=get_option( 'base_country' )) { $address.= "|".wpsc_display_purchlog_shipping_country();}
				
				echo '<strong>'. wpsc_display_purchlog_shipping_name() . '</strong><br>';
				echo ( wpsc_display_purchlog_shipping_address() != ""            ) ? wpsc_display_purchlog_shipping_address() . "<br>"            : '' ; 
				echo ( wpsc_display_purchlog_shipping_state_and_postcode() != "" ) ? wpsc_display_purchlog_shipping_state_and_postcode() . " " : '' ; 
				echo ( wpsc_display_purchlog_shipping_city() != ""               ) ? wpsc_display_purchlog_shipping_city() . "<br>"               : '' ; 
				if($purchlogitem->shippinginfo['shippingcountry']['value']!=get_option( 'base_country' )) { echo wpsc_display_purchlog_shipping_country();}
			}
			?></p></div>
	<script type="text/javascript">
	var z=1;
	var k=0;
	var adres='';
	var printers = dymo.label.framework.getPrinters();
	var printParams = {};
	if (printers.length != 0) {
        var label = dymo.label.framework.openLabelXml(template);
		var printer=printers[0];
		if (typeof printer.isTwinTurbo != "undefined")
    {
        if (printer.isTwinTurbo) { 
		<?php if(get_option('wpsc_dymo_twin_roll')=='left') { ?>
		printParams.twinTurboRoll = dymo.label.framework.TwinTurboRoll.Left; // or Left or Right 
		<?php } elseif(get_option('wpsc_dymo_twin_roll')=='right') {?>
		printParams.twinTurboRoll = dymo.label.framework.TwinTurboRoll.Right;
		<?php } else { ?>
		printParams.twinTurboRoll = dymo.label.framework.TwinTurboRoll.Auto; // or Left or Right 
		<?php }?>
		} 
    }
		
		<?php if ($action == 'print_billing_label') { 
			if($address!="") { $address=htmlspecialchars(preg_replace('/<br(\s+)?\/?>/i', "", $address), ENT_QUOTES); $address = preg_replace("/[\n\r]/","|",$address); }
		} else { 
  		    if($address!="") { $address=htmlspecialchars(preg_replace('/<br(\s+)?\/?>/i', "", $address), ENT_QUOTES); $address = preg_replace("/[\n\r]/","|",$address); }
		} ?>
		var adres_in='<?php echo $address;?>';
		var adres= adres_in.replace(/\|/g, "\n");
		<?php if(get_option('wpsc_dymo_company_name')!="") { ?> label.setObjectText("COMPANY", "<?php echo get_option('wpsc_dymo_company_name'); ?>"); <?php } ?>
		<?php if(get_option('wpsc_dymo_company_extra')!="") { ?> label.setObjectText("EXTRA", "<?php echo get_option('wpsc_dymo_company_extra'); ?>");<?php }?>
		<?php if($address!="") { ?>label.setObjectText("ADDRESS", adres);<?php } ?>
		label.print(printer.name, dymo.label.framework.createLabelWriterPrintParamsXml(printParams));
		}
	</script>
		  <?php
  		  $content .= ob_get_clean();
		  $i++;
      }
		 ?>
		 </body>
</html><?php
  		echo $content;
  		exit;
    }
}

/**
* Add bulk actions (with jQuery to bulk actions list)
*/
function wpsc_dymo_bulk_admin_footer() {
  if ( 'wpsc-purchase-logs' == $_GET['page'] ) {
?>
  <script type="text/javascript">
	jQuery(document).ready(function() {
	  jQuery('<option>').val('print_shipping_label').text('<?php _e( 'Print Shipping Label', 'wpsc-dymo' )?>').appendTo("select[name='action']");
	  jQuery('<option>').val('print_shipping_label').text('<?php _e( 'Print Shipping Label', 'wpsc-dymo' )?>').appendTo("select[name='action2']");
	});
  </script>
<?php
  }
	
  if($_GET['print']==true) { ?><script type="text/javascript">
	var popurls=new Array()
	<?php $forward = wp_nonce_url(admin_url(), 'print-dymo');
		$forward = add_query_arg(array('print_dymo' => 'true', 'post' => $_GET['post'], 'type' => $_GET['actie']), $forward);
	?>
	popurls[0]="<?php echo $forward;?>"

	function openpopup(popurl){
		var winpops=window.open(popurl,"","width=400,height=400,toolbar,location,status,scrollbars,menubar,resizable")
	}
	openpopup(popurls[Math.floor(Math.random()*(popurls.length))])
	</script>
<?php } 
}
/** 
* Register scripts
*/
function wpsc_dymo_scripts() {
	wp_register_script( 'wpsc-dymo-js', plugins_url( '/js/wpsc-dymo.js', dirname(__FILE__) ) );
	wp_enqueue_script( 'wpsc-dymo-js', array('jquery') );
}
?>