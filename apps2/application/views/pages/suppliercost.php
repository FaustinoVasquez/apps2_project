<style type="text/css" media="screen">

    .ui-icon.myicon {
        width: 16px;
        height: 16px;
        background-image:url('<?= base_url() ?>images/toolbar/Excel-icon2.png')
    }
    .ui-icon.orders {
        width: 16px;
        height: 16px;
        background-image:url('<?= base_url() ?>images/toolbar/cartadd.png');
    }
</style>
<?php
$formopen = array('id' => 'target', 'id' => "omc_form", 'class' => 'myform');
echo form_open(base_url() . 'index.php' . $from, $formopen);
?>
<fieldset class="bluegradiant">
    <div class="logo"><img longdesc="MI Technologies"
                           title="Mi Tech" alt="43" src="<?= base_url() ?>/images/header/mitechnologies.png">
    </div>
    <div class="form-data"> 
	<?php
	$inputsearch = array('name' => 'search', 'value' => $search, 'autocomplete' => 'on', 'maxlength' => '250', 'size' => '20', 'onclick' => "this.value=''");
	echo "Search:" . form_input($inputsearch);


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
</style>


<div class="clear"></div>

<section id="grid_container">
    <table id="<?= $nameGrid ?>"></table>
    <div id="<?= $namePager ?>"></div> 
</section>

<form method="post" action="<?= base_url() . 'index.php' . $from . 'csvExport' ?>">
    <input type="hidden" name="csvBuffer" id="csvBuffer" value="" />
</form>  



<script type="text/javascript">
    
    function resize_the_grid()
    {
        $("<?= '#' . $nameGrid ?>").fluidGrid({base:'#grid_wrapper', offset:-20});
    }
        
    $(document).ready(function(){

       

        var myGrid = $("<?= '#' . $nameGrid ?>");

        myGrid.jqGrid({
            url:'<?= base_url() . 'index.php/' . $from . $gridSearch ?>',
            datatype: "json",
            colNames:<?= $colNames ?>,
            colModel:<?= $colModel ?>,
            autowidth: true,
            pager: jQuery('<?= '#' . $namePager ?>'),
            rowNum: 50,
            sortname: 'ID',
            rowList:[50,300,100000000],
            loadComplete: function() { $("option[value=100000000]").text('ALL');},
            rownumbers: true,
            viewrecords: true,
            caption: "<?= $caption ?>",
            height: 600, 
            toppager:true,
            subGrid: <?= $subgrid ?>,
	    subGrisubGridOptions: { "plusicon" : "ui-icon-triangle-1-e", "minusicon" : "ui-icon-triangle-1-s", "openicon" : "ui-icon-arrowreturn-1-e" },
            subGridRowExpanded: function(subgrid_id, row_id) {
                
                var data=$("<?= '#' . $nameGrid ?>").getRowData(row_id); 
                
                $.ajax({
		    url: '<?= base_url() . "index.php" . $showskudata ?>'+data.ID,
                    type: "GET",
                    success: function(html) {
                        $("#" + subgrid_id).append(html);
                    }
                });
            }
             
        });
 
        jQuery("#list").jqGrid('navGrid','#pager',{edit:false,add:false,del:false,search:false,refresh:true});
        resize_the_grid();
        add_top_bar(myGrid);
    });
    
    $(window).resize(resize_the_grid);

    
    function add_top_bar(grid){
        jQuery(grid).jqGrid('navGrid','<?= '#' . $namePager ?>',{edit:false,add:false,del:false,search:false,refresh:true,cloneToTop:true},{},{width:600});
   
        jQuery(grid).jqGrid('navButtonAdd', '#' + jQuery(grid)[0].id + '_toppager_left', { // "#list_toppager_left"
            caption: "Excel",
            buttonicon: 'excel',
            onClickButton: function(){
                exportExcel(grid);
            }
        }); 
	
	
	
	jQuery(grid).jqGrid('navButtonAdd', '#' + jQuery(grid)[0].id + '_toppager_left', { // "#list_toppager_left"
	    id: "Asind_"+ jQuery(grid)[0].id +"_top", 
	    title:"BackOrders", 
	    caption: "Back Orders",
	    buttonicon: 'orders',
	    onClickButton: function(){
		var selRowId = jQuery(grid).jqGrid('getGridParam','selrow');
		var sku = jQuery(grid).jqGrid('getCell', selRowId, 'SKU');
		var BackOrders = jQuery(grid).jqGrid('getCell', selRowId, 'BackOrders');
		
		if ( sku != null){
		    if( BackOrders != '' ){
			$( "#dialog" ).dialog({
			    resizable: false,
			    height:375,
			    width:905,
			    modal: true,
			    buttons: {
				Cancel: function() {
				    $( this ).dialog( "close" );
				}
			    }
			});
		  
			$("#dialog").html('<object width="882" height="277"  data="<?= base_url() . "index.php/Catalog/showpo/" ?>'+sku+'">');
			$("#dialog").dialog("open")
		    }else alert("Back Order Empty");
		} else alert("Please Select Row");
	    }
	});
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
        document.forms[1].action='<?= base_url() . 'index.php' . $from . 'csvExport/' . $export ?>'; 
        document.forms[1].target='_blank';
        document.forms[1].submit();
    }

</script>

<div id="dialog" title="Purcharse Orders"></div>