
<?php
$formopen = array('id' => 'target', 'id' => "omc_form", 'class'=>'myform');
echo form_open(base_url() . 'index.php' . $from, $formopen);


?>
<fieldset class="bluegradiant">
    <div class="logo"><img longdesc="MI Technologies"
                           title="Mi Tech" alt="43" src="<?= base_url() ?>/images/header/mitechnologies.png">
    </div>
    <div class="form-data"> 
        <?php

        $inputadjustment = array('id'=>'adjustmentid', 'name' => 'adjustment', 'value' => $adjustment, 'autocomplete' => 'on', 'maxlength' => '250', 'size' => '20', 'onclick' => "this.value=''");
        echo "Adjustment:" . form_input($inputadjustment);
 
        echo "&nbsp;&nbsp;";
        $submit = array('name' => 'send', 'value' => 'Submit', 'type' => 'submit', 'class' => 'button');
        echo form_input($submit);

        $reset = array('name' => 'reset', 'value' => 'Reset', 'type' => 'submit', 'class' => 'button');
        echo form_input($reset);


        ?>
    </div>
</fieldset>
<br>
<?php
echo form_close();
?>


<style type="text/css" media="screen">
    .ui-icon.excel {
        width: 16px;
        height: 16px;
        background-image:url('<?= base_url() ?>images/toolbar/Excel-icon2.png');
    }
    .ui-icon.scanicon {
        width: 16px;
        height: 16px;
        background-image:url('<?= base_url() ?>images/toolbar/scanicon.png');
    }
</style>


<div class="clear"></div>

<section id="grid_container">
    <table id="<?= $nameGrid ?>"></table>
    <div id="<?= $namePager?>"></div> 
</section>

 <form method="post" action="<?= base_url() . 'index.php/' . $from . '/csvExport' ?>">
    <input type="hidden" name="csvBuffer" id="csvBuffer" value="" />
</form>  



<script type="text/javascript">
    

    function resize_the_grid()
    {
        $("<?='#'.$nameGrid ?>").fluidGrid({base:'#grid_wrapper', offset:-20});
    }
    

    $(document).ready(function(){

        var myGrid = $("<?='#'.$nameGrid ?>");

        myGrid.jqGrid({
            url:'<?= base_url() . 'index.php' . $from . $gridSearch ?>',
            datatype: "json", 
            rowNum:50,
            rowList:[50,300,1000],
            colNames:<?= $colNames ?>,
            colModel:<?= $colModel ?>,
            pager: jQuery('<?='#'.$namePager ?>'),
            viewrecords: true,
            rownumbers: true,
            sortname: 'ProductCatalogID',
            sortorder: '<?= $sort ?>',
            caption: '<?= $caption ?>',
            height: 600, 
            toppager:true,
        });
        //Ajustamos el grid a la ventana
        resize_the_grid(myGrid);    
       // agregamos el boton de excel
        add_top_bar(myGrid);
 
    });
    
     
    $(function() {   
      
	$("#dialog").dialog({
	    autoOpen: false,
	    resizable: false,
	    height:275,
	    width:505,
	    modal: true,
	    buttons: {
	    "Create": {
		text: "Create",
		id: "ButtonSend",
	        click: function(){
                       
                       var mydata= $("#scan").serialize();
                        
			var request = $.ajax({
			    url:"saveItem",
			    type: "POST",
			    data: mydata
			});

			request.done(function() {
			    var qty = $("#qty").val()
			    qty--;
			    $("#qty").val(qty);
			    $('#myItem').val('');
			});
			if ($("#qty").val() == 1){
				 $( this ).dialog( "close" ); 
                                 
                                 reload();
				}
			}
		    },
	    Cancel: function() {
		$( this ).dialog( "close" );
		}
	    }
	});
	
     });   
     
   function reload(result) {
    $("<?='#'.$nameGrid ?>").trigger("reloadGrid"); 
    } 
   
   
   $(window).resize(resize_the_grid);
    
     function add_top_bar(grid){
     jQuery(grid).jqGrid('navGrid','<?='#'.$namePager ?>',{cloneToTop:true,add:false,edit:false,del:false});
        jQuery(grid).jqGrid('navButtonAdd', '#' + jQuery(grid)[0].id + '_toppager_left', { // "#list_toppager_left"
            caption: "Excel",
            buttonicon: 'excel',
            onClickButton: function(){
                exportExcel(grid);
            }
        });
        
         jQuery(grid).jqGrid('navButtonAdd', '#' + jQuery(grid)[0].id + '_toppager_left', { // "#list_toppager_left"
            caption: "Scan Items",
            buttonicon: 'scanicon',
            onClickButton: function(){
		    var selRowId = jQuery(grid).jqGrid('getGridParam','selrow');
                    var sku = jQuery(grid).jqGrid('getCell', selRowId, 'ProductCatalogID');
                    var Qty = jQuery(grid).jqGrid('getCell', selRowId, 'Quantity');
		    var Serials = jQuery(grid).jqGrid('getCell', selRowId, 'Serials');
		    
                    $('#myadjustment').val($("#adjustmentid").val());
                    $("#mysku").val(sku);
		    $("#qty").val(Qty);
                    
		    if ( sku != false){
                        if ( Qty < Serials){
                            
                            alert("Plase Report This Error..");
                            
                        }else{
                            
                            if(Qty == Serials){
                                
                                alert("Process Completed"); 
                                
                            }else{
                                
                               $( "#dialog" ).dialog( "open" );   
                            }
                        }
                        
		    }else alert("Please Select Row");
                }
        });
      
        
        
        var topPagerDiv = $('#' + jQuery(grid)[0].id + '_toppager')[0];         // "#list_toppager"
        $("#edit_" + jQuery(grid)[0].id + "_top", topPagerDiv).remove();        // "#edit_list_top"
        $("#del_" + jQuery(grid)[0].id + "_top", topPagerDiv).remove();         // "#del_list_top"
        $("#search_" + jQuery(grid)[0].id + "_top", topPagerDiv).remove();      // "#search_list_top"
        $("#refresh_" + jQuery(grid)[0].id + "_top", topPagerDiv).remove();     // "#refresh_list_top"
        $("#add_" + jQuery(grid)[0].id + "_top", topPagerDiv).remove();         // "#add_list_top"
        $("#view_" + jQuery(grid)[0].id + "_top", topPagerDiv).remove();         // "#add_list_top"
    }
  
    
    function exportExcel(grid)
    {
        var mya=new Array();
        mya=$(grid).getDataIDs();  // Get All IDs
        var data=$(grid).getRowData(mya[0]);     // Get First row to get the labels
        if (data['stats']){delete data['stats']}
        var colNames=new Array();
        var ii=0;
        for (var i in data){colNames[ii++]=i;}    // capture col names
        var html="";
        for(k=0;k<colNames.length;k++)
        {
            html=html+colNames[k]+"\t";     // output each Column as tab delimited
        }
        html=html+"\n";                    // Output header with end of line
        for(i=0;i<mya.length;i++)
        {
            data=$(grid).getRowData(mya[i]); // get each row
            for(j=0;j<colNames.length;j++)
            {   
               if (colNames[j]=='OrderNumber'){               
                    data[colNames[j]]=data[colNames[j]].toUpperCase();
                     var startPos=(data[colNames[j]].indexOf('>')) +1;
                     var endPos=(data[colNames[j]].indexOf('<',startPos));
                    data[colNames[j]]=data[colNames[j]].toString().substring(startPos,endPos);
                }
 
                html=html+data[colNames[j]]+"\t"; // output each Row as tab delimited
            }

            html=html+"\n";  // output each row with end of line
        }

        html=html+"\n";  // end of line at the end
        document.forms[1].csvBuffer.value=html;
        document.forms[1].method='POST';
        document.forms[1].action='<?= base_url() . 'index.php' . $from . 'csvExport/'.$export?>'; //TRabajar en esto;;;
        document.forms[1].target='_blank';
        document.forms[1].submit();
    }
   
   
   $(function() {
        $( ".date-pick" ).datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat:"mm/dd/yy"
        });
    }); 
    
</script>


<div id="dialog" title="Scan Orders" style="font-size:30px">
    <form id="scan">
	    <label for="myItem"  style="font-size:30px">Item:</label>
	    <input type="text" name="myItem" id="myItem"  class="text ui-widget-content ui-corner-all" style="width: 350px" />
                <input type="text" name="myAdjustment" id="myadjustment"  class="text ui-widget-content ui-corner-all" style="font-size: 9px;visibility:hidden;" />	
                <input type="text" name="mySku" id="mysku"  class="text ui-widget-content ui-corner-all" style="font-size: 9px;visibility:hidden;" />
    </form>
   
	<label for="ItemRemainig" style="font-size:30px">Remaining:</label>
	<input type="text" name="qty" id="qty"  class="text ui-widget-content ui-corner-all" style="text-align: center; width: 100px" readonly />
</div>

<script>
			    
$(function(){
    {
	$('#myItem').keypress(function(event) {
	    
	    if(event.which == 13){
		event.preventDefault();
		$('#ButtonSend').click();
	    }
	});
    }
});

</script>