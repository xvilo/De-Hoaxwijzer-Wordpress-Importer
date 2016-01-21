<?php
	/*
	Plugin Name: De Hoaxwijzer Importer
	Version: 1.0
	Description: Import laatste resulaten vanuit de hoaxwijzer.
	Author: <a href="http://sem.design">Sem Schilder</a>
	*/
	add_action( 'admin_menu', 'kruyfAdmin' );

	/**
	 * Toevoegen admin menu om vacatures te verversen
	 *
	 */
	function kruyfAdmin() {
		add_options_page( 'Feed', 'Hoaxwijzer importer', 'manage_options', 'hoax-wijzer-importer', 'kruyfAdminOptions' );
	}
	
	/**
	 * Inhoud admin menu
	 *
	 */
	function kruyfAdminOptions() {
		echo '<div class="wrap">';
		echo '<br/><h1>Hoax wijzer importer:</h1><p>';
		echo '<br/><a class="button button-primary button-large" href="/wp-admin/options-general.php?page=hoax-wijzer-importer&refreshhoax">HOAX: Haal items opnieuw op</a><br/>';
		echo '<br/><a class="button button-primary button-large" href="/wp-admin/options-general.php?page=hoax-wijzer-importer&refreshfraude">FRAUDE: Haal items opnieuw op</a><br/>';
		echo '<br/><a class="button button-primary button-large" href="/wp-admin/options-general.php?page=hoax-wijzer-importer&refreshurban">STADSLEGENDES: Haal items opnieuw op</a><br/>';
		echo '<br/><a class="button button-primary button-large" href="/wp-admin/options-general.php?page=hoax-wijzer-importer&refreshcomplot">COMPLOTTHEORIEËN: Haal items opnieuw op</a><br/>';
		echo '<br/><a class="button button-primary button-large" href="/wp-admin/options-general.php?page=hoax-wijzer-importer&refreshanders">ANDERS: Haal items opnieuw op</a><br/>';
		echo '<br/><a class="button button-primary button-large" href="/wp-admin/options-general.php?page=hoax-wijzer-importer&deletefeed">Verwijder alle items</a><br/>';
		echo '</p>';
		echo '</div>';
	}

	function getData($url){
		include('simple_html_dom.php');
		$ii=0;
		for ($x = 0; $x <= 180;) {
			$base = $url."?offset=$x";
			$html = file_get_html($base);
			foreach($html->find('div[class=announcement]') as $item){
		       foreach($item->find('a[dir=ltr]') as $titel) {
		            $titles[$ii][] = $titel->plaintext;
		       }
		       foreach($item->find('span[dir=ltr]') as $datum) {
			        $time = strtotime($datum->plaintext);
		            $dates[$ii][] = date("Y-m-d H:i:s", $time);
		       }
		       foreach($item->find('div[dir=ltr]') as $content) {
			       $content->find('div', 0)->innertext = '';
			       $article[$ii][] = $content->innertext;
		       }
		       $ii++;
			}
			$x = $x + 10;
		}
		$i=0;
		$total = array();
		foreach($titles as $value) {
		    $total[] = array("titel" => $titles[$i],"datum" => $dates[$i],"content" => $article[$i]);
		    $i++;
		}
		return $total;
	}
	
	
	if(isset($_GET["refreshhoax"])) {
		function my_admin_error_notice() {
			$class = "updated";
			$message = "HOAX: feed is opnieuw binnengehaald.";
		        echo"<div class=\"$class\"> <p>$message</p></div>"; 
		}
		add_action( 'admin_notices', 'my_admin_error_notice' ); 
		function add_posts_mag() {
			$data = getData('https://sites.google.com/site/dehoaxwijzer/hoaxes');
			foreach($data as $item){
				if(!get_post_id_by_meta_key_and_value("itemid", $item['titel'][0])) {
					$post = array(
					 'post_author' => 1,
					 'post_content' => $item['content'][0],
					 'post_name' => $item['titel'][0],
					 'post_status' => 'publish',
					 'post_title' => $item['titel'][0],
					 'post_type' => 'post',
					 'post_date' => $item['datum'][1],
					 'post_date_gmt' => $item['datum'][0],
					 'post_category' => array(3),
					 'post_excerpt' => " ",
					);
				
					//Attempt to add post
					if($ids = wp_insert_post($post)) {
						add_post_meta($ids, 'itemid', $item['titel'][0]);
					}
				}
			}
		}
		add_action( 'wp_loaded', 'add_posts_mag' ); 
	}
	
	if(isset($_GET["refreshfraude"])) {
		function my_admin_error_notice() {
			$class = "updated";
			$message = "FRAUDE: feed is opnieuw binnengehaald.";
		        echo"<div class=\"$class\"> <p>$message</p></div>"; 
		}
		add_action( 'admin_notices', 'my_admin_error_notice' ); 
		function add_posts_mag() {
			$data = getData('https://sites.google.com/site/dehoaxwijzer/fraude');
			foreach($data as $item){
				if(!get_post_id_by_meta_key_and_value("itemid", $item['titel'][0])) {
					$post = array(
					 'post_author' => 1,
					 'post_content' => $item['content'][0],
					 'post_name' => $item['titel'][0],
					 'post_status' => 'publish',
					 'post_title' => $item['titel'][0],
					 'post_type' => 'post',
					 'post_date' => $item['datum'][1],
					 'post_date_gmt' => $item['datum'][0],
					 'post_category' => array(4),
					 'post_excerpt' => " ",
					);
				
					//Attempt to add post
					if($ids = wp_insert_post($post)) {
						add_post_meta($ids, 'itemid', $item['titel'][0]);
					}
				}
			}
		}
		add_action( 'wp_loaded', 'add_posts_mag' ); 
	}
	
	if(isset($_GET["refreshurban"])) {
		function my_admin_error_notice() {
			$class = "updated";
			$message = "STADSLEGENDES: feed is opnieuw binnengehaald.";
		        echo"<div class=\"$class\"> <p>$message</p></div>"; 
		}
		add_action( 'admin_notices', 'my_admin_error_notice' ); 
		function add_posts_mag() {
			$data = getData('https://sites.google.com/site/dehoaxwijzer/urban-legends');
			foreach($data as $item){
				if(!get_post_id_by_meta_key_and_value("itemid", $item['titel'][0])) {
					$post = array(
					 'post_author' => 1,
					 'post_content' => $item['content'][0],
					 'post_name' => $item['titel'][0],
					 'post_status' => 'publish',
					 'post_title' => $item['titel'][0],
					 'post_type' => 'post',
					 'post_date' => $item['datum'][1],
					 'post_date_gmt' => $item['datum'][0],
					 'post_category' => array(5),
					 'post_excerpt' => " ",
					);
				
					//Attempt to add post
					if($ids = wp_insert_post($post)) {
						add_post_meta($ids, 'itemid', $item['titel'][0]);
					}
				}
			}
		}
		add_action( 'wp_loaded', 'add_posts_mag' ); 
	}
	
	if(isset($_GET["refreshcomplot"])) {
		function my_admin_error_notice() {
			$class = "updated";
			$message = "COMPLOT: feed is opnieuw binnengehaald.";
		        echo"<div class=\"$class\"> <p>$message</p></div>"; 
		}
		add_action( 'admin_notices', 'my_admin_error_notice' ); 
		function add_posts_mag() {
			$data = getData('https://sites.google.com/site/dehoaxwijzer/complottheorieeen');
			foreach($data as $item){
				if(!get_post_id_by_meta_key_and_value("itemid", $item['titel'][0])) {
					$post = array(
					 'post_author' => 1,
					 'post_content' => $item['content'][0],
					 'post_name' => $item['titel'][0],
					 'post_status' => 'publish',
					 'post_title' => $item['titel'][0],
					 'post_type' => 'post',
					 'post_date' => $item['datum'][1],
					 'post_date_gmt' => $item['datum'][0],
					 'post_category' => array(6),
					 'post_excerpt' => " ",
					);
				
					//Attempt to add post
					if($ids = wp_insert_post($post)) {
						add_post_meta($ids, 'itemid', $item['titel'][0]);
					}
				}
			}
		}
		add_action( 'wp_loaded', 'add_posts_mag' ); 
	}
	
	if(isset($_GET["refreshanders"])) {
		function my_admin_error_notice() {
			$class = "updated";
			$message = "ANDERS: feed is opnieuw binnengehaald.";
		        echo"<div class=\"$class\"> <p>$message</p></div>"; 
		}
		add_action( 'admin_notices', 'my_admin_error_notice' ); 
		function add_posts_mag() {
			$data = getData('https://sites.google.com/site/dehoaxwijzer/andere-waarschuwingen');
			foreach($data as $item){
				if(!get_post_id_by_meta_key_and_value("itemid", $item['titel'][0])) {
					$post = array(
					 'post_author' => 1,
					 'post_content' => $item['content'][0],
					 'post_name' => $item['titel'][0],
					 'post_status' => 'publish',
					 'post_title' => $item['titel'][0],
					 'post_type' => 'post',
					 'post_date' => $item['datum'][1],
					 'post_date_gmt' => $item['datum'][0],
					 'post_category' => array(7),
					 'post_excerpt' => " ",
					);
				
					//Attempt to add post
					if($ids = wp_insert_post($post)) {
						add_post_meta($ids, 'itemid', $item['titel'][0]);
					}
				}
			}
		}
		add_action( 'wp_loaded', 'add_posts_mag' ); 
	}
	
	if(isset($_GET["deletefeed"])) {
	function my_admin_error_notice() {
		$class = "updated";
		$message = "Alles is leeg gehaald.";
	        echo"<div class=\"$class\"> <p>$message</p></div>"; 
	}
	add_action( 'admin_notices', 'my_admin_error_notice' ); 
	function del_posts_mag() {
		global $wpdb;
		$metaData = $wpdb->get_results("SELECT * FROM `".$wpdb->postmeta."` WHERE meta_key='".$wpdb->escape("itemid")."'");
	
		foreach($metaData as $meta) {
			$wpdb->get_results("DELETE FROM `".$wpdb->postmeta."` WHERE meta_key='".$wpdb->escape("itemid")."' AND meta_id='".$meta->meta_id."'");
			$wpdb->get_results("DELETE FROM `".$wpdb->posts."` WHERE ID='".$meta->post_id."'");
		}
	}
	add_action( 'wp_loaded', 'del_posts_mag' ); 
}

	
	function get_post_id_by_meta_key_and_value($key, $value, $bool = true) {
		global $wpdb;
		$meta = $wpdb->get_results("SELECT * FROM `".$wpdb->postmeta."` WHERE meta_key='".$wpdb->escape($key)."' AND meta_value='".$wpdb->escape($value)."'");
	
		if (is_array($meta) && !empty($meta) && isset($meta[0])) {
			$meta = $meta[0];
		}		
		
		if(is_object($meta)){ 
			$page = $wpdb->get_results("SELECT ID FROM `".$wpdb->posts."` WHERE ID='".$wpdb->escape($meta->post_id)."'");
			
			if(!empty($page[0]->ID)) {
				if($bool) {
					return true;
				}
				else {
					return $page[0]->ID;
				}
			}
		}
		else {
			if(!$bool) {
				return false;
			}
			else {
				return "";
			}
		}
	}
?>