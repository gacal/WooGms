<?php
	header("Content-type: text/xml; charset=utf-8"); 
	mysql_connect(DB_HOST,DB_USER,DB_PASS);
	mysql_select_db(DB_NAME);
	mysql_query('SET NAMES utf8');
	$q = mysql_query('select 
						ID
					from 
						rpl_posts as urunler
					where 
						post_status=\'publish\' and
						post_type = \'product\'
					');
	
	require_once "class.api.php";
	$consumer_key = WOO_CONS_KEY; // Add your own Consumer Key here
	$consumer_secret = WOO_CONS_SEC; // Add your own Consumer Secret here

	$store_url = SITE_URL; // Add the home URL to the store you want to connect to here
	$wc_api = new WC_API_Client( $consumer_key, $consumer_secret, $store_url );
	
	echo '<?xml version="1.0"?>
	<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0">
		<channel>
			<title>Google Merchant Center</title>
			<link>http://rapalacim.com</link>
			<description>rapalacim.com Products</description>';
			while ($f = mysql_fetch_object($q)) {
				$urun = $wc_api->get_product($f->ID);
				
				if ($urun->product->in_stock) {
					$stok = 'in stock';
				} else {
					$stok = 'out of stock';
				}
				
				echo "<item>
					<title>".$urun->product->title."</title>
					<link>".$urun->product->permalink."</link>
					<description><![CDATA[".strip_tags($urun->product->description)."]]></description>
					<g:availability>".$stok."</g:availability>
					<g:image_link>".$urun->product->featured_src."</g:image_link>
					<g:price>".$urun->product->price."</g:price>
					<g:condition>new</g:condition>
					<g:google_product_category>".implode(' &gt; ',$urun->product->categories)."</g:google_product_category>
					<g:product_type>TITLE</g:product_type>
					<g:id>$f->ID</g:id>
				</item>
				";
			}
			
echo '</channel></rss>';
