	<div style="clear:both;"></div>



	<?php



	  	$socialtb = $wpdb->prefix.'social_sites_config';



		$wpdb->get_results("SELECT * FROM $socialtb ORDER BY ID ASC");



		if($wpdb->num_rows > 0)



		{



			$gear='settting-1.png';



		}



		else



		{



			$gear='settting.png';



		}



		 



	  $chost=$_SERVER['HTTP_HOST'];

	  $chost=str_replace('http://www.','',$chost);  

	  $chost=str_replace('http://','',$chost);  

	  $chost=str_replace('https://www.','',$chost);  

	  $chost=str_replace('https://','',$chost);  



?>







<div style=" margin-left: 10px; margin-top: 25px; width: 90%;">



  



    



    <div style="width:100%">



    <div style="float:left; width:34%">



    <h2>JuicedMetrics.com&nbsp;&nbsp;<a title="Import Competitors" href="<?php echo get_option('siteurl').'/wp-admin/admin.php?page=Competitors&flag=import';?>"><img style="vertical-align:top;margin-top: -8px;"  src="<?php echo plugins_url("assets/images/import.png", __FILE__);?>" /></a>&nbsp;<a title="Export Competitors" href="<?php echo get_option('siteurl').'/wp-admin/admin.php?page=Competitors&flag=exportdata';?>"><img style="vertical-align:top;margin-top: -8px;"  src="<?php echo plugins_url("assets/images/excel.png", __FILE__);?>" /></a>&nbsp;<a title="Send Report" href="<?php echo get_option('siteurl').'/wp-admin/admin.php?page=Competitors&flag=sendmail';?>"><img style="vertical-align:top;margin-top: -8px;"  src="<?php echo plugins_url("assets/images/Email.png", __FILE__);?>" /></a>   </h2>



    </div>



   <div style="float:left; width:36%;padding-left:10px;">



    <h2 style="font-weight: bold; font-size: 27px;">Competitors</h2>



    </div>



    



    <div style="float:left; width:28%;padding-left:10px;">



    <h2><img style="vertical-align:top;" src="<?php echo plugins_url("assets/images/Juiced-Metrics-Green.png", __FILE__);?>" /></h2>



    </div>



    



    </div>



    



    <div style="clear:both;"></div>











<style>



.setlink



{



 color: #0074A2;cursor: pointer;text-decoration: underline;



}



.footable > tbody > tr > td.txtright



{



	text-align:right;



	padding-right:10px;



}



</style>



<table id="table" class="table table-striped table-bordered dataTable " cellspacing="0" width="100%">



        <thead>



      



            <tr>



                <th>Website</th>



                <th>Alexa Global Rank total</th>



                <th data-hide="phone,tablet">Alexa US rank</th>



                <th data-hide="phone,tablet">Pages Viewed</th>



                <th data-hide="phone,tablet">Time on Site</th>



               <th data-hide="phone,tablet">Bounce rate</th>



                <th data-hide="phone,tablet">FB Likes</th>



                 <!--<th>FB Mentions</th>-->



                  <th data-hide="phone,tablet">Twitter</th>



                   <th data-hide="phone,tablet">Pinterest</th>



            </tr>



        </thead>



		



  <tbody>







       <?php 



	    



	 



	    if(count($alexaresult) > 0)



	   {

			//echo '<pre>'; print_r($alexaresult); echo '</pre>';

			foreach($alexaresult as $alexa)



			{



			



			$alexurl = "http://www.alexa.com/siteinfo/".$alexa->website;



			



			$socialctb = $wpdb->prefix.'social_sites_config';



			$configdata = $wpdb->get_results("SELECT * FROM $socialctb WHERE ID='".$alexa->ref_id."'");



			



			 if($alexa->alexa_global_rank == '0.00')



			{



				$globalrank='-';



			}



			else



			{



				$globalrank='<a target="_blank" href="'.$alexurl.'">'.$alexa->alexa_global_rank.'</a>';



				$globalrank=$alexa->alexa_global_rank;



			}



			



			



			if(intval($alexa->highlight_status) > 0)



			{



			

		if($globalrank>0)

		{

		  $globalrank=rtrim(rtrim(number_format($globalrank, 2, ".", ""), '0'), '.');

		}

			



			



			



				echo ' <tr>



				<td style="background-color:#E6B9B8;"><a target="_blank" href="http://'.$alexa->website.'">'.$alexa->website.'</a></td>';



				



				 echo '<td class="txtright"  style="background-color:#E6B9B8; color: #0074A2;cursor: pointer;text-decoration: underline;"  onclick="OpenInNewTab(\''.$alexurl.'\');">'.$globalrank.'</td>';



               



                echo '<td class="txtright"  title="'.$alexa->country_code.' Rank" style="background-color:#E6B9B8;">'.rtrim(rtrim(number_format($alexa->alexa_us_rank, 2, ".", ""), '0'), '.').'</td>



                <td  class="txtright" style="background-color:#E6B9B8;">'.rtrim(rtrim(number_format($alexa->alexa_pages_viewed, 2, ".", ""), '0'), '.').'</td>



                <td  class="txtright" style="background-color:#E6B9B8;">'.rtrim(rtrim(number_format($alexa->alexa_time_on_site, 2, ".", ""), '0'), '.').'</td>



				<td class="txtright"  style="background-color:#E6B9B8;">'.rtrim(rtrim(number_format($alexa->bounce_rate, 2, ".", ""), '0'), '.').'</td>';



				if(trim($configdata[0]->fb_id) != '')



				{



			echo '<td class="txtright"  style="background-color:#E6B9B8; color: #0074A2;cursor: pointer;text-decoration: underline;"  onclick="OpenInNewTab(\'http:///www.facebook.com/'.$configdata[0]->fb_id.'\');">'.rtrim(rtrim(number_format($alexa->fb_Likes, 2, ".", ""), '0'), '.').'</td>';



				}



				else



				{



					echo '<td class="txtright"  style="background-color:#E6B9B8;">'.rtrim(rtrim(number_format($alexa->fb_Likes, 2, ".", ""), '0'), '.').'</td>';



				}



				



				



				//echo '<td style="background-color:#E6B9B8;">'.$alexa->fb_mentions.'</td>';



				



				if(trim($configdata[0]->twitter_id) != '')



				{



echo '<td  class="txtright" style="background-color:#E6B9B8;color: #0074A2;cursor: pointer;text-decoration: underline;;"  onclick="OpenInNewTab(\'http:///www.twitter.com/'.$configdata[0]->twitter_id.'\');">'.rtrim(rtrim(number_format($alexa->twitter, 2, ".", ""), '0'), '.').'</td>';



				}



				else



				{



					echo '<td class="txtright"  style="background-color:#E6B9B8;">'.rtrim(rtrim(number_format($alexa->twitter, 2, ".", ""), '0'), '.').'</td>';



				}



				



				



				



				echo '<td  class="txtright" style="background-color:#E6B9B8;">'.rtrim(rtrim(number_format($alexa->pinterest, 2, ".", ""), '0'), '.').'</td>



				



            </tr>';



			}



			else



			{



			 $globalrank=rtrim(rtrim(number_format($alexa->alexa_global_rank, 2, ".", ""), '0'), '.');



				echo ' <tr><td><a target="_blank" href="http://'.$alexa->website.'">'.$alexa->website.'</a></td>';



				 echo '<td class="txtright"  style=" color: #0074A2;cursor: pointer;text-decoration: underline;" onclick="OpenInNewTab(\''.$alexurl.'\');">'.$globalrank.'</td>';



                echo '<td class="txtright" title="'.$alexa->country_code.' Rank">'.rtrim(rtrim(number_format($alexa->alexa_us_rank, 2, ".", ""), '0'), '.').'</td>



                <td class="txtright" >'.rtrim(rtrim(number_format($alexa->alexa_pages_viewed, 2, ".", ""), '0'), '.').'</td>



                <td class="txtright" >'.rtrim(rtrim(number_format($alexa->alexa_time_on_site, 2, ".", ""), '0'), '.').'</td>



				<td class="txtright" >'.rtrim(rtrim(number_format($alexa->bounce_rate, 2, ".", ""), '0'), '.').'</td>';



				if(trim($configdata[0]->fb_id) != '')



				{



					echo '<td  class="txtright"  style=" color: #0074A2;cursor: pointer;text-decoration: underline;" onclick="OpenInNewTab(\'http:///www.facebook.com/'.$configdata[0]->fb_id.'\');">'.rtrim(rtrim(number_format($alexa->fb_Likes, 2, ".", ""), '0'), '.').'</td>';



				}



				else



				{



					echo '<td class="txtright" >'.rtrim(rtrim(number_format($alexa->fb_Likes, 2, ".", ""), '0'), '.').'</td>';



				}



				



				



				//echo '<td>'.$alexa->fb_mentions.'</td>';



				



				



				if(trim($configdata[0]->twitter_id) != '')



				{



					echo '<td class="txtright"  style=" color: #0074A2;cursor: pointer;text-decoration: underline;" onclick="OpenInNewTab(\'http:///www.twitter.com/'.$configdata[0]->twitter_id.'\');">'.rtrim(rtrim(number_format($alexa->twitter, 2, ".", ""), '0'), '.').'</td>';



				}



				else



				{



					echo '<td class="txtright" >'.rtrim(rtrim(number_format($alexa->twitter, 2, ".", ""), '0'), '.').'</td>';



				}



				



				



				echo '<td class="txtright" >'.rtrim(rtrim(number_format($alexa->pinterest, 2, ".", ""), '0'), '.').'</td>



           		</tr>';



			



			}



			 







								



			}



	   }



	   



	   ?>



 



       



        </tbody>



         <tfoot>



                <tr>



                    <td colspan="9">



                        <div class="pagination pagination-centered"></div>



                    </td>



                </tr>



                </tfoot>



    </table>



</div>







<!--



<table id="table" class="table table-striped table-bordered dataTable " cellspacing="0" width="100%">







    <thead><tr>  <th>Website</th>



                <th>Alexa Global Rank total</th>



                   <th>Alexa US rank</th></tr></thead>



    <tbody>



        <tr><td>proaupair.com</td><td>7877887.00</td><td>6</td></tr>



        <tr><td>aupair-world.net</td><td>9877887.00</td><td>-</td></tr>



        <tr><td>aupairinamerica.com</td><td>8877887.00</td><td>16</td></tr>



        <tr><td>aupair.com</td><td>6877887.00</td><td>5</td></tr>



        <tr><td>aupaircare.com</td><td>-</td><td>-4</td></tr>



        <tr><td>greataupair.com</td><td>7877887.00</td><td>9</td></tr>



        <tr><td>culturalcareaupair.com</td><td>-</td><td>21</td></tr>



        <tr><td>goaupair.com</td><td>5877887.00</td><td>-</td></tr>



        <tr><td>interexchange.org</td><td>-</td><td>51</td></tr>



    </tbody>



</table>



-->







<script type="text/javascript">



	$= jQuery.noConflict(); 



    $(function () {



        $('#table').footable();



    });



</script>

<!--	<script type="text/javascript">



	



	$(document).ready(function() {



    $('#example').dataTable( {



        "order": [[ 2, "asc" ]],



		"bFilter": false,



    } );



} );



	</script>



    -->



    



    <script type='text/javascript'>//<![CDATA[ 



$(window).load(function(){



$.fn.dataTableExt.oSort['nullable-asc'] = function(a,b) {



        if (a == '-')



            return 1;



        else if (b == '-')



            return -1;



		else if (a == '0.00')



            return 1;



		else if (b == '0.00')



            return -1;



		else if (a == '0')



            return 1;



		else if (b == '0')



            return -1;



		  else



        {



            var ia = parseInt(a);



            var ib = parseInt(b);



            return (ia<ib) ? -1 : ((ia > ib) ? 1 : 0);



        }



}







$.fn.dataTableExt.oSort['nullable-desc'] = function(a,b) {



        if (a == '-')



            return 1;



        else if (b == '-')



            return -1;



		else if (a == '0.00')



            return 1;



		else if (b == '0.00')







            return -1;



		else if (a == '0')



            return 1;



		else if (b == '0')



            return -1;



        else



        {



            var ia = parseInt(a);



            var ib = parseInt(b);



            return (ia>ib) ? -1 : ((ia < ib) ? 1 : 0);



        }



}







$('#table').dataTable( {



        "bPaginate": false,



		 "order": [[ 2, "asc" ]],



        "bFilter": false,



        "aoColumns": [



                null,



                {"bSortable": true, "sType": "nullable"},



				 {"bSortable": true, "sType": "nullable"},



				 {"bSortable": true, "sType": "nullable"},



				 {"bSortable": true, "sType": "nullable"},



				 {"bSortable": true, "sType": "nullable"},



				 {"bSortable": true, "sType": "nullable"},



				 {"bSortable": true, "sType": "nullable"},



				  {"bSortable": true, "sType": "nullable"}



                    ],



    } );



});











function OpenInNewTab(url) {



  var win = window.open(url, '_blank');



  win.focus();



}







</script>



