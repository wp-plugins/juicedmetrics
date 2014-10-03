<div class="wrap">
            	
<style type="text/css">
.setup
{
	background-color:#F1F1F1;
	 font-family: myriad pro;
}
.setup h2
{
	color:#222222;
	font-size: 27px;
    font-weight: bold;
}
.setup h3
{
	color:#222222;
	font-size: 25px;
    font-weight: bold;
}

.setup p
{
	color:#222222;
	font-size: 16px;
	font-weight:bold;
}
.salesdiv
{
	padding-left:20px;
}
.salestabel table th
{
background-color:#222222;
color:#FFFFFF;
	font-size: 15px;
	font-weight:bold;
	padding:10px;
	text-align: left;
}

.salestabel table td
{
color:#000;
	font-size: 15px;
	padding:5px;
	text-align: left;
}

.salestabel .row1
{
	background-color:#F6F6F6;
}
.salestabel .row2
{
	background-color:#fff;
}

.salestabel a
{
color:#818AFC;
text-decoration:underline;
}
.setup .txtbox
{
  border: 2px solid #E5E5E6;
    height: 36px;
    width: 225px;
}

.setup .reportbtn
{
  border: 1px solid #E5E5E6;
    height: 36px;
    width: 225px;
	border-radius:5px;
	background-color:#E5E5E5;
}

.setup td img
{
	height: 30px;
    width: 35px;
}

.newimg {
    padding-left: 17px;
    position: relative;
    right: 53px;
   
}

</style>
<div class="setup">
<!--<h2>Juiced Metrics</h2>-->
<h2><img src="<?php echo plugins_url("assets/images/Juiced-Metrics-Green.png", __FILE__);?>" /></h2>

<hr />
<div style="width:85%;">

<div style="margin:20px;">
<h3>Setup is simple</h3>
<p>You can create report names below.</p>




 


 




<div  style="   padding-left: 185px;">
<span>
<img src="<?php echo plugins_url("assets/images/l-r.png", __FILE__);?>" /> 
</span>
<div>
 <form id="trackform" name="trackform" action="<?php echo $mngpg;?>" method="post"><input type="hidden" name="addreport" value="addreport" />

<p><strong style="font-size:21px;">Step 1</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Create Report Name: <input   class="txtbox" type="text" name="report_name" id="report_name" /></p>
<p style="text-align:center; padding-right: 70px;"><a href="javascript:void(0);" onclick="formsubmit();"><img src="<?php echo plugins_url("assets/images/create_report.png", __FILE__);?>" /></a></p>
</form>

 </div>
</div>

<div  style="padding-right: 50px;" >
<div style="float:right" class="newimg"><img src="<?php echo plugins_url("assets/images/left1.png", __FILE__);?>" /> </div>

<div style="float:right"><h2>STEP 2</h2>
<p style="  margin-top: -15px;">Setup pages to track</p>

</div></div>
<div  style="padding-left: 35px;" >
<div style="clear:both;"></div>
<div class="salesdiv salestabel">
<form name="report_from" action="" method="POST" id="report_from">
<table  cellpadding="0" cellspacing="0" width="100%" border="0">
<tr>
<th width="10%"  style="text-align:center;"><input name="checkbox" id="checkbox" onclick="javascript:RCheckAll();" type="checkbox"></th>
<th width="60%">REPORT NAME</th>
<th width="15%">DATE CREATED</th>
<th width="15%">ACTIONS</th>
</tr>

<?php
	$i=1;
	foreach( $donors as $dn ):
	$val=$i%2;
	$class=($val) ? 'row1':'row2';
	
	$gurl = get_option('siteurl').'/wp-admin/admin.php?page=JuicedMetricsGraph&graph='.$dn->ID;
	
	$tb = $wpdb->prefix.'sales_report_page';
	
	//echo "SELECT COUNT(*) FROM $tb WHERE ref_id='".$dn->ID."'";echo '<br />';
	$page_count = $wpdb->get_row( "SELECT COUNT(*) as tot FROM $tb WHERE ref_id='".$dn->ID."'" );

?>
<tr class="<?=$class;?>">
<td width="10%"  style="text-align:center;"><input type="checkbox" name="reportid[]" value="<?php echo $dn->ID;?>" /></td>
<td width="60%"><?php echo $dn->report_name;?></td>
<td width="10%"><?php echo $dn->date_time;?></td>
<td width="10%"><?php
if(intval($page_count->tot) > 0)
{
	?>
	<a href="<?php echo get_option('siteurl').'/wp-admin/admin.php?page=JuicedMetrics&rname='.urlencode($dn->report_name).'&report='.$dn->ID;?>"><img src="<?php echo plugins_url("assets/images/settting-1.png", __FILE__);?>" /></a>&nbsp;&nbsp;
	<?php
}
else
{
	?>
	<a href="<?php echo get_option('siteurl').'/wp-admin/admin.php?page=JuicedMetrics&rname='.urlencode($dn->report_name).'&report='.$dn->ID;?>"><img src="<?php echo plugins_url("assets/images/settting.png", __FILE__);?>" /></a>&nbsp;&nbsp;<?php
}
?><a href="<?php echo $gurl;?>"><img src="<?php echo plugins_url("assets/images/graphs.png", __FILE__);?>" /></a></td>
</tr>

   <?php
   $i++;
	endforeach;
  ?>


</table>
<p style="text-align:right;"><a href="javascript:void(0);" onclick="Rformsubmit();">Delete</a></p>
</form>


    
    
</div>

</div>

</div>
</div>


<div>		
		<h3>How To Use:</h3>
		
		<ul>
			<li>
				* From the <b>theme html</b> use: <code>&lt;?php if (function_exists('echo_views')) {echo_views(get_the_ID());} ?&gt;</code> This shortcode function will be get page view count.
				<br>
                
				<br>&nbsp;&nbsp; For get the page view count on certain pages and post use this shotcode function : <code>&lt;?php if (function_exists('echo_views')) {echo_views(get_the_ID());} ?&gt;</code> 
                <br />
			
			</li>
            <li>&nbsp;</li>
            <li>If you have some support issue, don't hesitate to <a target="_blank" href="http://www.envisionyourwebsite.com">write here.</a>
		 	<br>The Envision Your Website team will be happy to support you on any issue.</li>
		</ul>
	
		
	</div>


<script type="text/javascript">
	function formsubmit()
	{
		document.trackform.submit();
	}
	
	function Rformsubmit()
	{
		var conf=confirm("Are your sure if you want to delete the selected report");
		if(conf)
		{
			document.report_from.submit();
		}
		
	}
	
	function RCheckAll()
	{
		if(document.getElementById("checkbox").checked == true)
		{
			chk=document.getElementsByName("reportid[]");
			for(var i=0;i<chk.length;i++)
			{
				chk[i].checked = true;
			}
		}
		else
		{
			chk=document.getElementsByName("reportid[]");
			for(var i=0;i<chk.length;i++)
			{
				chk[i].checked = false;
			}
		} 
	}
</script>



                
                
            </div>
			</div>
            
