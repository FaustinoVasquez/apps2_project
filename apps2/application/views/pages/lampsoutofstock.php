
<style type = "text/css" media = "screen">


    .ui-icon.excel {
        width: 16px;
        height: 16px;
        background-image:url('<?= base_url() ?>images/toolbar/Excel-icon2.png');
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
	$inputfrom = array( 'id' => 'from' , 'name' => 'datefrom' , 'class' => 'date-pick' , 'value' => $datefrom , 'size' => '10' , 'onchange' => "this.form.submit()" , 'style' => 'width:80px;text-align:center' ) ;
		    echo "&nbsp;&nbsp;from:" . form_input( $inputfrom ) ;

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
            url:'<?= base_url() . 'index.php' . $from . $gridSearch ?>',
            datatype: "json",
            colNames:<?= $colNames ?>,
            colModel:<?= $colModel ?>,
            autowidth: true,
            pager: jQuery('<?= '#' . $namePager ?>'),
            rowNum: 50,
	    loadonce:true,
            sortname: '<?= $sortname?>',
	    sortorder: '<?= $sortorder?>',
            rowList:[50,300,100000000],
            loadComplete: function() { $("option[value=100000000]").text('ALL');},
            rownumbers: true,
            viewrecords: true,
            caption: "<?= $caption ?>",
            height: 600, 
            toppager:true,
	    editurl:'<?= base_url() . 'index.php/' . $from . 'saveData' ?>'
        });
	
        jQuery("<?= '#' . $nameGrid ?>").jqGrid('navGrid','<?= '#' . $namePager ?>',{cloneToTop:true,edit:false,add:false,del:false,search:false,refresh:true},
	     {reloadAfterSubmit:false, closeAfterEdit: true,closeOnEscape:true, recreateForm: true, width:450});

	resize_the_grid();
        add_top_bar(myGrid);
    });
    
    $(window).resize(resize_the_grid);
    
    
    function add_top_bar(grid){
      
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
			if (( BackOrders != '' ) && ( BackOrders != '0' )) {
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
    
      function statistics(cellvalue, options,rowData) {             
        var sku = rowData[0];
        return "<a href= '<?= base_url() ?>index.php/Catalog/statistics/"+sku+"' OnClick=\"popupform(this, '"+sku+"')\" ><img src=\"<?= base_url() . 'images/toolbar/' ?>graph-icon.png\" alt="+rowData[0]+" /></a>";   
    }
    
      $(function() {
        $( ".date-pick" ).datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat:"mm/dd/yy"
        });
    }); 

    function formatLink(cellvalue, options,rowData) {
        if (cellvalue!=null){  
            return "<a href=<?= base_url()  . 'index.php/Reports/purchaseorderlist/'?>"+'?q='+ rowData[0]+'&p='+ 0 +'&t='+ 1 +">" + cellvalue + "</a>";
        }  
    }
</script>

<div id="dialog" title="Back Orders"></div>
