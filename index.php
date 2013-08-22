<html>
<head>
	<title>PHP Calendar</title>
	<?php 
		include_once('inc/bundles.php');
	?>
</head>
<body>
	<div id="calContainer"></div>
	<div id="calItem"></div>
</body>
<script>
	$(document).ready(function(){
		calnext('<?php echo date('y') ?>', '<?php echo date('m') ?>');

		$("#calItem").dialog({
		      autoOpen: false,
		      height: 500,
		      width: 1000,
		      modal: true,
		      open: function(e) {

		      }
	    });
	});

	function ShowCalItem(_y,_m,_d,_item){
		$("#calItem").dialog("open");
		var _url = "<?php echo 'calendar/items'; ?>";
		$.ajax({
            "type":"POST",
            "url":_url,
            "data":'year='+_y+'&month='+_m+'&day='+_d+'&type='+_item,
            "dataType":"html",
            "success":function(htmldata){
            	$("#calItem").html(htmldata);
            }
        });
	}

	function calprev(){
		var _month = parseInt($('#hdnMonth').val())-1;
		var _year = parseInt($('#hdnYear').val());
		
		if(_month==0){
			_year = parseInt($('#hdnYear').val())-1;
			_month = 12;
		}
		var url='calview.php?year='+_year+'&month='+_month;
		//window.open(url);
		$.post( url,{},
		  function( data ) {
			  $('#calContainer').html( data );
		  }
		);
  	}
	
	function calnext(cy, cm){
		var _month = parseInt($('#hdnMonth').val())+1;
		var _year = parseInt($('#hdnYear').val());
		if(cy==0 && cm==0){
			if(_month>12){
				_year = parseInt($('#hdnYear').val())+1;
				_month = 1;
			}
		}else{
			_month = cm;
			_year = cy;
		}
		var url='calview.php?year='+_year+'&month='+_month;
		//window.open(url);
		$.post( url,{},
		  function( data ) {
			  $('#calContainer').html( data );
		  }
		);
  	}
</script>
</html>