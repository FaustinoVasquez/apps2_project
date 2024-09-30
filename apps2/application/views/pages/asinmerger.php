
<style type = "text/css" media = "screen">


    .ui-icon.myicon {
        width: 16px;
        height: 16px;
        background-image:url('<?= base_url() ?>images/toolbar/Excel-icon2.png')
    }


</style>
<?php
$formopen = array('id' => 'target', 'id' => "omc_form", 'class' => 'myform');
echo form_open(base_url() . 'index.php' . $from, $formopen);
?>
<fieldset class="bluegradiant">
    <div class="logo"><img longdesc="MI Technologies"
                           title="Mi Tech" alt="43" src="<?= base_url() ?>/images/header/mitechnologies.png">
    </div>
    <div class="form-data"> 
    <?php
     
        $inputASIN= array( 'name' => 'search' , 'id' => 'mysku' , 'value'=> $search ,'maxlength' => '15' , 'size' => '12' ,  'onChange' => "this.form.submit()",) ;
        echo "ASIN:" . form_input($inputASIN);

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

 <style>
.ui-autocomplete {
max-height: 200px;
overflow-y: auto;
/* prevent horizontal scrollbar */
overflow-x: hidden;
}
/* IE 6 doesn't support max-height
* we use height instead, but this forces the menu to always be this tall
*/
* html .ui-autocomplete {
height: 100px;
}
</style>
    
    
<div class="clear"></div>
    
<section id="grid_container">
    <table id="<?= $nameGrid ?>"></table>
    <div id="<?= $namePager ?>"></div> 
</section>
    
<form method="post" action="<?= base_url() . 'index.php' . $from . 'csvExport' ?>">
    <input type="hidden" name="csvBuffer" id="csvBuffer" value="" />
</form>  
    
    
    
<script type="text/javascript">
    
    function resize_the_grid()
    {
        $("<?= '#' . $nameGrid ?>").fluidGrid({base:'#grid_wrapper', offset:-20});
    }
 
    
    $(document).ready(function(){
        

        var myGrid = $("<?= '#' . $nameGrid ?>");
	
        myGrid.jqGrid({
            url:'<?= base_url() . 'index.php' . $from . $gridSearch ?>',
            datatype: "json",
            colNames:<?= $colNames ?>,
            colModel:<?= $colModel ?>,
            autowidth: true,
            pager: jQuery('<?= '#' . $namePager ?>'),
            rowNum: 50,
            sortname: 'ProductCatalogId',
            rowList:[50,300,100000000],
            loadComplete: function() { $("option[value=100000000]").text('ALL');},
            rownumbers: true,
            viewrecords: true,
            caption: "<?= $caption ?>",
            height: 600, 
            toppager:true,
	    editurl:'<?= base_url() . 'index.php/' . $from . 'processRecord' ?>'
        });
	
        jQuery("<?= '#' . $nameGrid ?>").jqGrid('navGrid','<?= '#' . $namePager ?>',{cloneToTop:true,edit:false,add:false,del:true,search:false,refresh:true},
                                {}, // default settings for edit
                                {}, // default settings for add
                                { mtype: "post", 
                                  reloadAfterSubmit: true,}
                                
                                );

	resize_the_grid();

        
         
    });
    
    $(window).resize(resize_the_grid);
    
  
   
     $(function() {
        $( ".date-pick" ).datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat:"mm/dd/yy"
        });
    }); 
    


    function formatLink(cellvalue, options,rowData) {
        if (cellvalue!=null){    
            return "<a href= http://www.amazon.com/dp/"+cellvalue+"/?tag=discountmer09-20 target='_blank'>" + cellvalue + "</a>";
        }
    }

</script>


