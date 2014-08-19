<script type="text/javascript" src="<?php echo plugins_url("assets/javascripts/d3.js", __FILE__);?>"></script>
<script type="text/javascript" src="<?php echo plugins_url("assets/javascripts/d3-funnel-charts.js", __FILE__);?>"></script>


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
	font-size: 15px;
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


.newimg {
    padding-left: 17px;
    position: relative;
    right: 53px;
    top: 26px;
}
.cf li h1
{
	font-weight:bold;
	 font-family: myriad pro;
	 font-size:17px;
}

.cf li
{
	float:left;
	padding-left: 10px;
	width: 230px;
	border-bottom:1px solid #ccc;
	height:295px;
}

</style>
<div class="setup">
<!--<h2>Juiced Metrics</h2>-->
<h2><img src="<?php echo plugins_url("assets/images/Juiced-Metrics-Green.png", __FILE__);?>" /></h2>
<hr />
<div style="width:85%;">

<div style="margin:20px;">
<h3>Conversion Funnels</h3>
<p>Trackable pages can be created to follow how traffic is reacting to your sales funnels.</p>
<p>Click on a conversion funnel to see the statistics in more details. If you have a custom enhancement please 
<a href="#">click here to let us know what your are looking for.</a></p>
</div>

<div style="margin:20px;" style="width:85%"> 
<ul class="cf">

<?php
/*$newarray=array(
array('title'=>'How to make a business grow Free download',
'Funnel'=>'5 Page Funnel',
'Visits'=>'1200 Visits',
'Conversion'=>'83% Conversion'
),

array('title'=>'Webinar is in 3 days campaign',
'Funnel'=>'5 Page Funnel',
'Visits'=>'1200 Visits',
'Conversion'=>'72% Conversion'
),

array('title'=>'What ever business owner should know',
'Funnel'=>'5 Page Funnel',
'Visits'=>'1200 Visits',
'Conversion'=>'60% Conversion'
),


array('title'=>'What ever business owner should know',
'Funnel'=>'5 Page Funnel',
'Visits'=>'1200 Visits',
'Conversion'=>'60% Conversion'
),




array('title'=>'How to make business grow book',
'Funnel'=>'5 Page Funnel',
'Visits'=>'1200 Visits',
'Conversion'=>'83% Conversion'
),

array('title'=>'Webinar is in 3days campaign',
'Funnel'=>'5 Page Funnel',
'Visits'=>'1200 Visits',
'Conversion'=>'72% Conversion'
),

array('title'=>'What ever business owner should know',
'Funnel'=>'5 Page Funnel',
'Visits'=>'1200 Visits',
'Conversion'=>'60% Conversion'
),


array('title'=>'What ever business owner should know',
'Funnel'=>'5 Page Funnel',
'Visits'=>'1200 Visits',
'Conversion'=>'60% Conversion'
),

);
*/


$i=1;
foreach($newarray as $subarray)
{




$firstval=array_slice($subarray['pageinfo'],0,1);
$endval=end($subarray['pageinfo']);




$firstval=$firstval[0]['uniquevist'];
$endval=$endval['uniquevist'];




if($firstval > $endval)
{
	if(intval($firstval) > 0)
	{
		//$conversion=($endval-$firstval)/$firstval*100;
		$conversion=($endval/$firstval)*100;
	}
	
}
else
{
	if(intval($endval) > 0)
	{
		//$conversion=($firstval-$endval)/$endval*100;
	$conversion=($firstval/$endval)*100;
	}
	
}



$dataarray=array();
$graph_dataarray=array();
$graphvalue='';
if(count($subarray['pageinfo']))
{
	foreach($subarray['pageinfo'] as $sitems)
	{
		//$dataarray[]='[\''.$sitems['title'].'\','.$sitems['uniquevist'].']';
		$dataarray[]='[\'Views\','.$sitems['uniquevist'].']';
		
		if(intval($sitems['uniquevist']) > 0)
		{
			$graph_dataarray[]='[\'Views\','.$sitems['uniquevist'].']';
			
		}
		else
		{
			$dummyvisit='0.3';
			$graph_dataarray[]='[\'Views\','.$dummyvisit.']';
			
		}
	}
	
	$graphvalue=implode(',',$graph_dataarray);
	//echo '<pre>';print_r($dataarray);
}

//echo $graphvalue;
//exit;



$gurl = get_option('siteurl').'/wp-admin/admin.php?page=JuicedMetricsGraph&graph='.$subarray['id'];

?>
<li><div>
<table width="100%" cellpadding="0" cellspacing="0">
<tr><td colspan="2" style="text-align:left;"><h1 style="height: 30px;text-align: center;"><a href="<?php echo $gurl;?>" style="color: #444444;; text-decoration:none;"><?php echo $subarray['title'];?></a></h1></td></tr>
<tr><td colspan="2" style="text-align:center;"><div id="funnelContainer<?php echo $i;?>"></div></td></tr>
<tr><td colspan="2" style="text-align:center;"><p><?php echo number_format($conversion,2);?>% Conversion</p></td></tr>
<tr>
<td><?php echo $subarray['Funnel'];?> Page Funnel</td>
<td><?php echo $subarray['Visits'];?> Visits</td>
</tr>
</table>


<script type="text/javascript">
//var data = [['Views', 1500], ['Comments', 300], ['Responses', 150]];
	
	var data = [<?php echo $graphvalue;?>];
	
	
    var chart = new FunnelChart(data, 183, 127, 1/4);
    chart.draw('#funnelContainer<?php echo $i;?>', 2);
</script></div>
</li>

<?php	
$i++;
}

?>





</ul>

</div>
</div>





                    
                   
            </div>