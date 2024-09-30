<style type="text/css" media="screen">
    .ui-icon.excel {
        width: 16px;
        height: 16px;
        background-image:url('<?= base_url() ?>images/toolbar/Excel-icon2.png');
    }
</style>


<div class="clear"></div>

<section id="grid_container">
    <form action='<?= base_url() . 'index.php' . $from . $search ?>' method='POST'>
	<label>Warehouse:</label>
	<?php
	$dropdown = 'style="font-size:12px" onChange="this.form.submit()"';
	echo form_dropdown('warehouses', $warehouseOptions, isset($warehouse->warehouses) ? $warehouse->warehouses : $this->input->post('warehouses'), $dropdown);
	echo "&nbsp;&nbsp;";

	$inputfrom = array('id' => 'from', 'name' => 'dateFrom', 'class' => 'date-pick', 'value' => $dateFrom, 'size' => '10', 'onchange' => "this.form.submit()", 'style' => 'width:80px;text-align:center');
	echo "<label>from:</label>" . form_input($inputfrom);

	echo "&nbsp;&nbsp;";
	$inputto = array('id' => 'to', 'name' => 'dateTo', 'class' => 'date-pick', 'value' => $dateTo, 'size' => '10', 'onchange' => "this.form.submit()", 'style' => 'width:80px;text-align:center');
	echo "<label>to:</label>" . form_input($inputto);
	echo "&nbsp;&nbsp;";
	?>   


	<input type='submit' class='button' value='Submit!'>
    </form>

    <table id="list1"></table>
    <div id="pager1"></div>
</section>

<form method="post" action="<?= base_url() . 'index.php' . $from . 'csvExport' ?>">
    <input type="hidden" name="csvBuffer" id="csvBuffer" value="" />
</form> 

<script type="text/javascript">
    
    function resize_the_grid()
    {
        $("#list1").fluidGrid({base:'#grid_wrapper', offset:-20});
    }
     
    $(document).ready(function(){

        var myGrid = $("#list1");
      
        myGrid.jqGrid({
            url:'<?= base_url() . 'index.php' . $from . 'gridDataHistory?q=' . $search . '&dateFrom=' . $dateFrom . '&dateTo=' . $dateTo . '&filtered=' . $warehouses ?>',
            datatype: "json",
            colNames:<?= $headers ?>,
            colModel:<?= $body ?>,
            height:250,
            autowidth: true,
	    loadonce: true,
            pager: '#pager1',
            rowNum: 1000,
            rowList: [1000,1500,2000],
            rownumbers: true,
            sortname: 'Adjustment_Id',
            sortorder: 'desc',
	    firstsortorder: 'desc',
            viewrecords: true,
            caption: "<?= $caption ?>",
	    editurl:'<?= base_url() . 'index.php' . $from . 'saveData' ?>',
	    toppager:true,
	    reloadAfterEdit: true
        });
        jQuery("#list1").jqGrid('navGrid','#pager1',{cloneToTop:true,edit:<?= $edit ?>,add:false,del:false,search:false,refresh:true},
            { // edit option
              beforeShowForm: function(form) { $('#tr_Adjustment_Id', form).hide(); },
              reloadAfterSubmit:false,
              closeAfterEdit: true,
              closeOnEscape:true,
              recreateForm: true,
              width:450
          });
	
	jQuery("#list1").jqGrid('filterToolbar',{stringResult: true,searchOnEnter : false}); 
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
