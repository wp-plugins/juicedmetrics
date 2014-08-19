<?php
if( !class_exists('ManageJuicedMetrics') ):
	class ManageJuicedMetrics{
		function ManageJuicedMetrics() { //constructor
			if( $_GET['page'] == 'JuicedMetrics' && ( $_GET['doaction'] || $_GET['delete'] ) )
				$this->Actions();
				
			if( $_POST['updatedonor'] )
				$this->Update();
		}
		
		function Actions(){
		
			global $wpdb;
			$tb = $wpdb->prefix.'sales_report_page';
			if( $_GET['action'] == 'delete' || $_GET['delete']):
				if( $_GET['action'] ) $dIDs = $wpdb->escape($_GET['donor']);
				$mngpg = get_option('siteurl').'/wp-admin/admin.php?page=JuicedMetrics&rname='.urlencode($_GET['rname']).'&report='.trim($_GET['report']);
				if( $_GET['delete'] ) $dIDs[] = $wpdb->escape($_GET['delete']);
				foreach( $dIDs as $dID ):
					$del = "DELETE FROM $tb WHERE ID = $dID LIMIT 1";
					//echo $del; exit;
					$wpdb->query($del);
					$msg = 2;
				endforeach;
				header("Location:$mngpg&msg=2");
			endif;
		}
		
		function Manage(){
	
			global $wpdb;
			if(isset($_POST['addreport'])):
				$this->AddReport();
			elseif(isset($_POST['addpostpage'])):
			$this->AddPostPage();
			else:
			
			if(isset($_GET['report'])):
					
				$resultsPosts = array();
				$resultsTerms = array();
			$tempPosts = get_posts();
			foreach ( $tempPosts as $post ) {
				
				$linkTitle = apply_filters( 'the_title', $post->post_title );
				$linkTitle = apply_filters( 'search_autocomplete_modify_title', $linkTitle, $tempObject );
			
				$resultsPosts[] = array(
					'title' => $linkTitle,
					'id'   => $post->ID,
					'link'   => $post->guid,
				);
			}
		
			$tempPosts = get_pages();
			foreach ( $tempPosts as $post ) {
				
				$linkTitle = apply_filters( 'the_title', $post->post_title );
				$linkTitle = apply_filters( 'search_autocomplete_modify_title', $linkTitle, $tempObject );
			
				$resultsPosts[] = array(
					'title' => $linkTitle,
					'id'   => $post->ID,
					'link'   => $post->guid,
				);
			}
					$results = array_merge( $resultsPosts, $resultsTerms );


			$mngpg = get_option('siteurl').'/wp-admin/admin.php?page=JuicedMetrics';
			$tb = $wpdb->prefix.'sales_report_page';
			$pageresult = $wpdb->get_results("SELECT * FROM $tb WHERE ref_id='".$_GET['report']."' ORDER BY ID ASC");
			require('add-report-page.php');
			else:
			
			if(count($_POST) > 0)
			{
				if(count($_POST['reportid']) > 0)
				{
					foreach($_POST['reportid'] as $delid)
					{
						$qry="DELETE FROM `".$wpdb->prefix ."sales_report` WHERE `ID` ='".intval($delid)."'";
						$wpdb->query($qry);
						
						$qry="DELETE FROM `".$wpdb->prefix ."sales_report_page` WHERE `ref_id` ='".intval($delid)."'";
						$wpdb->query($qry);
					}
				}
				
			}
			$tb = $wpdb->prefix.'sales_report';
			$mngpg = get_option('siteurl').'/wp-admin/admin.php?page=JuicedMetrics';
			$donors = $wpdb->get_results("SELECT * FROM $tb ORDER BY ID ASC");
			require('sales-setup.php');
		endif;
			endif;
		}
	function ajaxResponse()
{
	global $wpdb; 	global $userdata;  
	
	get_currentuserinfo(); 
	echo "Hello ". $userdata->user_login;
	exit;
}
	
	
		
		function AddPostPage()
		{
			global $wpdb;
			$tb = $wpdb->prefix.'sales_report_page';
						
			$ref_id = $_POST['ref_id'];
			$report_name = $_POST['report_name'];
			$page_id = $_POST['page_id'];
			
			unset($_POST['addpostpage']);
			
			foreach( $_POST as $key => $val ):
				$update[] = $key." = '".$val."'";
			endforeach;
			
			$key='date_time';
			$val=date('Y-m-d H:i:s');
			$update[] = $key." = '".$val."'";
			
			//echo "INSERT INTO $tb SET ".implode(', ',$update)."" ;exit;
			
			$wpdb->query("INSERT INTO $tb SET ".implode(', ',$update)."" );
			$mngpg = get_option('siteurl').'/wp-admin/admin.php?page=JuicedMetrics&rname='.urlencode($report_name).'&report='.$ref_id.'&msg=1';
			
			echo '<META http-equiv="refresh" content="0;URL='.$mngpg.'">';
			exit();
			
			//header("Location:$mngpg&msg=1");
			
		
	
	
			
		}
		
			function AddReport(){
	
			global $wpdb;
			$tb = $wpdb->prefix.'sales_report';
						
			$reportname = $_POST['report_name'];
			unset($_POST['addreport']);
			
			foreach( $_POST as $key => $val ):
				$update[] = $key." = '".$val."'";
			endforeach;
			
			$key='date_time';
			$val=date('Y-m-d H:i:s');
			$update[] = $key." = '".$val."'";
			
			$wpdb->query("INSERT INTO $tb SET ".implode(', ',$update)."" );
			$mngpg = get_option('siteurl').'/wp-admin/admin.php?page=JuicedMetrics&msg=1';
			
			echo '<META http-equiv="refresh" content="0;URL='.$mngpg.'">';
			exit();
			
			//header("Location:$mngpg&msg=1");
			
		}
		
		
	}
endif;

if( class_exists('ManageJuicedMetrics') )
	$manageDP = new ManageJuicedMetrics();