
<script type="text/javascript">

    $(document).ready(function(){
     

	jQuery("#backOrders").jqGrid({ 
	    url:'<?= base_url() . 'index.php' . $showpoData.$sku ?>',  
	    datatype: "json",  
	    colNames: ['Vendor','PO #','PO Qty','PO Received','Back Order','Order Date','Expected Date','AZ Order#'], 
	    colModel: [ 
		{name:'vendor',index:'vendor', width:160, align:'left'},
		{name:'PO#',index:'PO#', width:50, align:'center'},
		{name:'PoQty',index:'PoQty', width:50, align:'center', formatter:'number', formatoptions:{decimalSeparator:'.',thousandsSeparator: ',', decimalPlaces: 2, prefix: '' }},
		{name:'PoReceived',index:'PoReceived', width:50, align:'center', formatter:'number', formatoptions:{decimalSeparator:'.',thousandsSeparator: ',', decimalPlaces: 2, prefix: '' }}, 
		{name:'backorder',index:'backorder', width:50, align:'center', formatter:'number', formatoptions:{decimalSeparator:'.',thousandsSeparator: ',', decimalPlaces: 2, prefix: '' }},
		{name:'orderdate',index:'orderdate', width:80, align:'center',formatter :'date',formatoptions: { newformat: 'M j Y'}},
		{name:'expecteddate',index:'expecteddate', width:80, align:'center',formatter :'date',formatoptions: { newformat: 'M j Y'}},
		{name:'OrderNo',index:'OrderNo', width:60, align:'left'},
	    ], 
	    width:880,
	    height: 200,
	    rowNum:50, 
	    pager: "#backpager", 
	    caption: '<?= $caption?>',
	    sortname: 'vendor', 
	    sortorder: "asc", 
	    rownumbers: true               
	});
	jQuery("#BackOrders").jqGrid('navGrid',"#backpager",{edit:false,add:true,del:false}) 

    });
    
</script>

<table id="backOrders"></table>
<div id="backpager"></div> 
