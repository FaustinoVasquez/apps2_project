<style type="text/css" media="screen">

    .ui-icon.myicon {
        width: 16px;
        height: 16px;
        background-image:url('<?= base_url() ?>images/toolbar/Excel-icon2.png')
    }

</style>

<SCRIPT TYPE="text/javascript">
<!--


   function target_popup(form) {
    window.open('', 'formpopup', 'height=580,width=560,scrollbars=yes');
    form.target = 'formpopup';
    return true;
}



//-->
</SCRIPT>



<?php
$formopen = array('id' => 'target', 'id' => "prodcat_form", 'class'=>'myform');
echo form_open(base_url() . 'index.php/' . $from, $formopen);
?>
<fieldset class="bluegradiant">
    <div class="logo"><img longdesc='MI Technologies' title='Mi Tech' alt='43' src='<?= base_url() ?>/images/header/mitechnologies.png'>
    </div>
    <div class="form-data"> 
        <?php
        $inputtext = array('name' => 'search', 'value' => $search, 'autocomplete' => 'on', 'maxlength' => '250', 'size' => '35px');
        echo "Search:" . form_input($inputtext);
        $dropdown = 'class="form-select" onChange="this.form.submit()"';
        echo form_dropdown('productLines', $productLineOptions, isset($lineselect->productLines) ? $lineselect->productLines : $this->input->post('productLines'), $dropdown);
        ?>
        <br>
        <?php
        $inputdateTo = array('name' => 'dateTo', 'value' => $dateTo, 'autocomplete' => 'on', 'size' => '10', 'class' => 'date-pick', 'style' => 'text-align:center;');
        echo 'Cutoff Date Inventory: ' . form_input($inputdateTo);
        echo "&nbsp;&nbsp;";
        $historyInputText = array('name' => 'historyDays', 'value' => $historyDays, 'autocomplete' => 'on', 'maxlength' => '4', 'size' => '1', 'style' => 'text-align:center;');
        echo 'DOIR: ' . form_input($historyInputText);
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

<form name="form1" method="post" action="<?= base_url() . 'index.php' . $from . 'csvExport' ?>" >
    <input type="hidden" name="csvBuffer" id="csvBuffer" value="" />
</form>


<div class="clear"></div>

<section id="grid_container">
    <table id="list"></table>
    <div id="pager" ></div>
</section>


<form name="myForm" method="post" action="<?= base_url() . 'index.php' . $totalcosturl ?>" onSubmit="target_popup(this)">
    <input name="mySubmit" type="submit"  value="" style="visibility:hidden"/>
</form>


<script type="text/javascript">

    //funcion para hacer el autoresize
    function resize_the_grid(){
        $("#list").fluidGrid({base:'#grid_wrapper', offset:-20});
    }


    $(document).ready(function(){
    
        var myGrid = $("#list");

        myGrid.jqGrid({
            url:'<?= base_url() . 'index.php' . $from . $gridSearch ?>',
           
            datatype: "json",
            height: 560, 
            colNames:<?= $headers ?>,
            colModel:<?= $body ?>,
            pager: '#pager',
            rowNum: 50,
 	    rowList:[50,300,500,100000000],
            loadComplete: function() { $("option[value=100000000]").text('ALL');},
            rownumbers: true,
            sortname: 'ID',
            sortorder: 'asc',
            viewrecords: true,
            caption: "<?= $caption ?>",
            toppager:true,

            cellEdit: true,
            cellurl: '<?= base_url() . 'index.php' . $from . 'cellDataEdit' ?>',
       
            afterSaveCell:function() {
                jQuery("#list").jqGrid().trigger("reloadGrid");
                jQuery("#list").jqGrid().trigger("reloadGrid");

            },
            subGrid: true,
            subGrisubGridOptions: { "plusicon" : "ui-icon-triangle-1-e", "minusicon" : "ui-icon-triangle-1-s", "openicon" : "ui-icon-arrowreturn-1-e" },
            subGridRowExpanded: function(subgrid_id, row_id) {
                
                var data=$("#list").getRowData(row_id); 
                
                $.ajax({
                    url: '<?= base_url() . "index.php" . $showskudata  ?>'+data.ID,
                    type: "GET",
                    success: function(html) {
                        $("#" + subgrid_id).append(html);
                    }
                });
            },
            footerrow : true,
            userDataOnFooter : true
        });  jQuery("#list").jqGrid('navGrid','#pager',{cloneToTop:true,view:true,del:false,add:false,edit:false},{},{},{},{multipleSearch:true})
	
        
        jQuery("#list").jqGrid('navButtonAdd', '#' + jQuery("#list")[0].id + '_toppager_left', { // "#list_toppager_left"
            caption: "Excel",
            buttonicon: 'myicon',
            onClickButton: function(){
                exportExcel(myGrid,'Projection');
            }
        });
        
         jQuery("#list").jqGrid('navButtonAdd', '#' + jQuery("#list")[0].id + '_toppager_left', { // "#list_toppager_left"
            caption: "TotalCost",
            buttonicon: 'myicon',
            onClickButton: function(){
                document.myForm.mySubmit.click();
               
            }
        });
        
        resize_the_grid(myGrid);  
    });
    
    


    var topPagerDiv = $('#' + jQuery("#list")[0].id + '_toppager')[0];         // "#list_toppager"
    $("#edit_" + jQuery("#list")[0].id + "_top", topPagerDiv).remove();        // "#edit_list_top"
    $("#del_" + jQuery("#list")[0].id + "_top", topPagerDiv).remove();         // "#del_list_top"
    $("#search_" + jQuery("#list")[0].id + "_top", topPagerDiv).remove();      // "#search_list_top"
    // $("#refresh_" + myGrid[0].id + "_top", topPagerDiv).remove();     // "#refresh_list_top"
    // $("#" + myGrid[0].id + "_toppager_center", topPagerDiv).remove(); // "#list_toppager_center"
    // $(".ui-paging-info", topPagerDiv).remove();

    var bottomPagerDiv = $("div#pager14")[0];
    $("#add_" +jQuery("#list")[0].id, bottomPagerDiv).remove();               // "#add_list"



    function exportExcel(myGrid,RemoveStyle)
    {
        var mya=new Array();
        mya=$(myGrid).getDataIDs();  // Get All IDs
        var data=$(myGrid).getRowData(mya[0]);     // Get First row to get the labels
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
            data=$(myGrid).getRowData(mya[i]); // get each row
            for(j=0;j<colNames.length;j++)
            {
                if (colNames[j]==RemoveStyle){               
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
        document.forms[1].action='<?= base_url() . 'index.php/' . $from . '/csvExport' ?>';
        document.forms[1].target='_blank';
        document.forms[1].submit();
    }


    $(window).resize(resize_the_grid);

  
    /*
     *
     *  RowObject Trae todo el arreglo, desde ahi podemos acceder a otras celdas del renglon
     *
     */
    function projection(cellvalue, options,rowData) {
        if ((cellvalue > rowData[5])) {
            return '<div class="colorBlock" style="background-color: green; color: white; font-weight: bold;">'+ cellvalue +'</div>';
        }
        if (cellvalue <= rowData[5]) {
            return '<div class="colorBlock" style="background-color: red; color: white; font-weight: bold;">'+ cellvalue +'</div>';
        }
        
    }

    //  jQuery('.date-pick').datepicker({dateFormat:"yy-mm-dd"});
    $(function() {
        $( ".date-pick" ).datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat:"mm/dd/yy"
        });
    });







</script>
