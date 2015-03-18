<?php
ob_start();
/*
Plugin Name: Juiced Metrics
Plugin URI: http://envisionyourwebsite.com
Description: Setup is simple You can create report names below. Select the pages you would like to track with the sales cycle or your sales funnel. Trackable pages can be created to follow how traffic is reacting to your sales funnels. This will show you which pages are working and which need modification. This will help you know what to work on to gain more sales.. 
Author: Envision Your Website
Version: 4.1.1
Author URI: http://www.envisionyourwebsite.com
Copyright 2014 envisionyourwebsite.com  (email : tgarner@envisionyourwebsite.com)
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.*/
$JuicedMetrics_db_version = "4.1.1";
include_once('manage-dp.php');
require_once(ABSPATH . '/wp-admin/includes/plugin.php');
require_once(ABSPATH . WPINC . '/pluggable.php');
wp_enqueue_script('jquery'); 
define ( 'JM_PLUGIN_URL', plugin_dir_url(__FILE__));

if( !class_exists('JuicedMetrics') ):
	class JuicedMetrics{
		function JuicedMetrics() { //constructor
			//ACTIONS
				#Add Graph Panel
				add_action( 'admin_menu', array($this, 'AddPanel') );
				add_action('wp_ajax_ak_attach', array($this,  'ajaxResponse'));	
				#Save Default Settings
				add_action( 'init', array($this, 'DefaultSettings') );
				if( $_POST['action'] == 'socialstats_update' )
					add_action( 'init', array($this,'SaveSettings') );
				if( $_POST['action'] == 'add_competitor_site' )
				add_action( 'init', array($this,'Addcompetitor') );
				if( $_POST['action'] == 'edit_competitor_site' )
				add_action( 'init', array($this,'Editcompetitor') );
				if( $_POST['action'] == 'import_competitor' )
				add_action( 'init', array($this,'Importcompetitor') );
				if( $_POST['action'] == 'sendmail_competitor' )
				add_action( 'init', array($this,'SendMailcompetitor') );
				register_setting( 'wsl-settings-group'  , 'wsl_licensekey' ); 
				register_setting( 'wsl-settings-group'  , 'localkeydata' ); 
				register_activation_hook(__FILE__,array($this,'JuicedMetrics_install'));
				Register_uninstall_hook(__FILE__,array($this,'JuicedMetrics_drop'));
		}
		function AddPanel()
		{
				global $manageDP;
				add_menu_page( __("Sales Cycle",'JuicedMetrics'), 'Juiced Metrics', 10, 'JuicedMetrics', array($manageDP, 'Manage'), plugins_url("assets/images/fb-ads.png", __FILE__) );
				add_submenu_page( 'JuicedMetrics', 'Setup', 'Setup', 10, 'JuicedMetrics', array($manageDP, 'Manage') );
				add_submenu_page( 'JuicedMetrics', 'Graph', 'Sales Funnel', 10, 'JuicedMetricsGraph', array($this, 'Graph') );
				add_submenu_page( 'JuicedMetrics', 'Competitors', 'Competitors', 10, 'Competitors', array($this, 'SCSocialstats') );
				add_submenu_page( 'JuicedMetrics', 'Competitors Settings', 'Competitors Settings', 10, 'CompetitorsSettings', array($this, 'Socialstats') );
		}
		function ajaxResponse()
		{
			global $wpdb; 
			$status=intval($_POST['status']);
			$id=intval($_POST['id']);
			$tb = $wpdb->prefix.'social_sites_config';
			$wpdb->query("UPDATE $tb SET  highlight_status='".$status."' WHERE ID='".$id."'" );
			$tb = $wpdb->prefix.'social_sites';
			$wpdb->query("UPDATE $tb SET  highlight_status='".$status."' WHERE ref_id='".$id."'" );
			echo 'Updated Successfully';exit;
			/*if(isset($_POST['sorder']))
			{
				$sorder=explode(',',urldecode($_POST['sorder']));
				$tb = $wpdb->prefix.'sales_report_page';
				$i=1;
				foreach($sorder as $items)
				{
						$wpdb->query("UPDATE $tb SET  page_order='".$i."' WHERE ID='".$items."' AND ref_id='".$_GET['refid']."'" );
						$i++;
				}
			}*/
			exit;
		}
		function PageSelect($sel){
			global $wpdb;
			$pages = $wpdb->get_results("SELECT ID, post_status, post_title FROM $wpdb->posts WHERE post_type='page' ORDER BY post_status DESC, menu_order ASC, post_title ASC");
			$output .= '<option value="sidebar"';
			if( $sel == 'sidebar' ) $output .= ' selected="selected"';
			$output .= '>SideBar</option>'."\n"; 
			foreach( $pages as $page ):
				if( $page->post_status == 'draft') $draft = '{draft} '; else $draft = '';
				$output .= '<option value="'.$page->ID.'"';
				if( $sel == $page->ID ) $output .= ' selected="selected"';
				$output .= '>'.$draft.$page->post_title.'</option>'."\n";
			endforeach;
			return $output;
		}
		function Graph(){
		
			
			wp_register_script('jqueryd3',plugins_url('assets/javascripts/d3.js', __FILE__),false,'1.0',true);
			 wp_enqueue_script( 'jqueryd3' );
			 wp_register_script('jqueryd3funnelcharts',plugins_url('assets/javascripts/d3-funnel-charts.js', __FILE__),false,'1.0',true);
			 wp_enqueue_script( 'jqueryd3funnelcharts' );
			 
			/*$JuicedMetrics = get_option( 'JuicedMetrics' );
			if( $_POST['notice'] )
				echo '<div id="message" class="updated fade"><p><strong>' . $_POST['notice'] . '</strong></p></div>';
				*/
			global $wpdb;
			$tb = $wpdb->prefix.'sales_report';
			?>
<div class="wrap">
 <?php
if(isset($_GET['graph']))
{
$reports = $wpdb->get_results("SELECT * FROM $tb WHERE ID='".$_GET['graph']."' ORDER BY ID ASC");
$tb1 = $wpdb->prefix.'sales_report_page';
$subreports = $wpdb->get_results("SELECT * FROM $tb1 WHERE ref_id='".$reports[0]->ID."' ORDER BY page_order ASC");
				$subarray=array();
				$uniquevisit=array();
				$runiquevisit=array();
				$tb1 = $wpdb->prefix.'simpleviews';
				$i=1;
				foreach($subreports as $subdata)
				{
				$subreports1 = $wpdb->get_results("SELECT * FROM $tb1 WHERE post_id='".$subdata->page_id."'  AND ref_id='".$reports[0]->ID."'  ORDER BY view DESC");

				$subreports2 = $wpdb->get_results("SELECT * FROM $tb1 WHERE post_id='".$subdata->page_id."' AND ref_id='".$reports[0]->ID."'  AND return_visit > 0 ORDER BY view DESC");

				$uniquevisit[]=count($subreports1);

				$runiquevisit[]=count($subreports2);

				$subarray[$subdata->page_id]=array('title'=>get_the_title($subdata->page_id),'uniquevist'=>count($subreports1),'returnvist'=>count($subreports2),'page_id'=>$subdata->page_id);

/*		if($i == 1 || $i == 2)
		{
			$testview=$i*10;
		}
		else
		{
			$testview=$i*2;
		}

			$subarray[$subdata->page_id]=array('title'=>get_the_title($subdata->page_id),'uniquevist'=>$testview,'returnvist'=>count($subreports2),'page_id'=>$subdata->page_id);
			 */
				 $i++;
				}

			function invenDescSort($item1,$item2)
			{
			    if ($item1['uniquevist'] == $item2['uniquevist']) return 0;
			    return ($item1['uniquevist'] < $item2['uniquevist']) ? 1 : -1;
			}
			usort($subarray,'invenDescSort');
				require('report-details.php');
			}
			else
			{
				$reports = $wpdb->get_results("SELECT * FROM $tb ORDER BY ID ASC");
				$newarray=array();

				function invenDescSort($item1,$item2)
				{
					if ($item1['uniquevist'] == $item2['uniquevist']) return 0;
					return ($item1['uniquevist'] < $item2['uniquevist']) ? 1 : -1;
				}
				foreach($reports as $ritems)
				{
				$tb = $wpdb->prefix.'sales_report_page';
				$tb1 = $wpdb->prefix.'simpleviews';
				$subreports = $wpdb->get_results("SELECT * FROM $tb WHERE ref_id='".$ritems->ID."' ORDER BY page_order ASC");
				$subarray=array();
				$uniquevisit=array();
				foreach($subreports as $subdata)
				{
				$subreports1 = $wpdb->get_results("SELECT * FROM $tb1 WHERE post_id='".$subdata->page_id."' AND ref_id='".$ritems->ID."' ORDER BY view_datetime ASC");

				$subreports2 = $wpdb->get_results("SELECT * FROM $tb1 WHERE post_id='".$subdata->page_id."' AND return_visit > 0 AND ref_id='".$ritems->ID."' ORDER BY view_datetime ASC");

				$uniquevisit[]=count($subreports1);

				 $subarray[$subdata->page_id]=array('title'=>get_the_title($subdata->page_id),'uniquevist'=>count($subreports1),'page_id'=>$subdata->page_id,'returnvist'=>count($subreports2));

				}

			usort($subarray,'invenDescSort');

				if(count($uniquevisit) > 0)
				{
					$visit=array_sum($uniquevisit);
				}
				else
				{
					$visit=0;
				}
			if(count($subarray) > 0)
			{
			$newarray[]=array(
			'id'=>$ritems->ID,
			'title'=>$ritems->report_name,
			'Funnel'=>count($subarray),
			'pageinfo'=>$subarray,
			'Visits'=>$visit,
			'Conversion'=>'83%'
		);
			}			

			}
			$socialtb = $wpdb->prefix.'social_stats';
			$alexaresult = $wpdb->get_results("SELECT * FROM $socialtb  GROUP BY DATE(date_time) DESC");
	require('report-list.php');
}?>

</div>
            <?php
		}

		  function Socialstatsbk(){
		  $sstats = get_option( 'Socialstats' );
		 if( $_POST['notice'] )
				echo '<div id="message" class="updated fade"><p><strong>' . $_POST['notice'] . '</strong></p></div>';
		 ?>
		 <div class="wrap">
		<h2>Sales Cycle Social Status Settings</h2>

		<form action="" method="post">
		<table class="form-table">
		<tbody><!--<tr>

		<th scope="row"><label for="blogname">Feed Burner Feed Name</label></th>
		<td><input type="text" class="regular-text" value="<?php //echo (isset($sstats['FEEDBURNER_FEED_NAME'])) ? $sstats['FEEDBURNER_FEED_NAME']:'';?>" id="feed_burne_namer" name="feed_burne_namer"></td>
		</tr>-->
		<tr>
		<th scope="row"><label for="blogname">Twitter id</label></th>

		<td><input type="text" class="regular-text" value="<?php echo (isset($sstats['TWITTER_ID'])) ? $sstats['TWITTER_ID']:'';?>" id="twitter_id" name="twitter_id"></td>

		</tr>

		<tr>
		<th scope="row"><label for="blogname">Facebook id</label></th>

		<td><input type="text" class="regular-text" value="<?php echo (isset($sstats['FACEBOOK_PAGE_ID'])) ? $sstats['FACEBOOK_PAGE_ID']:'';?>" id="facebook_id" name="facebook_id"></td>
		</tr>

		<tr>
		<th scope="row"><label for="blogname">Domain Name <em>(For alexa.com)</em></label></th>

		<td><input type="text" class="regular-text" value="<?php echo (isset($sstats['DOMAIN_NAME'])) ? $sstats['DOMAIN_NAME']:'';?>" id="domain_name" name="domain_name"></td>

		</tr>

		</tbody></table>

		<p class="submit"><input type="submit" value="Save Changes" class="button button-primary" id="submit" name="submit">
		<input name="action" value="socialstats_update" type="hidden" />
		</p></form>

		</div>

		 <?php

		  }
		   function Socialstats()
		   {

			global $wpdb;

		  	$socialtb = $wpdb->prefix.'social_sites_config';
			$addsql='';
			if(isset($_GET['flag']))
			{	
				if(trim($_GET['flag']) == 'edit')
				{
					$addsql=" WHERE ID='".$_GET['id']."'";
				}
			}
			$alexaresult = $wpdb->get_results("SELECT * FROM $socialtb $addsql ORDER BY ID ASC");
		 if( $_POST['notice'] )
				echo '<div id="message" class="updated fade"><p><strong>' . $_POST['notice'] . '</strong></p></div>';

			 ?>
            <div class="wrap">
            <?php

            if(isset($_GET['flag']))
            {	
                if(trim($_GET['flag']) == 'add')
                {
                    ?>
                       <h2><img src="<?php echo plugins_url("assets/images/Juiced-Metrics-Green.png", __FILE__);?>" /></h2>
         <hr />
                <h2>Add Competitor Site</h2>
            <form action="" method="post">
            <table class="form-table">
            <tbody>
            <tr>
            <th scope="row"><label for="blogname">Website Name </label></th>
            <td><input type="text" class="regular-text" value="" id="domain_name" name="domain_name"><em>With out http://www.</em></td>
            </tr>
           <!-- <tr>
            <th scope="row"><label for="blogname">Twitter id</label></th>
            <td><input type="text" class="regular-text" value="" id="twitter_id" name="twitter_id"><em>With out https://twitter.com/</em></td>
            </tr>-->
            <tr>
            <th scope="row"><label for="blogname">Facebook id</label></th>
            <td><input type="text" class="regular-text" value="" id="facebook_id" name="facebook_id"><em>With out https://www.facebook.com/</em></td>
            </tr>
           <!-- <tr>
            <th scope="row"><label for="blogname">FB API Access Token</label></th>
            <td><input type="text" class="regular-text" value="" id="fp_api_access_token" name="fp_api_access_token"></td>
            </tr>-->
            </tbody></table>
            <p class="submit"><input type="submit" value="Save Changes" class="button button-primary" id="submit" name="submit">

            <input name="action" value="add_competitor_site" type="hidden" />
            </p></form>

                <?php
                }

                if(trim($_GET['flag']) == 'edit')
                {
                    ?>
                       <h2><img src="<?php echo plugins_url("assets/images/Juiced-Metrics-Green.png", __FILE__);?>" /></h2>
         <hr />
                <h2>Edit Website Details</h2>
            <form action="" method="post">
            <table class="form-table">
            <tbody>
            <tr>

            <th scope="row"><label for="blogname">Website Name </label></th>
            <td><input type="text" class="regular-text" value="<?php echo $alexaresult[0]->website;?>" id="domain_name" name="domain_name"><em>With out http://www.</em></td>
            </tr>
           <!-- <tr>
            <th scope="row"><label for="blogname">Twitter id</label></th>
            <td><input type="text" class="regular-text" value="<?php //echo $alexaresult[0]->twitter_id;?>" id="twitter_id" name="twitter_id"><em>With out https://twitter.com/</em></td>
            </tr>-->
            <tr>
            <th scope="row"><label for="blogname">Facebook id</label></th>
            <td><input type="text" class="regular-text" value="<?php echo $alexaresult[0]->fb_id;?>" id="facebook_id" name="facebook_id"><em>With out https://www.facebook.com/</em></td>
            </tr>
           <!-- <tr>
            <th scope="row"><label for="blogname">FB API Access Token</label></th>
            <td><input type="text" class="regular-text" value="<?php //echo $alexaresult[0]->fp_api_access_token;?>" id="fp_api_access_token" name="fp_api_access_token"></td>
            </tr>-->

            </tbody></table>

            <p class="submit"><input type="submit" value="Save Changes" class="button button-primary" id="submit" name="submit">

            <input name="ID" value="<?php echo $alexaresult[0]->ID;?>" type="hidden" />
            <input name="action" value="edit_competitor_site" type="hidden" />
            </p></form>

                <?php
                }
				if(trim($_GET['flag']) == 'delete')
				{
					$qry="DELETE FROM `".$wpdb->prefix ."social_sites_config` WHERE `ID` ='".intval($_GET['id'])."'";

					$wpdb->query($qry);
					$qry="DELETE FROM `".$wpdb->prefix ."social_sites` WHERE `ref_id` ='".intval($_GET['id'])."'";
					$wpdb->query($qry);
					$accurl = get_option('siteurl').'/wp-admin/admin.php?page=CompetitorsSettings';
				echo '<META http-equiv="refresh" content="0;URL='.$accurl.'">';exit();
				}

                ?>
                <?php
            }
            else
            {
            $accurl = get_option('siteurl').'/wp-admin/admin.php?page=CompetitorsSettings';

                ?>
         <h2><img src="<?php echo plugins_url("assets/images/Juiced-Metrics-Green.png", __FILE__);?>" /></h2>
         <hr />
            <h2>Juiced Metrics Settings <a class="add-new-h2" href="<?php echo $accurl.'&flag=add';?>">Add New</a></h2>

			<?php
			wp_register_style( 'footable-core', plugins_url('assets/css/foo/footable.core.css', __FILE__) );
			wp_enqueue_style('footable-core');
			wp_register_style( 'footable-standalone', plugins_url('assets/css/foo/footable.standalone.css', __FILE__) );
			wp_enqueue_style('footable-standalone');
			wp_register_style( 'footable-demos', plugins_url('assets/css/foo/footable-demos.css', __FILE__) );
			wp_enqueue_style('footable-demos');
			
			wp_register_script('footablejs',plugins_url('assets/javascripts/foo/footable.js?v=2-0-1', __FILE__),false,'1.10.2',true);
			wp_enqueue_script( 'footablejs' );
			wp_register_script('footablesortjs',plugins_url('assets/javascripts/foo/footable.sort.js?v=2-0-1', __FILE__),false,'1.10.2',true);
			wp_enqueue_script( 'footablesortjs' );
			
			wp_register_script('footablefilter',plugins_url('assets/javascripts/foo/footable.filter.js?v=2-0-1', __FILE__),false,'1.10.2',true);
			wp_enqueue_script( 'footablefilter' );
	//wp_register_script('footablepaginatejs',plugins_url('assets/javascripts/foo/footable.paginate.js?v=2-0-1', __FILE__),false,'1.10.2',true);
			//wp_enqueue_script( 'footablepaginatejs' );
			?>

            <div style="clear:both;"></div>
            <div style=" margin-left: 10px; margin-top: 25px; width: 90%;">

            <table id="example" class="table table-striped table-bordered dataTable " cellspacing="0" width="100%">
                    <thead>
                    <tr>
                    <th    data-sort-ignore="true">Competitors<br />Highlight</th>

                    <th>Website</th>
                    <th data-hide="phone,tablet">Facebook Id</th>
                   <!-- <th data-hide="phone,tablet">Twitter Id</th>-->
                 <!--   <th data-hide="phone,tablet">FB API Access Token</th>-->
                    <th data-hide="phone,tablet">Action</th>
                    </tr>
                    </thead>
              <tbody>
                   <?PHP
                   if(count($alexaresult) > 0)
                   {
                        foreach($alexaresult as $alexa)

                        {
                            echo ' <tr>';
							if(intval($alexa->highlight_status) > 0)
							{	
								echo '<td  width="10%" style=" text-align:center"><input onclick="updatehighlight(\'0\',\''.$alexa->ID.'\')" checked="checked" type="checkbox" value="'.$alexa->ID.'" /></td>';
							}
							else
							{
								echo '<td  width="10%" style=" text-align:center" ><input onclick="updatehighlight(\'1\',\''.$alexa->ID.'\')" type="checkbox" /></td>';

							}
  //<td>'.$alexa->fp_api_access_token.'</td> <td>'.$alexa->twitter_id.'</td>

                            echo '<td>'.$alexa->website.'</td>
                            <td>'.$alexa->fb_id.'</td>
                             <td><a  href="'.$accurl.'&flag=edit&id='.$alexa->ID.'">Edit</a>&nbsp;<a onclick="return condelete();" href="'.$accurl.'&flag=delete&id='.$alexa->ID.'">Delete</a></td>

                        </tr>';

                        }
                   }
                   ?>
                    </tbody>
                     <tfoot>
                <tr>

                    <td colspan="6">
                        <div class="pagination pagination-centered"></div>
                    </td>
                </tr>
                </tfoot>
                </table>
            </div>

            <script type="text/javascript">
	$= jQuery.noConflict(); 
    $(function () {
        $('table').footable();

    });

			function condelete()
			{
				var con=confirm("Are you sure if you want to delete this setting.");
				return con;
			}
			function updatehighlight(sts,cid)
			{

			$s = jQuery.noConflict(); 
			$s.post("<?php echo get_option('siteurl')?>/wp-admin/admin-ajax.php", {action:"ak_attach", "status": encodeURIComponent(sts),"id": encodeURIComponent(cid)}, function(str)	{
		alert(str);
	});

			}

			</script>
                <?php
            }

            ?>
             </div> 
		 <?php 
		  }   

		  function SCSocialstats() 

		 {
		 	wp_register_style( 'footable-core', plugins_url('assets/css/foo/footable.core.css', __FILE__) );
			wp_enqueue_style('footable-core');
			wp_register_style( 'footable-standalone', plugins_url('assets/css/foo/footable.standalone.css', __FILE__) );
			wp_enqueue_style('footable-standalone');
			wp_register_style( 'footable-demos', plugins_url('assets/css/foo/footable-demos.css', __FILE__) );
			wp_enqueue_style('footable-demos');
			
			wp_register_script('jquerymin',plugins_url('assets/javascripts/jquery-1.10.2.min.js', __FILE__),false,'1.10.2',true);
			wp_enqueue_script( 'jquerymin' );
			wp_register_script('footablejs',plugins_url('assets/javascripts/foo/footable.js?v=2-0-1', __FILE__),false,'1.10.2',true);
			wp_enqueue_script( 'footablejs' );
			wp_register_script('footablesortjs',plugins_url('assets/javascripts/foo/footable.sort.js?v=2-0-1', __FILE__),false,'1.10.2',true);
			wp_enqueue_script( 'footablesortjs' );
			
			wp_register_script('jquerydataTablesjs',plugins_url('assets/javascripts/jquery.dataTables.js', __FILE__),false,'1.10.2',true);
			wp_enqueue_script( 'jquerydataTablesjs' );
	//wp_register_script('footablepaginatejs',plugins_url('assets/javascripts/foo/footable.paginate.js?v=2-0-1', __FILE__),false,'1.10.2',true);
			//wp_enqueue_script( 'footablepaginatejs' );
			
		  	global $wpdb;
			 if( $_POST['notice'] )
				echo '<div id="message" class="updated fade"><p><strong>' . $_POST['notice'] . '</strong></p></div>';
		  	 //$socialtb = $wpdb->prefix.'social_stats';
			//$alexaresult = $wpdb->get_results("SELECT * FROM $socialtb  GROUP BY DATE(date_time) DESC");
		if(isset($_GET['flag']))
		{
			if(trim($_GET['flag']) == 'import')
			{
				require('competitors-import.php');
			}
			else if(trim($_GET['flag']) == 'export')
			{
				$socialtb = $wpdb->prefix.'social_sites';
				$alexaresult = $wpdb->get_results("SELECT * FROM $socialtb ORDER BY ID ASC");
				
				require('competitors-export.php');
			}
			else if(trim($_GET['flag']) == 'sendmail')
			{
				require('competitors-sendmail.php');
			}
			else if(trim($_GET['flag']) == 'exportdata')

			{
				$exheader =array('Website','Alexa Global Rank total','Alexa US rank','Pages Viewed','Time on Site','Bounce rate','FB Likes','Twitter','Pinterest');
				$exceloutput='<table width="1%" border="1" >';
				$exceloutput.='<tr>';
				foreach($exheader as $hval)
				{
					$exceloutput.='<th width="10%" ><strong>'.$hval.'</strong></th>';
				}	
				$exceloutput.='</tr>';
				$socialtb = $wpdb->prefix.'social_sites';
				$alexaresult = $wpdb->get_results("SELECT * FROM $socialtb ORDER BY ID ASC");
				$row=array();
				if(count($alexaresult) > 0)
				{
					foreach($alexaresult as $alexa)
					{
					 $globalrank=rtrim(rtrim(number_format($alexa->alexa_global_rank, 2, ".", ""), '0'), '.');
					 $alexa_us_rank=rtrim(rtrim(number_format($alexa->alexa_us_rank, 2, ".", ""), '0'), '.');
					 $alexa_pages_viewed=rtrim(rtrim(number_format($alexa->alexa_pages_viewed, 2, ".", ""), '0'), '.');
					 $alexa_time_on_site=rtrim(rtrim(number_format($alexa->alexa_time_on_site, 2, ".", ""), '0'), '.');
					 $bounce_rate=rtrim(rtrim(number_format($alexa->bounce_rate, 2, ".", ""), '0'), '.');
					 $fb_Likes=rtrim(rtrim(number_format($alexa->fb_Likes, 2, ".", ""), '0'), '.');
					 $twitter=rtrim(rtrim(number_format($alexa->twitter, 2, ".", ""), '0'), '.');
					 $pinterest=rtrim(rtrim(number_format($alexa->pinterest, 2, ".", ""), '0'), '.');
					 $row=array($alexa->website,$globalrank,$alexa_us_rank,$alexa_pages_viewed,$alexa_time_on_site,$bounce_rate,$fb_Likes,$twitter,$pinterest);
					 $exceloutput.='<tr>';
					foreach($row as $hval)
					{
						$exceloutput.='<td width="10%" >'.$hval.'</td>';
					}	
					$exceloutput.='</tr>';
					}
					$exceloutput.='</table>';
				$upload_dir = wp_upload_dir();
				$basedir = $upload_dir['basedir'];
				$baseurl = $upload_dir['baseurl'];
				$filename = "competitors_".date('YmdHis').".xls";
				$fp = fopen($basedir.'/'.$filename,'wr');
				fwrite($fp,$exceloutput);
				$excell_file = $baseurl.'/'.$filename; 
				wp_redirect($excell_file);exit;
				}
				//require('competitors-export.php');
			}
			else
			{
				$socialctb = $wpdb->prefix.'social_sites_config';
				$alexacresult = $wpdb->get_results("SELECT * FROM $socialctb  ORDER BY ID ASC");
				if(count($alexacresult) > 0)
				{
					foreach($alexacresult as $configdata)
					{
						insert_Socialstats($configdata);
					}
				}
				$socialtb = $wpdb->prefix.'social_sites';
				$alexaresult = $wpdb->get_results("SELECT * FROM $socialtb ORDER BY ID ASC");
				require('socialstats-list.php');
			}
		}
		else
		{
			$socialctb = $wpdb->prefix.'social_sites_config';
			$alexacresult = $wpdb->get_results("SELECT * FROM $socialctb  ORDER BY ID ASC");
			if(count($alexacresult) > 0)
			{
				foreach($alexacresult as $configdata)
				{
					insert_Socialstats($configdata);
				}
			}
			$socialtb = $wpdb->prefix.'social_sites';
			$alexaresult = $wpdb->get_results("SELECT * FROM $socialtb ORDER BY ID ASC");
			require('socialstats-list.php');
		}
  
		 }

	 function DefaultSettings ()
	  {
			$default = array( 
								'FEEDBURNER_FEED_NAME'=>'',
								'TWITTER_ID'	=> '',
								'FACEBOOK_PAGE_ID'=>'',
								'DOMAIN_NAME'=>$_SERVER['HTTP_HOST']								
								);
				if( !get_option('Socialstats') ): #Set Defaults if no values exist
				add_option( 'Socialstats', $default );
			else: #Set Defaults if new value does not exist
				$dplus = get_option( 'Socialstats' );
				foreach( $default as $key => $val ):
					if( !$dplus[$key] ):
						$dplus[$key] = $val;
						$new = true;
					endif;
				endforeach;
				if( $new )
					update_option( 'Socialstats', $dplus );
			endif;
		}
	 function SaveSettings()
	 {
			$update = get_option( 'Socialstats' );
			$update["FEEDBURNER_FEED_NAME"] = $_POST['feed_burne_namer'];
			$update["TWITTER_ID"] = $_POST['twitter_id'];
			$update["FACEBOOK_PAGE_ID"] = $_POST['facebook_id'];
			$update["DOMAIN_NAME"] = $_POST['domain_name'];
			update_option( 'Socialstats', $update );
			$_POST['notice'] = __('Settings Saved', 'Socialstats');
		}
		function Addcompetitor()
		{
			global $wpdb;
			
		
			$domain_name=trim($_POST['domain_name']);
			$twitter_id='';
			$facebook_id=trim($_POST['facebook_id']);
			$fp_api_access_token='';
		   $domain_name=str_replace('http://www.','',$domain_name);  
		   $domain_name=str_replace('http://','',$domain_name);  
		   $domain_name=str_replace('https://www.','',$domain_name);  
		   $domain_name=str_replace('https://','',$domain_name); 
		   $domain_name=str_replace('www.','',$domain_name); 
			$qry="INSERT INTO `".$wpdb->prefix ."social_sites_config` SET 
			website='".$domain_name."',
			twitter_id='".$twitter_id."',
			fb_id='".$facebook_id."',
			fp_api_access_token='".$fp_api_access_token."'";
			$wpdb->query($qry);
		    $ref_id = $wpdb->insert_id;
			$qry="INSERT INTO `".$wpdb->prefix ."social_sites` SET 
			ref_id='".$ref_id."',
			website='".$domain_name."'";
			$wpdb->query($qry);
			$_POST['notice'] = __('Website Details has been added successfully', 'Socialstats');
		}

		function Editcompetitor()
		{
			 
			global $wpdb;
			$domain_name=trim($_POST['domain_name']);
			$twitter_id='';
			$facebook_id=trim($_POST['facebook_id']);
			$fp_api_access_token='';
			$id=trim($_POST['ID']);
		$domain_name=str_replace('http://www.','',$domain_name);  
	   $domain_name=str_replace('http://','',$domain_name);  
	   $domain_name=str_replace('https://www.','',$domain_name);  
	   $domain_name=str_replace('https://','',$domain_name); 
	   $domain_name=str_replace('www.','',$domain_name); 
	   
	  
			$qry="UPDATE `".$wpdb->prefix ."social_sites_config` SET 
			website='".$domain_name."',
			twitter_id='".$twitter_id."',
			fb_id='".$facebook_id."',
			fp_api_access_token='".$fp_api_access_token."' WHERE ID='".$id."'";
			$wpdb->query($qry);
			$qry="UPDATE `".$wpdb->prefix ."social_sites` SET website='".$domain_name."' WHERE ref_id='".$id."'";
			$wpdb->query($qry);
			$_POST['notice'] = __('Website Details has been updated successfully', 'Socialstats');
		}

		function Importcompetitor()
		{
		//echo '<pre>';print_r($_FILES);exit;
			if (empty($_FILES['xls_import']['tmp_name']))
			{
				$this->log['error'][] = 'No file uploaded, aborting.';
				$this->print_messages();
				return;
			}
			if (!current_user_can('publish_pages') || !current_user_can('publish_posts')) {
				$this->log['error'][] = 'You don\'t have the permissions to publish posts and pages. Please contact the blog\'s administrator.';
				$this->print_messages();
				return;
			}

			$tmp_name = $_FILES['xls_import']['tmp_name'];
			$filename = $_FILES['xls_import']['name'];
		 	$uploadpath=date('YmdHis').rand(0,10).'_'.$filename;
			$puploads=dirname(__FILE__)."/uploadedXLfile/".$uploadpath;
			//chmod(dirname(__FILE__)."/uploadedXLfile", 0777);
		 if(move_uploaded_file($tmp_name,$puploads))
		 {
		 	require 'include/reader.php';
			$colname=array('Website','Alexa_Global_Rank_total','Alexa_US_rank','Pages_Viewed','Time_on_Site','Bounce_rate','FB_Likes','Twitter','Pinterest');
			//echo '<pre>';print_r($colname);exit;				
			$lists=self :: parseExcel($puploads,$colname);
			if(count($lists) > 0)
			{
				unset($lists[0]);
				foreach($lists as $mitems)
				{
					if(trim($mitems['Website']) != '')
					{
						$ssc=$this->db->query("SELECT * FROM ".$wpdb->prefix ."social_sites_config WHERE website='".$mitems['Website']."'");				
						$domain_name=$mitems['Website'];
						$Alexa_Global_Rank_total=$mitems['Alexa_Global_Rank_total'];
						$Alexa_US_rank=$mitems['Alexa_US_rank'];
						$Pages_Viewed=$mitems['Pages_Viewed'];
						$Time_on_Site=$mitems['Time_on_Site'];
						$Bounce_rate=$mitems['Bounce_rate'];
						$FB_Likes=$mitems['FB_Likes'];
						$Twitter=$mitems['Twitter']; 
						$Pinterest=$mitems['Pinterest'];
						if(count($ssc) > 0)
						{
						$domain_name=str_replace('http://www.','',$domain_name);  
						$domain_name=str_replace('http://','',$domain_name);  
						$domain_name=str_replace('https://www.','',$domain_name);  
						$domain_name=str_replace('https://','',$domain_name); 
						$domain_name=str_replace('www.','',$domain_name);
						$qry="INSERT INTO `".$wpdb->prefix ."social_sites_config` SET website='".$domain_name."'";
						$wpdb->query($qry);
						$ref_id = $wpdb->insert_id;
						$qry="INSERT INTO `".$wpdb->prefix ."social_sites` SET 
						ref_id='".$ref_id."',
						website='".$domain_name."',
						alexa_global_rank='".$Alexa_Global_Rank_total."',
						alexa_us_rank='".$Alexa_US_rank."',
						alexa_pages_viewed='".$Pages_Viewed."',
						alexa_time_on_site='".$Time_on_Site."',
						bounce_rate='".$Bounce_rate."',
						fb_Likes='".$FB_Likes."',
						twitter='".$Twitter."',
						Pinterest='".$Pinterest."'";
						$wpdb->query($qry);
					}
						else
						{
						$refid=$ssc[0]->ID;
						$qry="UPDATE `".$wpdb->prefix ."social_sites_config` SET website='".$domain_name."' WHERE ID='".$refid."'";
						$wpdb->query($qry);
						$qry="UPDATE `".$wpdb->prefix ."social_sites` SET 
						website='".$domain_name."',
						alexa_global_rank='".$Alexa_Global_Rank_total."',
						alexa_us_rank='".$Alexa_US_rank."',
						alexa_pages_viewed='".$Pages_Viewed."',
						alexa_time_on_site='".$Time_on_Site."',
						bounce_rate='".$Bounce_rate."',
						fb_Likes='".$FB_Likes."',
						twitter='".$Twitter."',
						Pinterest='".$Pinterest."' WHERE ref_id='".$refid."'";
						$wpdb->query($qry);
					}
						}
				}

			}
		 }
		 unlink($puploads);
		 $_POST['notice'] = __('Competitors are has been imported successfully', 'Competitors');
		}

		function SendMailcompetitor()
		{
			global $wpdb;
			//echo '<pre>';print_r($_POST);exit;
			$exheader =array('Website','Alexa Global Rank total','Alexa US rank','Pages Viewed','Time on Site','Bounce rate','FB Likes','Twitter','Pinterest');
				$mailtable='<table cellpadding="0" style=" border-image: none;margin: 10px 0;border-top: 1px solid #94C9E5;border-left: 1px solid #94C9E5;" cellspacing="0" border="0" width="549">';
				$mailtable.='<tr>';
				foreach($exheader as $hval)
				{
					$mailtable.='<th valign="top" style="background-color: #C3E0EF;border-bottom: 1px solid #94C9E5;border-right: 1px solid #94C9E5;color: #444444;font-size: 12px;font-weight: bold;padding: 0px 0px 0px 5px;" nowrap="nowrap" align="center" width="150"><p style="margin:5px 0; padding:0; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; line-height:22px; color:#555555;">'.$hval.'</p></th>';
				}	
				$mailtable.='</tr>';
				$socialtb = $wpdb->prefix.'social_sites';
				$alexaresult = $wpdb->get_results("SELECT * FROM $socialtb WHERE alexa_global_rank > 0 ORDER BY alexa_global_rank ASC LIMIT 0,5");
				$row=array();
				if(count($alexaresult) > 0)
				{
					foreach($alexaresult as $alexa)
					{
					 $globalrank=rtrim(rtrim(number_format($alexa->alexa_global_rank, 2, ".", ""), '0'), '.');
					 $alexa_us_rank=rtrim(rtrim(number_format($alexa->alexa_us_rank, 2, ".", ""), '0'), '.');
					 $alexa_pages_viewed=rtrim(rtrim(number_format($alexa->alexa_pages_viewed, 2, ".", ""), '0'), '.');
					 $alexa_time_on_site=rtrim(rtrim(number_format($alexa->alexa_time_on_site, 2, ".", ""), '0'), '.');
					 $bounce_rate=rtrim(rtrim(number_format($alexa->bounce_rate, 2, ".", ""), '0'), '.');
					 $fb_Likes=rtrim(rtrim(number_format($alexa->fb_Likes, 2, ".", ""), '0'), '.');
					 $twitter=rtrim(rtrim(number_format($alexa->twitter, 2, ".", ""), '0'), '.');
					 $pinterest=rtrim(rtrim(number_format($alexa->pinterest, 2, ".", ""), '0'), '.');
					 $row=array($alexa->website,$globalrank,$alexa_us_rank,$alexa_pages_viewed,$alexa_time_on_site,$bounce_rate,$fb_Likes,$twitter,$pinterest);
					 $exceloutput.='<tr>';
					 $mailtable.='<tr>';
					foreach($row as $hval)
					{
						$mailtable.='<td style="background-color: #FFFFFF;border-bottom: 1px solid #94C9E5;border-right: 1px solid #94C9E5;color: #444444;font-size: 12px;padding: 8px 5px 8px 10px;" valign="top" align="left" width="440"><p style="margin:5px 0; padding:0; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; line-height:22px; color:#555555; font-weight:bold;">'.$hval.'</p></td>';
					}	
					$mailtable.='</tr>';
				}
				}
				$mailtable.='</table>';
		$sitename=get_option( 'blogname' );
		$adminemail=get_option( 'admin_email' );
	$mailmessage='<div style="margin:15px 0 10px 0; background:#fdfcf8; border:#e2dabb solid 1px; padding:10px;">
	<span style="margin:10px 0; padding:0; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; line-height:22px; color:#5d5d5d;">Welcome to '.$sitename.'.! </span>
	<div style="margin:0 0 5px 0; padding:0; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:14px; line-height:22px; color:#1b69a4;">'.$mailtable.'</div></div>';
	$to=trim($_POST['email_address']);
	$subject="Top 5 Competitors Report"; 
	$headers = 'From: '.$sitename.' <'.$adminemail.'>' . '\r\n';
	$headers .= "MIME-Version: 1.0\r\n";
	$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
	wp_mail( $to, $subject, $mailmessage, $headers );
		$_POST['notice'] = __('Mail has been send successfully', 'Socialstats');
		}

		function parseExcel($excel_file_name_with_path,$colname)
		{
			$data = new Spreadsheet_Excel_Reader();
			$data->setOutputEncoding('CP1251');
			$data->read($excel_file_name_with_path);
			for ($i = 1; $i <= $data->sheets[0]['numRows']; $i++)
			{
				for ($j = 1; $j <= $data->sheets[0]['numCols']; $j++)
				{
					$product[$i-1][$colname[$j-1]]=$data->sheets[0]['cells'][$i][$j];
				}
			}
			return $product;
		}
	 	function print_messages() {
        if (!empty($this->log)) {
	?>
        <div class="wrap">
        <?php if (!empty($this->log['error'])): ?>
        <div class="error">
        <?php foreach ($this->log['error'] as $error): ?>
        <p><?php echo $error; ?></p>
        <?php endforeach; ?>
        </div>
        <?php endif; ?>
        <?php if (!empty($this->log['notice'])): ?>
        <div class="updated fade">
        <?php foreach ($this->log['notice'] as $notice): ?>
        <p><?php echo $notice; ?></p>
        <?php endforeach; ?>
        </div>
        <?php endif; ?>
        </div><!-- end wrap -->
	<?php
            $this->log = array();
        }
    }
		function JuicedMetrics_install() 
		{
			global $wpdb;
			$wpdb->query('DROP TABLE IF EXISTS '.$wpdb->prefix.'sales_report');
			$sql="CREATE TABLE IF NOT EXISTS `".$wpdb->prefix ."sales_report` (
			`ID` bigint(20) NOT NULL AUTO_INCREMENT,
			`report_name` tinytext NOT NULL,
			`date_time` datetime DEFAULT '0000-00-00 00:00:00',
			PRIMARY KEY (`ID`)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
			$wpdb->query($sql);
			$wpdb->query('DROP TABLE IF EXISTS '.$wpdb->prefix.'sales_report_page');

			$sql2="CREATE TABLE IF NOT EXISTS `".$wpdb->prefix ."sales_report_page` (
			`ID` bigint(20) NOT NULL AUTO_INCREMENT,
			`ref_id` bigint(20) NOT NULL,
			`report_name` varchar(200) NOT NULL,
			`page_id` bigint(20) NOT NULL,
			`page_order` int(10) NOT NULL,
			`date_time` datetime NOT NULL,
			PRIMARY KEY (`ID`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
			";
			$wpdb->query($sql2);		
			$wpdb->query('DROP TABLE IF EXISTS '.$wpdb->prefix.'simpleviews');

			$sql3 = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix ."simpleviews` (
			`ID` bigint(20) NOT NULL AUTO_INCREMENT,
			`ref_id` int(10) NOT NULL,
			`post_id` int(10) NOT NULL,
			`view` int(10) DEFAULT NULL,
			`view_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			`ip_address` varchar(30) NOT NULL,
			`return_visit` int(1) NOT NULL,
			PRIMARY KEY (`ID`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
			$wpdb->query($sql3);
			$wpdb->query('DROP TABLE IF EXISTS '.$wpdb->prefix.'social_sites');
		
			$sql4 = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix ."social_sites` (
			`ID` int(10) NOT NULL AUTO_INCREMENT,
			`ref_id` int(10) NOT NULL,
			`website` varchar(300) NOT NULL,
			`alexa_global_rank` decimal(10,2) NOT NULL,
			`alexa_us_rank` decimal(10,2) NOT NULL,
			`alexa_pages_viewed` decimal(10,2) NOT NULL,
			`alexa_time_on_site` decimal(10,2) NOT NULL,
			`bounce_rate` decimal(10,2) NOT NULL,
			`fb_Likes` decimal(10,2) NOT NULL,
			`fb_Mentions` decimal(10,2) NOT NULL,
			`twitter` decimal(10,2) NOT NULL,
			`pinterest` decimal(10,2) NOT NULL,
			`country_name` varchar(200) NOT NULL,
			`country_code` varchar(100) NOT NULL,
			`date_time` datetime NOT NULL,
			`ip_address` varchar(30) NOT NULL,
			`highlight_status` int(1) NOT NULL,
			PRIMARY KEY (`ID`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
			$wpdb->query($sql4);
			$wpdb->query('DROP TABLE IF EXISTS '.$wpdb->prefix.'social_sites_config');

			$sql5 = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix ."social_sites_config` (
			`ID` int(10) NOT NULL AUTO_INCREMENT,
			`website` varchar(300) NOT NULL,
			`fb_id` varchar(200) NOT NULL,
			`twitter_id` varchar(200) NOT NULL,
			`fp_api_access_token` varchar(200) NOT NULL,
			`highlight_status` int(1) NOT NULL,
			PRIMARY KEY (`ID`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
			$wpdb->query($sql5);
			$wpdb->query('DROP TABLE IF EXISTS '.$wpdb->prefix.'social_stats');

			$sql6="CREATE TABLE IF NOT EXISTS `".$wpdb->prefix ."social_stats` (
			`FID` bigint(20) NOT NULL AUTO_INCREMENT,
			`date_time` datetime DEFAULT '0000-00-00 00:00:00',
			`AlexaRank` int(10) NOT NULL,
			`AlexaLinks` int(10) NOT NULL,
			`FacebookLikes` int(10) NOT NULL,
			`TwitterFollows` int(10) NOT NULL,
			`FeedburnerSubscriptions` int(10) NOT NULL,
			`SocialTotal` int(10) NOT NULL,
			`page_id` int(10) NOT NULL,
			`ip_address` varchar(30) NOT NULL,
			PRIMARY KEY (`FID`)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
			$wpdb->query($sql6);
		}

		function JuicedMetrics_drop()
		{
			global $wpdb;

			$wpdb->query('DROP TABLE IF EXISTS '.$wpdb->prefix.'sales_report');
			$wpdb->query('DROP TABLE IF EXISTS '.$wpdb->prefix.'sales_report_page');
			$wpdb->query('DROP TABLE IF EXISTS '.$wpdb->prefix.'simpleviews');
			$wpdb->query('DROP TABLE IF EXISTS '.$wpdb->prefix.'social_sites');
			$wpdb->query('DROP TABLE IF EXISTS '.$wpdb->prefix.'social_sites_config');
			$wpdb->query('DROP TABLE IF EXISTS '.$wpdb->prefix.'social_stats');
		}

	}//END Class JuicedMetrics

endif;
function check_license()
{/*

	$licensing_secret_key='';
	$currentserver=$_SERVER['HTTP_HOST'];
	$oururl = "openspacesmarketing.com";
	$wwwoururl = "www.openspacesmarketing.com";
   	if($currentserver == $whmcsurl)
	{
		 $licensing_secret_key = "Leased-ee6339f8";
	}
	else if($currentserver == $wwwoururl)
	{
		 $licensing_secret_key = "Leased-ee6339f8";
	}
	else
	{
		$licensing_secret_key='';
	}

	return $licensing_secret_key;
*/}
function insert_Socialstats($stats)
{

	//echo '<pre>';print_r($stats);
	 global $wpdb;
	/*if(isset($stats->twitter_id))
	{
		if(trim($stats->twitter_id) != '')
		{
			//Get Twitter Followers Count
	   $twitterName=$stats->twitter_id;
	   $twitterName=str_replace('http://www.twitter.com','',$twitterName);  
	   $twitterName=str_replace('http://twitter.com','',$twitterName);  
	   $twitterName=str_replace('https://www.twitter.com','',$twitterName);  
	   $twitterName=str_replace('https://twitter.com','',$twitterName); 
			$data = @file_get_contents("http://query.yahooapis.com/v1/public/yql?q=SELECT%20*%20from%20html%20where%20url=%22https://twitter.com/".$twitterName."%22%20AND%20xpath=%22//a[@class='js-nav']/strong%22&format=json"); // Opening the Query URL
		 // Decoding the obtained JSON data

		 if($data == '')
		 {
		 	$data = url_get_contents("http://query.yahooapis.com/v1/public/yql?q=SELECT%20*%20from%20html%20where%20url=%22https://twitter.com/".$twitterName."%22%20AND%20xpath=%22//a[@class='js-nav']/strong%22&format=json"); 
		 }
		$data = json_decode($data);
		// The count parsed from the JSON
		$tcount = intval($data->query->count);
		}

	}*/

$tcount=0;	
$data = url_get_contents("https://cdn.api.twitter.com/1/urls/count.json?url=".$stats->website);
if($data == '')
{
	$data = @file_get_contents("https://cdn.api.twitter.com/1/urls/count.json?url=".$stats->website);
}
$data = json_decode($data);
$tcount1=$data->count;

$data = url_get_contents("https://cdn.api.twitter.com/1/urls/count.json?url=www.".$stats->website);
if($data == '')
{
	$data = @file_get_contents("https://cdn.api.twitter.com/1/urls/count.json?url=www.".$stats->website);
}
$data = json_decode($data);
$tcount2=$data->count;

$tcount2=$data->count;
$tcount=$tcount1+$tcount2;	

$fbcount=0;
	$fbMentions=0;	
	if(isset($stats->fb_id))
	{
		if(trim($stats->fb_id) != '')
		{
			//Get Facebook Like Count
			$fpageID = $stats->fb_id;
	   $fpageID=str_replace('http://www.facebook.com','',$fpageID);  
	   $fpageID=str_replace('http://facebook.com','',$fpageID);  
	   $fpageID=str_replace('https://www.facebook.com','',$fpageID);  
	   $fpageID=str_replace('https://facebook.com','',$fpageID); 
	     $data=@file_get_contents('http://graph.facebook.com/' . $fpageID);
		 if($data == '')
		 {
		 	$data = url_get_contents('http://graph.facebook.com/' . $fpageID); 
		 }
		$finfo = json_decode($data);
			/*http://graph.facebook.com/envisionyourweb*/	
			$fbcount = $finfo->likes;
		}
	}
	if(isset($stats->fp_api_access_token))
	{
		if(trim($stats->fp_api_access_token) != '')
		{
			//Get Facebook Like Count
			$fp_api_access_token = $stats->fp_api_access_token;
			$data = @file_get_contents('https://graph.facebook.com/fql/?access_token='.$fp_api_access_token.'&q=SELECT+mention_count+FROM+keyword_insights');
		if($data == '')
		 {
		 	$data = url_get_contents('https://graph.facebook.com/fql/?access_token='.$fp_api_access_token.'&q=SELECT+mention_count+FROM+keyword_insights'); 
		 }
			$fbminfo = json_decode($data);
			/*http://graph.facebook.com/envisionyourweb*/	
			$fbMentions = $fbminfo->mention_count;
		}
	}
	$aresult=0;
	$alinksin=0;
	$pcount=0;
	//echo $stats->website;
	if(isset($stats->website))
	{
		if(trim($stats->website) != '')
          {	
			//Get Alexa Rank and Sites Linking in
			$source = url_get_contents('http://data.alexa.com/data?cli=10&dat=snbamz&url='.$stats->website);
			if($source == '')
			{
			$source = @file_get_contents('http://data.alexa.com/data?cli=10&dat=snbamz&url='.$stats->website);
			}
			/*preg_match('/\<popularity url\="(.*?)" text\="([0-9]+)" source\="panel"\/\>/si', $source, $matches);
			$aresult = ($matches[2]) ? $matches[2] : 0;
			preg_match('/\<linksin num\="([0-9]+)"\/\>/si', $source, $cocok);
			$alinksin = ($cocok[1]) ? $cocok[1] : 0;
			preg_match('/\<country code\="US" name\="United States" rank\="([0-9]+)"\/\>/si', $source, $uranks);
			$usrank = ($uranks[1]) ? $uranks[1] : 0;*/
			@preg_match('/\<popularity url\="(.*?)" text\="([0-9]+)" source\="(.*?)"\/\>/si', $source, $matches);
			$aresult = ($matches[2]) ? $matches[2] : 0;
			@preg_match('/\<linksin num\="([0-9]+)"\/\>/si', $source, $cocok);
			$alinksin = ($cocok[1]) ? $cocok[1] : 0;
			@preg_match('/\<country code\="US" name\="United States" rank\="([0-9]+)"\/\>/si', $source, $uranks);
			$usrank = ($uranks[1]) ? $uranks[1] : 0;
			$ccode='US';
			$name='United States';
			if(intval($usrank) == 0)
			{
				@preg_match('/\<country code\="(.*?)" name\="(.*?)" rank\="([0-9]+)"\/\>/si', $source, $uranks);
				$ccode = ($uranks[1]) ? $uranks[1] : 0;
				@preg_match('/\<country code\="'.$ccode.'" name\="(.*?)" rank\="([0-9]+)"\/\>/si', $source, $uranks);
				$name = ($uranks[1]) ? $uranks[1] : 0;
				@preg_match('/\<country code\="'.$ccode.'" name\="'.$name.'" rank\="([0-9]+)"\/\>/si', $source, $uranks);
				$usrank = ($uranks[1]) ? $uranks[1] : 0;
			}
			//echo phpinfo();
			//echo $stats->website;
			$url = "http://www.alexa.com/siteinfo/".$stats->website;
			//$url="http://www.alexa.com/siteinfo/batcaddy.com";
			//echo $url;
			$ch = curl_init();
			$timeout = 5;
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
			$html = curl_exec($ch);
			curl_close($ch);
			# Create a DOM parser object
			$dom = new DOMDocument();
			# Parse the HTML from Google.
			# The @ before the method call suppresses any warnings that
			# loadHTML might throw because of invalid HTML in the page.
			@$dom->loadHTML($html);
			
			if ($dom) {
			$xml = simplexml_import_dom($dom);
			$elements = $xml->xpath('//*[contains(@data-cat, "bounce_percent")]');
			$elements1 = $xml->xpath('//*[contains(@data-cat, "pageviews_per_visitor")]');
			$elements2 = $xml->xpath('//*[contains(@data-cat, "time_on_site")]');
			$alexa_array=array();
			
			
			
			if(!empty($elements))
			{
				foreach($elements[0]->div as $element) { foreach($element as $val) {$bounce_percent=$val; break;}}
			}
			if(!empty($elements1))
			{
			foreach($elements1[0]->div as $element) {foreach($element as $val) {$pageviews_per_visitor=$val;break;}}
			}
			if(!empty($elements2))
			{
			foreach($elements2[0]->div as $element) {foreach($element as $val) {$time_on_site=$val;break;}}
			}
			}
			//echo $bounce_percent;echo '<br />';
			//echo $time_on_site;echo '<br />';
			//$json_string = file_get_contents('http://api.pinterest.com/v1/urls/count.json?callback=receiveCount&url=http://www.'.$stats->website);
			//$json = json_decode($json_string, true);
				$u='http://api.pinterest.com/v1/urls/count.json?callback=receiveCount&url=http://www.'.$stats->website;
				//echo $u;
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_HEADER, 0);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_URL, $u);
				$data = curl_exec($ch);
				curl_close($ch);
			$data=(array)json_decode($data);
		  
			$pcount=intval( $data['count'] );
		}
	}
	
	
	$table = $wpdb->prefix . "social_sites";
	$result = $wpdb->query("UPDATE $table 
	SET 
	`alexa_global_rank`='".$aresult."',
	`alexa_us_rank`='".$usrank."',
	`alexa_pages_viewed`='".$pageviews_per_visitor."',
	`alexa_time_on_site`='".$time_on_site."',
	`bounce_rate`='".$bounce_percent."',
	`fb_likes`='".$fbcount."',
	`fb_mentions`='".$fbMentions."',
	`twitter`='".$tcount."',
	`pinterest`='".$pcount."',
	`country_code`='".$ccode."',
	`country_name`='".$name."',
	`date_time`='".date('Y-m-d H:i:s')."',
	`ip_address`='".$_SERVER['REMOTE_ADDR']."' WHERE ref_id='".$stats->ID."'");
  return ($result);
		}
if (!function_exists('echo_views')) {
    function echo_views($post_id) {
	//insert_Socialstats($post_id);
        if (update_views($post_id) == 1) {
            $views = get_views($post_id);
            echo number_format_i18n($views);
        } else {
           echo 0;
        }
    }
}
function insert_views($views, $post_id) {
    global $wpdb;
    $table = $wpdb->prefix . "simpleviews";
	 $table1 = $wpdb->prefix . "sales_report_page";
	$pagess = $wpdb->get_results("SELECT * FROM $table1 WHERE page_id='".$post_id."' ORDER BY ref_id ASC");

	if(count($pagess) > 0)
	{
		foreach($pagess as $sitems)
		{
			$result = $wpdb->query("INSERT INTO $table 
			SET 
			ref_id='".$sitems->ref_id."',
			post_id='".$post_id."',
			view='".$views."',
			view_datetime='".date('Y-m-d H:i:s')."',
			ip_address='".$_SERVER['REMOTE_ADDR']."'");
		}
	}
	return ($result);
}
function update_views($post_id) {
    global $wpdb;
    $table = $wpdb->prefix . "simpleviews";
    $views = get_views($post_id) + 1;
    if($wpdb->query("SELECT view FROM $table WHERE post_id = '$post_id' AND ip_address='".$_SERVER['REMOTE_ADDR']."'") != 1)
	{
		$result =insert_views($views, $post_id);
	}
	else
	{	
		$returnvisit=$views-1;
		 $table1 = $wpdb->prefix . "sales_report_page";
	$pagess = $wpdb->get_results("SELECT * FROM $table1 WHERE page_id='".$post_id."' ORDER BY ref_id ASC");
	if(count($pagess) > 0)
	{
		foreach($pagess as $sitems)
		{
			$result = $wpdb->query("UPDATE $table SET view = $views,return_visit='".$returnvisit."' WHERE post_id = '$post_id' AND ref_id='".$sitems->ref_id."' AND ip_address='".$_SERVER['REMOTE_ADDR']."'");
		}
	}
	}
   return ($result);
}

function get_views($post_id) {
    global $wpdb;
    $table = $wpdb->prefix . "simpleviews";
    $result = $wpdb->get_results("SELECT view FROM $table WHERE post_id = '$post_id' AND ip_address='".$_SERVER['REMOTE_ADDR']."'", ARRAY_A);
    if (!is_array($result) || empty($result)) {
        return "0";
    } else {
        return $result[0]['view'];
    }
}

function url_get_contents ($url) {
    if (function_exists('curl_exec')){ 
        $conn = curl_init($url);
        curl_setopt($conn, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($conn, CURLOPT_FRESH_CONNECT,  true);
        curl_setopt($conn, CURLOPT_RETURNTRANSFER, 1);
        $url_get_contents_data = (curl_exec($conn));
        curl_close($conn);
    }
return $url_get_contents_data;

} 

if( class_exists('JuicedMetrics') )
	$JuicedMetrics = new JuicedMetrics();