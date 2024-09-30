
<br>
<section id="grid_container">
    <table id="list"></table>
    <div id="pager"></div>
    <table id="list1"></table>
    <div id="pager1"></div>
</section>


<script type="text/javascript">
    
    function resize_the_grid()
    {
        $("#list").fluidGrid({base:'#grid_wrapper', offset:-20});
         $("#list1").fluidGrid({base:'#grid_wrapper', offset:-20});
    }
     
     $(function(){
         var myGrid= $("#list"),
            pagerSelector = "#pager", 
            myAddButton = function(options) {
                myGrid.jqGrid('navButtonAdd',pagerSelector,options);
                myGrid.jqGrid('navButtonAdd','#'+myGrid[0].id+"_toppager",options);
            };
      
        myGrid.jqGrid({
            url:'gridData',
            datatype: "json",
            postData: {
            sku: <?=$search?>},
            colNames:<?= $headers ?>,
            colModel:<?= $body ?>,
            height:75,
            autowidth: true,
            rowNum: 50,
            rowList: [50,100],
            rownumbers: true,
            sortname: 'Sold',
            sortorder: 'desc',
            viewrecords: true,
            caption: "<?= $caption ?>",
	         
        });
       // jQuery("#list").jqGrid('navGrid','#pager',{cloneToTop:true,edit:false,add:false,del:false,search:false,refresh:true});
	   resize_the_grid();

    });


 $(function(){
         var myGrid1= $("#list1"),
            pagerSelector1 = "#pager1", 
            myAddButton1 = function(options1) {
                myGrid1.jqGrid('navButtonAdd',pagerSelector1,options1);
                myGrid1.jqGrid('navButtonAdd','#'+myGrid1[0].id+"_toppager",options1);
            };
      
        myGrid1.jqGrid({
            url:'subGridData',
            datatype: "json",
            postData: {
            sku: <?=$search?>},
            colNames:<?= $headers1 ?>,
            colModel:<?= $body1 ?>,
            height:150,
            autowidth: true,
          
            rowNum: 50,
            rowList: [50,100],
            rownumbers: true,
            sortname: 'SKU',
            sortorder: 'desc',
            viewrecords: true,
            caption: "<?= $caption1 ?>",
       
        });
        jQuery("#list1").jqGrid('navGrid','#pager1',{cloneToTop:true,edit:false,add:false,del:false,search:false,refresh:true});
     resize_the_grid();

     jQuery("#list1").jqGrid('filterToolbar');

    });

    
    $(window).resize(resize_the_grid);
        
</script>