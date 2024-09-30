
<br>
<div class="clear"></div>

<section id="grid_container">
    <table id="pohistory"></table>
    <div id="pohistorypager"></div>
</section>

<script type="text/javascript">
    
    function resize_the_grid()
    {
	$("#pohistory").fluidGrid({base:'#grid_wrapper', offset:-20});
    }

    $(document).ready(function(){
     
	jQuery("#pohistory").jqGrid({ 
	    url:'<?= base_url() . 'index.php' . $getData . $sku ?>',  
	    datatype: "json",  
	    colNames: ['SKU','PO #','Date Issued','Date Expected','Qty Ordered','Qty Received','Qty Missing','Fully Received?'], 
	    colModel: [ 
		{name:'SKU',index:'SKU',  align:'left'},
		{name:'PO',index:'PO', align:'center'},
		{name:'DateIssued',index:'DateIssued',  align:'center'},
		{name:'DateExpected',index:'DateExpected',  align:'center'}, 
		{name:'QtyOrdered',index:'QtyOrdered',  align:'center', formatter:'number', formatoptions:{decimalSeparator:'.',thousandsSeparator: ',', decimalPlaces: 2, prefix: '' }},
		{name:'QtyReceived',index:'QtyReceived',  align:'center', formatter:'number', formatoptions:{decimalSeparator:'.',thousandsSeparator: ',', decimalPlaces: 2, prefix: '' }},
		{name:'QtyMissing',index:'QtyMissing',  align:'center', formatter:'number', formatoptions:{decimalSeparator:'.',thousandsSeparator: ',', decimalPlaces: 2, prefix: '' }},
		{name:'FullyReceived',index:'FullyReceived', align:'center', 
		    formatter: function (cellvalue, rowObject, action) {if(cellvalue === 1){return "Yes"} else {return "No"}}},

	    ], 
	    autowidth: true,
	    height:250,
	    rowNum:50, 
	    pager: "#pohistorypager", 
	    caption: '<?= $caption ?>',
	    sortname: 'vendor', 
	    sortorder: "asc", 
	    rownumbers: true               
	});
	jQuery("#pohistory").jqGrid('navGrid',"#pohistorypager",{edit:false,add:true,del:false}) 
	resize_the_grid();
    });
    $(window).resize(resize_the_grid);  
    
</script>


