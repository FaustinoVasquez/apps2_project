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

    <table id="list1"></table>
    <div id="pager1"></div>
</section> 


<script type="text/javascript">
    
    function resize_the_grid()
    {
        $("#list1").fluidGrid({base:'#grid_wrapper', offset:-20});
    }
     
    $(document).ready(function(){

        var myGrid = $("#list1");
      
        myGrid.jqGrid({
            url:'getData',
             postData: {
                sku: <?=$sku?>,
            },
            datatype: "json",
            colNames:<?= $colNames ?>,
            colModel:<?= $colModel ?>,
            height:50,
            autowidth: true,
            pager: '#pager1',
            rowNum: 1,
            rowList: [50],
            rownumbers: true,
            sortname: 'ID',
            viewrecords: true,
            caption: "<?= $caption ?>",
            cellEdit: true,
            cellurl: 'editCell', 
            afterEditCell: function (id,name,val,iRow,iCol){ 
                if(name=='DumpFBMEndDate') { 
                    jQuery("#"+iRow+"_DumpFBMEndDate","#list1").datetimepicker({
                        showOn: 'button',
                        buttonImage: 'ui-icon-calculator',
                        dateFormat:"mm/dd/yy",
                        changeMonth: true,
                        changeYear: true,
                    }); 
                };


                if(name=='DumpFBAEndDate') { 
                    jQuery("#"+iRow+"_DumpFBAEndDate","#list1").datetimepicker({
                        showOn: 'button',
                        buttonImage: 'images/icon-calendar.gif',
                        dateFormat:"mm/dd/yy",
                        changeMonth: true,
                        changeYear: true,
                    }); 
                };

                
            }, 
            afterSaveCell: function(rowid,name,val,iRow,iCol) {
                    myGrid.trigger('reloadGrid');
            },
        });
        jQuery("#list1").jqGrid('navGrid','#pager1',{cloneToTop:true,edit:false,add:false,del:false,search:false,refresh:true});

    	resize_the_grid();
    	
    });
    
    $(window).resize(resize_the_grid);
    
   
</script>

<script type="text/javascript">   
    $(function() {
        $( ".date-pick" ).datetimepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat:"mm/dd/yy"
        });
    });   
         
</script>