<style type="text/css" media="screen">

    .myicon {
        float:left;
        width: 16px;
        height: 16px;
        background-image:url('http://apps2.mitechnologiesinc.com/images/toolbar/Excel-icon2.png');
        margin-left: 20px;
        margin-top: 5px; 
    }


</style>

<br/>

<div class="clear"></div>

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
            url:'<?= base_url() . 'index.php' . $from . 'DataVendorfpcomp?q=' . $search?>',
            datatype: "json",
            colNames:<?= $headers ?>,
            colModel:<?= $body ?>,
            height:250,
            autowidth: true,
            pager: '#pager1',
            rowNum: 50,
            rowList: [50,100,200],
            rownumbers: true,
            sortname: 'SupplierID',
            sortorder: 'asc',
            viewrecords: true,
            toolbar : [true,"top"],
            caption: "<?= $caption ?>"
        });
        
        jQuery("#list1").jqGrid('navGrid','#pager1',{edit:false,add:false,del:false,search:false,refresh:true});
         resize_the_grid();
    });
    
     $(window).resize(resize_the_grid);
    
    
    function exportExcel()
    {
        var mya=new Array();
        mya=$("#list1").getDataIDs();  // Get All IDs
        var data=$("#list1").getRowData(mya[0]);     // Get First row to get the labels
        var colNames=new Array(); 
        var ii=0;
        for (var i in data){colNames[ii++]=i;}    // capture col names
        var html="";
        for(i=0;i<mya.length;i++)
        {
            data=$("#list1").getRowData(mya[i]); // get each row
            for(j=0;j<colNames.length;j++)
            {
                html=html+data[colNames[j]]+"\t"; // output each column as tab delimited
            }
            html=html+"\n";  // output each row with end of line

        }
        html=html+"\n";  // end of line at the end
        document.forms[0].csvBuffer.value=html;
        document.forms[0].method='POST';
        document.forms[0].action='<?= base_url() . 'index.php' . $from . 'csvExportHistory/history'?>'; // send it to server which will open this contents in excel file
        document.forms[0].target='_blank';
        document.forms[0].submit();
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
