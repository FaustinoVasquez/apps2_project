<?php

$formopen = array('id' => 'target', 'id' => "omc_form", 'class'=>'myform');
echo form_open(base_url() . 'index.php' . $from, $formopen);


?>
<fieldset class="bluegradiant">
    <div class="logo"><img longdesc="MI Technologies"
                           title="Mi Tech" alt="43" src="<?= base_url() ?>/images/header/mitechnologies.png">
    </div>
    <div class="form-data"> 
        <?php
        $inputsearch = array('name' => 'search', 'value' => $search, 'autocomplete' => 'on', 'maxlength' => '250', 'size' => '20', 'onclick' => "this.value=''");
        echo "Search:" . form_input($inputsearch);
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

<style type="text/css" media="screen">
    .ui-icon.excel {
        width: 16px;
        height: 16px;
        background-image:url('<?= base_url() ?>images/toolbar/Excel-icon2.png');
    }
</style>


<div class="clear"></div>

<section id="grid_container">
    <table id="<?= $nameGrid ?>"></table>
    <div id="<?= $namePager?>"></div> 
</section>

 <form method="post" action="<?= base_url() . 'index.php' . $from . 'csvExport' ?>">
    <input type="hidden" name="csvBuffer" id="csvBuffer" value="" />
</form>  



<script type="text/javascript">
    

    function resize_the_grid()
    {
        $("<?='#'.$nameGrid ?>").fluidGrid({base:'#grid_wrapper', offset:-20});
    }
    

    $(document).ready(function(){

        var myGrid = $("<?='#'.$nameGrid ?>");
        
       

        myGrid.jqGrid({
            url:'<?= base_url() . 'index.php' . $gridSearch ?>',
            datatype: "json", 
            rowNum:50,
            rowList:[50,300,100000000],
            loadComplete: function() { $("option[value=100000000]").text('ALL');},
            colNames:<?= $colNames ?>,
            colModel:<?= $colModel ?>,
            pager: jQuery('<?='#'.$namePager ?>'),
            viewrecords: true,
            rownumbers: true,
            sortname: 'id',
            sortorder: '<?= $sort ?>',
            caption: '<?= $caption ?>',
            height: 600, 
            toppager:true,
           // jsonReader: { repeatitems : false }, 
            shrinkToFit: false,
            editurl: '<?= base_url() . 'index.php' . $gridEdit ?>'
        });
   
       jQuery("<?='#'.$nameGrid ?>").jqGrid('setFrozenColumns');
       
        //Ajustamos el grid a la ventana
        resize_the_grid(myGrid);    
       // agregamos el boton de excel
       
        add_top_bar(myGrid);
 
    });
     
 

   $(window).resize(resize_the_grid);


     function add_top_bar(grid){
   
     jQuery(grid).jqGrid('navGrid','<?='#'.$namePager ?>',{cloneToTop:true,add:false,edit:true,del:false,view:true},{width:600});
                
                
        jQuery(grid).jqGrid('navButtonAdd', '#' + jQuery(grid)[0].id + '_toppager_left', { // "#list_toppager_left"
            caption: "Excel",
            buttonicon: 'excel',
            onClickButton: function(){
                exportExcel(grid);
            }
        });
   
   

        
        var topPagerDiv = $('#' + jQuery(grid)[0].id + '_toppager')[0];         // "#list_toppager"
      //  $("#edit_" + jQuery(grid)[0].id + "_top", topPagerDiv).remove();        // "#edit_list_top"
        $("#del_" + jQuery(grid)[0].id + "_top", topPagerDiv).remove();         // "#del_list_top"
      //  $("#search_" + jQuery(grid)[0].id + "_top", topPagerDiv).remove();      // "#search_list_top"
        $("#refresh_" + jQuery(grid)[0].id + "_top", topPagerDiv).remove();     // "#refresh_list_top"
        $("#add_" + jQuery(grid)[0].id + "_top", topPagerDiv).remove();         // "#add_list_top"
      //  $("#view_" + jQuery(grid)[0].id + "_top", topPagerDiv).remove();         // "#add_list_top"
        

    }
  
    
    function exportExcel()
    {
        var mya=new Array();
        mya=$("#list").getDataIDs();  // Get All IDs
        var data=$("#list").getRowData(mya[0]);     // Get First row to get the labels
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
            data=$("#list").getRowData(mya[i]); // get each row
            for(j=0;j<colNames.length;j++)
            {  
                html=html+data[colNames[j]]+"\t"; // output each Row as tab delimited
            }

            html=html+"\n";  // output each row with end of line

        }

        html=html+"\n";  // end of line at the end
        document.forms[1].csvBuffer.value=html;
        document.forms[1].method='POST';
        document.forms[1].action='<?= base_url() . 'index.php' . $from . 'csvExport' ?>';
        document.forms[1].target='_blank';
        document.forms[1].submit();
    }
   
   
   $(function() {
        $( ".date-pick" ).datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat:"mm/dd/yy"
        });
    }); 
    

  
    
    
</script>
