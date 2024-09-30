<style type="text/css" media="screen">
    .ui-icon.myicon {
        width: 16px;
        height: 16px;
        background-image:url('<?= base_url() ?>images/toolbar/Excel-icon2.png')
    }
</style>

<div class="clear"></div>

<section id="grid_container">
    <table id="list"></table>
    <div id="pager"></div>
</section>

<form method="post" action="<?= base_url() . 'index.php' . $from . 'csvExport' ?>">
    <input type="hidden" name="csvBuffer" id="csvBuffer" value="" />
</form> 

<div id="dialog-modal" title="Details" style="display: none;"></div>


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
            url:"<?=base_url().'index.php'.$from.'gridDataVqoh'?>",
             datatype: "json",
             postData: {
                sku: <?=$sku?>,
                cat: <?=$cat?>,
            },
            colNames:<?= $headers ?>,
            colModel:<?= $body ?>,
            height:250,
            autowidth: true,
	        loadonce: true,
            pager: '#pager',
            rowNum: 1000,
            rowList: [1000,1500,2000],
            rownumbers: true,
            sortname: 'id',
            sortorder: 'desc',
            viewrecords: true,
            caption: "<?= $caption ?>",
    	    toppager:true,
        });
        jQuery("#list").jqGrid('navGrid','#pager',{cloneToTop:true,edit:false,add:false,del:false,search:false,refresh:true});

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
    }
    

    
    function exportExcel(grid)
    {
        var mya=new Array();
        mya=$(grid).getDataIDs();  // Get All IDs
        var data=$(grid).getRowData(mya[0]);     // Get First row to get the labels
        var colNames=new Array(); 
        var ii=0;
        for (var i in data){colNames[ii++]=i;}    // capture col names
        var html="";
        for(i=0;i<mya.length;i++)
        {
            data=$(grid).getRowData(mya[i]); // get each row
            for(j=0;j<colNames.length;j++)
            {
                html=html+data[colNames[j]]+"\t"; // output each column as tab delimited
            }
            html=html+"\n";  // output each row with end of line

        }
        html=html+"\n";  // end of line at the end
        document.forms[1].csvBuffer.value=html;
        document.forms[1].method='POST';
        document.forms[1].action='<?= base_url() . 'index.php' . $from . 'csvExportHistory/history' ?>'; // send it to server which will open this contents in excel file
        document.forms[1].target='_blank';
        document.forms[1].submit();
    }

   function linksku(cellvalue, options,rowData){         
    if (cellvalue!=null){
        return '<a id="t'+cellvalue+'" href="#"  onclick="showDialog('+cellvalue+',\'<?=base_url()?>\');">' + cellvalue + '</a>'; 
    }else{
        return '';
    }
}

    
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
