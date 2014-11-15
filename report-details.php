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

	font-size: 13x;

	font-weight:bold;

	 font-family: myriad pro;

	 margin:0;

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

 /* float: right;*/

   /* padding-left: 17px;

 

    right: 0;

    top: 402px;*/

	position: relative;

	 left: -37px;

	 

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

}



</style>

<div class="setup">

<!--<h2>Juiced Metrics</h2>-->

<h2><img src="<?php echo plugins_url("assets/images/Juiced-Metrics-Green.png", __FILE__);?>" /></h2>



<?php



$tuniquevisit=0;



if(count($uniquevisit) > 0)

{

	 $tuniquevisit=array_sum($uniquevisit);

}



$rtuniquevisit=0;



if(count($runiquevisit) > 0)

{

	 $rtuniquevisit=array_sum($runiquevisit);

}





 $string = str_replace('-', ',', $string);



$dataarray=array();

$graphvalue='';





if(count($subarray) > 0)

{



	

	$i=0;

	foreach($subarray as $sitems)

	{

	

		if(intval($sitems['uniquevist']) > 0)

		{

			$graph_dataarray[]='[\''.html_entity_decode($sitems['title']).'\','.$sitems['uniquevist'].']';

			

		}

		else

		{

			$dummyvisit='0.3';

			$graph_dataarray[]='[\''.html_entity_decode($sitems['title']).'\','.$dummyvisit.']';

			

		}

		

		$dataarray[]='[\''.html_entity_decode($sitems['title']).'\','.$sitems['uniquevist'].']';

		

		

		

		if($i == 0)

		{

			$datasubarray[]=array_slice($subarray,$i,$i+2);

		}

		else

		{	

			$datasubarray[]=array_slice($subarray,$i,$i+1);

		}

		

		$i++;

		//$dataarray[]='[\'Views\','.$sitems['uniquevist'].']';

	}

	

	$graphvalue=implode(',',$graph_dataarray);

	

}



//echo $graphvalue;exit;





$conversionarray=array();

if(count($datasubarray) > 0)

{

	array_pop($datasubarray);

	foreach($datasubarray as $dropofdata)

	{

		$firstval=$dropofdata[0]['uniquevist'];

		$endval=$dropofdata[1]['uniquevist'];

		

	//echo $firstval.'=>'.$endval;

		//echo '=>';

		$cval=0;

		if(intval($firstval) > 0)

		{

		$cval=(($firstval-$endval)/$firstval)*100;

		}

		$conversionarray[]=$cval;

		

		/*if($firstval > $endval)

		{

			if(intval($firstval) > 0)

			{

				$cval=($endval-$firstval)/$firstval*100;

				//$cval=($firstval/$endval)*100;

				$conversionarray[]=$cval;

			}

			

		}

		else

		{

		

			if(intval($endval) > 0)

			{

			$cval=($firstval-$endval)/$endval*100;

			//$cval=($endval/$firstval)*100;

			

			$conversionarray[]=$cval;

			

			}

		}*/



	}

}

$chkconversionarray=$conversionarray;



asort($conversionarray);

//

//$dropofval=array_slice($conversionarray,0,1);



$dropofval=end($conversionarray);



$dropkey = array_search($dropofval, $chkconversionarray);



/*

echo '<pre>';print_r($dropofval);

echo '<pre>';print_r($chkconversionarray);



echo '<pre>';print_r($conversionarray);*/



$firstval=array_slice($subarray,0,1);

$endval=end($subarray);







$firstval=$firstval[0]['uniquevist'];

$endval=$endval['uniquevist'];







if($firstval > $endval)

{

	if(intval($firstval) > 0)

	{

	//	$conversion=($endval-$firstval)/$firstval*100;

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









?>



<hr />

<div style="width:100%;">



<div style="margin:20px;">

<h3><?php echo $reports[0]->report_name;?></h3>

<p style="font-size: 16px;">This will show you which pages are working and which need modification. </p>

<p style="font-size: 16px;">This will help you know what to work on to gain more sales.</p>

<p style="text-align:right"><?php echo count($subarray);?> Pages Funnel</p>

<p style="text-align:right"><?php echo $tuniquevisit;?> Unique Visits</p>

<p style="text-align:right"><?php echo $rtuniquevisit;?> return Visits</p>

</div>

<div style="width:100%">

<div style="float:left; width:60%">

<div style="text-align:center;"><div id="funnelContainer"></div></div>

<p style="font-size: 26px;font-weight: normal;text-align: center;"><?php echo number_format($conversion,2);?>% Conversion</p>

<script type="text/javascript">

//  var data = [['Video Views', 1500], ['Comments', 300], ['Video Responses', 150]];

 var data = [<?php echo $graphvalue;?>];

    var chart = new FunnelChart(data, 650, 450, 1/4);

    chart.draw('#funnelContainer', 2);

</script>

</div>

<div style="float:left;  width:20%">

<div class="newimg" id="dropoffpoint" >

<p style="font-size: 22px;font-weight: normal;text-align: center;">Your biggest drop off spot</p>

<p style="font-size: 22px;font-weight: normal;text-align: center;">is right here!</p>

<p style="font-size: 15px;font-weight: normal;text-align: center;"><?php echo number_format($dropofval,2);?>% of your traffic</p>

<p style="font-size: 15px;font-weight: normal;text-align: center;">abandons your funnel</p>



<p style="font-size: 15px;font-weight: normal;text-align: center;"><img style=" margin-left: -217px;" src="<?php echo plugins_url("assets/images/right-arrow.png", __FILE__);?>" /></p>

</div>





</div>









</div>

</div>





<script type="text/javascript">

$s = jQuery.noConflict(); 

$s(document).ready(function(){



setTimeout(setDropofpoint,3500);



  

});



function setDropofpoint()

{



	  var p = $s("#fgs<?php echo $dropkey;?>");

    var position = p.offset();   

	

/*x=position.left-100;

y=position.top-100;

$s("#dropoffpoint").css({left:x,top:y});*/



y=position.top-250;

$s("#dropoffpoint").css({top:y});





	

  

}

</script>
