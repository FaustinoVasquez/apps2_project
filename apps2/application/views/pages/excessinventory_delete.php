<style type="text/css" media="screen">
    th.ui-th-column div{
        white-space:normal !important;
        height:auto !important;
        padding:3px;
    }

    .ui-icon.myicon {
        width: 16px;
        height: 16px;
        background-image:url('http://apps2.mitechnologiesinc.com/images/toolbar/Excel-icon2.png');
    }


</style>



<?php
$formopen = array( 'id' => "excessinv_form", 'class'=>'myform');
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
        echo '<br>';
        $inputfrom = array('id' => 'from', 'name' => 'datefrom', 'class' => 'date-pick', 'value' => $datefrom, 'size' => '10', 'onchange' => "this.form.submit()", 'style' => 'width:80px;text-align:center');
        echo "from:" . form_input($inputfrom);
        echo "&nbsp;&nbsp;";
    //    $inputto = array('id' => 'to', 'name' => 'dateto', 'class' => 'date-pick', 'value' => $dateto, 'size' => '10', 'onchange' => "this.form.submit()", 'style' => 'width:80px;text-align:center');
  //      echo "to:" . form_input($inputto);
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


<form method="post" action="<?= base_url() . 'index.php' . $from . 'csvExport' ?>">
    <input type="hidden" name="csvBuffer" id="csvBuffer" value="" />
</form>


<div class="clear"></div>

<section id="grid_container">
    <table id="list14"></table>
    <div id="pager14"></div> 
</section>



<script type="text/javascript">


    function resize_the_grid(){
        $("#list14").fluidGrid({base:'#grid_wrapper', offset:-20});
    }

    //funcion para hacer el autoresize
    $(document).ready(function(){

        var myGrid = $("#list14");


        myGrid.jqGrid({
            url:'<?= base_url() . 'index.php' . $gridSearch ?>',
            datatype: "json", 
            rowNum:50,
            rowList:[50,300,1000,100000000],
            loadComplete: function() { $("option[value=100000000]").text('ALL');},
            colNames:<?= $colNames ?>,
            colModel:<?= $colModel ?>,
            pager: '#pager14',
            viewrecords: true,
            rownumbers: true,
            sortorder: 'asc',
            caption: '<?= $caption ?>',
            height: 550, 
            toppager:true,
            footerrow : true,
            userDataOnFooter : true
        }); 
        myGrid.jqGrid('navGrid','#pager14',{cloneToTop:true,edit:false,add:false,del:false,search:false,refresh:true, excel:true})
       

        myGrid.jqGrid('navButtonAdd', '#' + myGrid[0].id + '_toppager_left', { // "#list_toppager_left"
            caption: "Excel",
            buttonicon: 'myicon',
            onClickButton: function(){
                exportExcel();
            }
        });
          resize_the_grid(myGrid);    

  });
 
        // remove some double elements from one place which we not need double
        var topPagerDiv = $('#' + jQuery("#list14")[0].id + '_toppager')[0];         // "#list_toppager"
        $("#edit_" + jQuery("#list14")[0].id + "_top", topPagerDiv).remove();        // "#edit_list_top"
        $("#del_" + jQuery("#list14")[0].id + "_top", topPagerDiv).remove();         // "#del_list_top"
        $("#search_" + jQuery("#list14")[0].id + "_top", topPagerDiv).remove();      // "#search_list_top"
        // $("#refresh_" + myGrid[0].id + "_top", topPagerDiv).remove();     // "#refresh_list_top"
        // $("#" + myGrid[0].id + "_toppager_center", topPagerDiv).remove(); // "#list_toppager_center"
        // $(".ui-paging-info", topPagerDiv).remove();

        var bottomPagerDiv = $("div#pager14")[0];
        $("#add_" + jQuery("#list14")[0].id, bottomPagerDiv).remove();               // "#add_list"
     




        function exportExcel()
        {
            var mya=new Array();
            mya=$("#list14").getDataIDs();  // Get All IDs
            var data=$("#list14").getRowData(mya[0]);     // Get First row to get the labels
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
                data=$("#list14").getRowData(mya[i]); // get each row
                for(j=0;j<colNames.length;j++)
                {
                    html=html+data[colNames[j]]+"\t"; // output each Row as tab delimited
                }
                html=html+"\n";  // output each row with end of line

            }

            html=html+"\n";  // end of line at the end
            document.forms[1].csvBuffer.value=html;
            document.forms[1].method='POST';
            document.forms[1].action='<?= base_url() . 'index.php/' . $from . '/csvExport' ?>';  // send it to server which will open this contents in excel file
            document.forms[1].target='_blank';
            document.forms[1].submit();
        }

 
 
        $(window).resize(resize_the_grid);


        function getCSVData(){
            var csv_value=$('#list14').table2CSV({delivery:'value'});
            $("#csv_text").val(csv_value);
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

