
<style type="text/css" media="screen">
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


<div class="clear"></div>

<section id="grid_container">
    <table id="<?= $nameGrid ?>"></table>
    <div id="<?= $namePager ?>"></div> 
</section>

<form method="post" action="<?= base_url() . 'index.php' . $from . '/csvExport' ?>">
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
            rowNum:50,
            rowList:[50,300,1000,100000000],
            loadComplete: function() { $("option[value=100000000]").text('ALL');},
            colNames:<?= $colNames ?>,
            colModel:<?= $colModel ?>,
            pager: jQuery('<?= '#' . $namePager ?>'),
            viewrecords: true,
            rownumbers: true,
            sortorder: '<?= $sort ?>',
            caption: '<?= $caption ?>',
            height: 600, 
            toppager:true,
            subGrid: <?= $subgrid ?>,
            cellEdit: true,
            editurl:'<?= base_url() . 'index.php/' . $from . 'dataEdit' ?>',
            cellurl: '<?= base_url() . 'index.php/' . $from . 'cellDataEdit' ?>',
            afterSaveCell:function() {
                var index=$(myGrid).getDataIDs();  // Get All IDs
                var data=$(myGrid).getRowData(index[arguments[3] -1]);
                if (data['Projection']){
                    data['Projection']=data['Projection'].replace('<div class="colorBlock" style="background-color: red; color: white; font-weight: bold;">','');
                    data['Projection']=data['Projection'].replace('</div>','');

                    if (data['TargetInventory'] > data['Projection']){
                        data['Projection']=data['Projection'].replace(data['Projection'], '<div class="colorBlock" style="background-color: red; color: white; font-weight: bold;">'+data['Projection']+'</div>');
                    }else{
                        data['Projection']=data['Projection'].replace(data['Projection'], '<div class="colorBlock" style="background-color: green; color: white; font-weight: bold;">'+data['Projection']+'</div>');
                    }                    
                }

                jQuery(myGrid).jqGrid().trigger("reloadGrid");
                jQuery(myGrid).jqGrid().trigger("reloadGrid");


            },
            subGridOptions: { "plusicon" : "ui-icon-triangle-1-e", "minusicon" : "ui-icon-triangle-1-s", "openicon" : "ui-icon-arrowreturn-1-e" },
            subGridRowExpanded: function(subgrid_id, row_id) {
 
               // var rowData = jQuery(myGrid).jqGrid().getRowData(row_id);
               // var cat_id = rowData['categoryID'];

                $.ajax({
                    url: '<?= base_url() . "index.php/" . $from . "showSkuData/" ?>'+row_id,
                    type: "GET",
                    success: function(html) {
                        $("#" + subgrid_id).append(html);
                    }
                });
            }
        });
        //Ajustamos el grid a la ventana
        resize_the_grid(myGrid);    
        // agregamos el boton de excel
        add_top_bar(myGrid);
    });

    
   
    $(window).resize(resize_the_grid);
    
    function add_top_bar(grid){
        jQuery(grid).jqGrid('navGrid','<?= '#' . $namePager ?>',{cloneToTop:true,edit:true,add:false,del:false,search:false,edittext: '<span class="ui-pg-button-text"> Edit SKU </span>'},
            {//edit
                recreateForm:true,
                jqModal:false,
                reloadAfterSubmit:false,
                closeAfterEdit:true,
                savekey: [true,13],
                closeAfterEdit:true,
                zIndex:1000,
                width: 450, 
                beforeInitData: function(formid) {
                    jQuery(grid).jqGrid('setColProp','TargetInventory',{editable:false});
                },
                afterShowForm: function (formid) {
                    jQuery(grid).jqGrid('setColProp','TargetInventory',{editable:true});
                },
            }, 
            {} //add
            );
        jQuery(grid).jqGrid('navButtonAdd', '#' + jQuery(grid)[0].id + '_toppager_left', { // "#list_toppager_left"
            caption: "Excel",
            buttonicon: 'excel',
            onClickButton: function(){
                exportExcel(grid);
            }
	     
        });
	if (<?= $backordersButton ?> == 1){
	
	    jQuery(grid).jqGrid('navButtonAdd', '#' + jQuery(grid)[0].id + '_toppager_left', { // "#list_toppager_left"
		id: "Asind_"+ jQuery(grid)[0].id +"_top", 
		title:"BackOrders", 
		caption: "Back Orders",
		buttonicon: 'orders',
		onClickButton: function(){
		    var selRowId = jQuery(grid).jqGrid('getGridParam','selrow');
		    var sku = jQuery(grid).jqGrid('getCell', selRowId, 'ID');
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
	
	
	
        var topPagerDiv = $('#' + jQuery(grid)[0].id + '_toppager')[0];         // "#list_toppager"
      //  $("#edit_" + jQuery(grid)[0].id + "_top", topPagerDiv).remove();        // "#edit_list_top"
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
                if (colNames[j]=='Projection'){               
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
        document.forms[1].action='<?= base_url() . 'index.php/' . $from . 'csvExport/' . str_replace(array( '(', ')' ), '',$export) ?>'; //TRabajar en esto;;;
        document.forms[1].target='_blank';
        document.forms[1].submit();
    }



   


</script>

<script type="text/javascript"> 
    function projection(cellvalue, options,rowData) {
        
    
    
        if ((cellvalue > rowData[8])) {
            return '<div class="colorBlock" style="background-color: green; color: white; font-weight: bold;">'+ cellvalue +'</div>';
        }
        if (cellvalue <= rowData[8]) {
            return '<div class="colorBlock" style="background-color: red; color: white; font-weight: bold;">'+ cellvalue +'</div>';
        }
        
    }
    
     
    function statistics(cellvalue, options,rowData) {  

        var sku = rowData[0];
        return "<a href= '<?= base_url() ?>index.php/Catalog/statistics/"+sku+"' OnClick=\"popupform(this, '"+sku+"')\" ><img src=\"<?= base_url() . 'images/toolbar/' ?>graph-icon.png\" alt="+rowData[0]+" /></a>";   
    }
    

    function formatLink(cellvalue, options,rowData) {
        if (cellvalue!=0){  
            return "<a href=<?= base_url()  . 'index.php/Reports/purchaseorderlist/'?>"+'?q='+ rowData[0]+'&p='+ 0 +'&t='+ 1 +" target='_blank'>" + cellvalue + "</a>";
        }  
        return cellvalue;s
    }


    function skuqty(cellvalue, options,rowData) {
        if (cellvalue!=0){  
            return "<a href=<?= base_url()  . 'index.php/Reports/skuqty/fbaqty/"+ rowData[0]+"'?> target='_blank'>" + cellvalue + "</a>";
        }  
        return cellvalue;
    }
    
    function fbainqty(cellvalue, options,rowData) {
        if (cellvalue!=0){  
            return "<a href=<?= base_url()  . 'index.php/Reports/skuqty/fbainqty/"+ rowData[0]+"'?> target='_blank'>" + cellvalue + "</a>";
        }  
        return cellvalue;
    }

    function fbapenqty(cellvalue, options,rowData) {
        if (cellvalue!=0){  
            return "<a href=<?= base_url()  . 'index.php/Reports/skuqty/fbapenqty/"+ rowData[0]+"'?> target='_blank'>" + cellvalue + "</a>";
        }

        return cellvalue; 
    }

    function backorders(cellvalue, options,rowData) {
        if (cellvalue!=0){
            return "<a href=<?= base_url()  . 'index.php/Reports/purchaseorderlist/"+ rowData[0]+"'?> target='_blank'>" + cellvalue + "</a>";
        }

        return cellvalue;
    }


    
    
</script>
<!--base_url()  . 'index.php' .$backorders -->
<script type="text/javascript"> 
    $(function() {
        $( ".date-pick" ).datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat:"mm/dd/yy"
        });
    });   
    
</script>


