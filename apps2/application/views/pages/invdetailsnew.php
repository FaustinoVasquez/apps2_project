<style type="text/css" media="screen">
    .ui-icon.excel {
        width: 16px;
        height: 16px;
        background-image:url('<?= base_url() ?>images/toolbar/Excel-icon2.png');
    }
</style>
 


<div class="clear"></div> 
<br>
<section id="grid_container">
    <table id="list"></table>
    <div id="pager"></div>
</section>


<script type="text/javascript">
    
    function resize_the_grid()
    {
        $("#list").fluidGrid({base:'#grid_wrapper', offset:-20});
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
            height:250,
            autowidth: true,
            pager: '#pager',
            rowNum: 1000,
            rowList: [1000,1500,2000],
            rownumbers: true,
            sortname: 'Warehouse',
            sortorder: 'desc',
            viewrecords: true,
            caption: "<?= $caption ?>",
	        toppager:true,
            subGrid: true,
            subGridOptions: { "plusicon" : "ui-icon-triangle-1-e", "minusicon" : "ui-icon-triangle-1-s", "openicon" : "ui-icon-arrowreturn-1-e" },
            subGridRowExpanded: function(subgrid_id, row_id) {
               var subgrid_table_id, pager_id; 
               var data = jQuery("#list").jqGrid('getRowData',row_id);

               subgrid_table_id = subgrid_id+"_t"; 
               pager_id = "p_"+subgrid_table_id;
               $("#"+subgrid_id).html("<div style='background-color:#363737;width:100%;'>\n\
                                          <center>\n\
                                            <br>\n\
                                              <table id='"+subgrid_table_id+"' class='scroll'></table>\n\
                                            <br>\n\
                                         </center>\n\
                                       </div>\n\
                                     <div id='"+pager_id+"' class='scroll'></div>"
                                     ); 
                   jQuery("#"+subgrid_table_id).jqGrid({ 
                       url:'subGridData',  
                       datatype: "json", 
                       postData: {sku: data.id,
                                  wh: data.Warehouse }, 
                       colNames: ['SKU','BinID','Qty','Location'], 
                   colModel: [ 
                        {name:'SKU',index:'SKU', width:100, align:'center',hidden:true},
                        {name:'BinID',index:'BinID', width:100, align:'center'},
                        {name:'Qty',index:'Qty', width:100, align:'center'},
                        {name:'Location',index:'Location', width:100, align:'center'},
                   ], 
                       width:700,
                       rowNum:20, 
                       pager: pager_id, 
                       caption: "Warehouse Bin Information",
                       sortname: 'SKU', 
                       sortorder: "asc", 
                       height: '100%'
                       
                   });
              jQuery("#"+subgrid_table_id).jqGrid('navGrid',"#"+pager_id,{edit:false,add:true,del:false})
                }
        });
        jQuery("#list").jqGrid('navGrid','#pager',{cloneToTop:true,edit:false,add:false,del:false,search:false,refresh:true});
	   resize_the_grid();

    });
    
    $(window).resize(resize_the_grid);
        
</script>

<script type="text/javascript">   
    $(function() {
        $( ".date-pick" ).datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat:"mm/dd/yy"
        });
    });   
         
</script>
