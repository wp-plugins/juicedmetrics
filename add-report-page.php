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





.bootstrap-select.btn-group .btn .filter-option

{

	padding-left:10px;

}

.bootstrap-select:not([class*="span"]):not([class*="col-"]):not([class*="form-control"]):not(.input-group-btn)

{

	 width: 240px !important;

}



</style>



   <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>

    <script type="text/javascript" src="<?php echo plugins_url("assets/javascripts/bootstrap-select.js", __FILE__);?>"></script>

    <link rel="stylesheet" type="text/css" href="<?php echo plugins_url("assets/css/bootstrap-select.css", __FILE__);?>">



    <!-- 3.0 -->

    <link href="<?php echo plugins_url("assets/css/bootstrap.min.css", __FILE__);?>" rel="stylesheet">

    <script src="http://netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>



    <!-- 2.3.2

	    <link href="http://netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css" rel="stylesheet">

    <link href="http://netdna.bootstrapcdn.com/twitter-bootstrap/2.3.2/css/bootstrap-combined.min.css" rel="stylesheet">

    <script src="http://netdna.bootstrapcdn.com/twitter-bootstrap/2.3.2/js/bootstrap.js"></script>

    -->

    <script type="text/javascript">

        $(window).on('load', function () {



           /* $('.selectpicker').selectpicker({

                'selectedText': 'cat'

            });*/



            // $('.selectpicker').selectpicker('hide');

        });

    </script>

	

	

	 <!--  <link rel="stylesheet" href="<?php //echo plugins_url("assets/css/tablednd.css", __FILE__);?>" type="text/css"/>

	

<script type="text/javascript" src="//google-code-prettify.googlecode.com/svn/trunk/src/prettify.js"></script>-->

<!--<script type="text/javascript" src="<?php //echo plugins_url("assets/javascripts/jquery.tablednd.js", __FILE__);?>"></script>-->

<!--<script type="text/javascript">

    $(document).ready(function() {

        prettyPrint();

           $('#table-5').tableDnD({

            onDragStart: function(table, row) {

                $(table).parent().find('.result').text('');

            },

            onDrop: function(table, row) {

                var data = $(table).tableDnDSerialize();

				//alert(decodeURIComponent(data));

				var result=data.split("&");

				var res = new Array() 

				for (var i = 0; i < result.length; i++)

				{

					

					res[i]=result[i].replace("table-5%5B%5D=", ""); 

					

				}

				

	$.post("<?php //echo get_option('siteurl')?>/wp-admin/admin-ajax.php?refid=<?php //echo trim($_GET['report']);?>", {action:"ak_attach", "sorder": encodeURIComponent(res)}, function(str)	{

		for (var i = 0; i < res.length; i++){$('#td_'+res[i]).html(i+1);}

	});

	

                prettyPrint();

            },

            dragHandle: ".dragHandle"

        });



       





     



    });

</script>-->

<!--<style>

.dragHandle

{

	cursor:move;

}

</style>-->





<div class="setup">

<!--<h2>Juiced Metrics</h2>-->

<h2><img src="<?php echo plugins_url("assets/images/Juiced-Metrics-Green.png", __FILE__);?>" /></h2>

<hr />

<div style="width:85%;">



<div style="margin:20px;">

<h3>Why your business need a logo - Report</h3>

<p>Select the pages you would like to track with the sales cycle or your sales funnel.</p>

<p>It simple to setup, but if you get stuck then <a href="#">watch this video</a></p>



<div  style="   padding-left: 100px;">

<span>

<img src="<?php echo plugins_url("assets/images/l-r.png", __FILE__);?>" /> 

</span>

<div>

 <form id="trackform" name="trackform" action="<?php echo $mngpg;?>" method="post">

 <input type="hidden" name="addpostpage" value="addpostpage" />

  <input type="hidden" name="ref_id" value="<?php echo $_GET['report'];?>" />

    <input type="hidden" name="report_name" value="<?php echo urldecode($_GET['rname']);?>" />

  



<p><strong>Step 1</strong> Find the pages your want to track:



 <select id="id_select" name="page_id" class="selectpicker bla bla bli"  data-live-search="true">

 <?php

 foreach($results as $rec)

 {

 	echo '<option value="'.$rec['id'].'">'.$rec['title'].'</option>';

 }

 ?>

 

       

        

    </select>

</p>

<p style="text-align:center;"><a href="javascript:void(0);" onclick="formsubmit();"><img src="<?php echo plugins_url("assets/images/add_btn.png", __FILE__);?>" /></a></p>



</form>

<script type="text/javascript">

	function formsubmit()

	{

		document.trackform.submit();

	}

</script>

 </div>

</div>





<div style="clear:both;"></div>

<div class="salesdiv salestabel">



<!--<div class="tableDemo">

<table id="table-5" cellspacing="0" cellpadding="2">

    <tr id="table5-row-1"><td class="dragHandle">&nbsp;</td><td>1</td><td>One</td><td>some text</td></tr>

    <tr id="table5-row-2"><td class="dragHandle">&nbsp;</td><td>2</td><td>Two</td><td>some text</td></tr>

    <tr id="table5-row-3"><td class="dragHandle">&nbsp;</td><td>3</td><td>Three</td><td>some text</td></tr>

    <tr id="table5-row-4"><td class="dragHandle">&nbsp;</td><td>4</td><td>Four</td><td>some text</td></tr>

    <tr id="table5-row-5"><td class="dragHandle">&nbsp;</td><td>5</td><td>Five</td><td>some text</td></tr>

    <tr id="table5-row-6"><td class="dragHandle">&nbsp;</td><td>6</td><td>Six</td><td>some text</td></tr>

</table>

    <div class="result"></div>

</div>-->







<div class="tableDemo">

<table   id="table-5"  cellpadding="0" cellspacing="0" width="100%" border="0">

<tr>

<th width="10%">PAGE</th>

<!--<th width="10%">ORDER</th>

--><th width="60%">PAGE NAME</th>

<th width="20%">ACTIONS</th>

</tr>





<?php

	$i=1;

	foreach( $pageresult as $dn ):

	$val=$i%2;

	$class=($val) ? 'row1':'row2';

	

?>

<tr class="row1" id="table5-row-<?=$dn->ID;?>">

<td width="10%"  style="padding-left:20px;" id="td_<?php echo $dn->ID;?>"><?php echo $i;?></td>

<!--<td width="10%" ><img  class="dragHandle" src="<?php //echo plugins_url("assets/images/up-down-arrow.png", __FILE__);?>" /> </td>

--><td width="60%"><?php echo get_the_title($dn->page_id);?></td>

<td width="10%"><a target="_blank" href="<?php echo get_the_guid($dn->page_id);?>">View Page</a>&nbsp;&nbsp;<a href="<?php echo $mngpg.'&rname='.urlencode($_GET['rname']).'&report='.$_GET['report'].'&delete='.$dn->ID;?>">Remove Page</a></td>

</tr>

 <?php

   $i++;

	endforeach;

  ?>





<tr class="row2">

<td width="10%" colspan="4">&nbsp;</td></tr>

</table>

 <div class="result"></div>

</div></div>



</div>



</div>

</div>

