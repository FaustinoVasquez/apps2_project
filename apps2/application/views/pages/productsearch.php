<style type="text/css">
    .ui-autocomplete-loading { background: white url('images/ui-anim_basic_16x16.gif') right center no-repeat; }
    .ui-icon.myicon {
        width: 16px;
        height: 16px;
        background-image:url('http://apps2.mitechnologiesinc.com/images/toolbar/Excel-icon2.png');
    }

</style>

<?php
$formopen = array('id' => "target" , 'class'=>'myform');
echo form_open(base_url() . 'index.php' . $from, $formopen);
?>
<fieldset class="bluegradiant">
    <div class="logo"><img longdesc='MI Technologies' title='Mi Tech' alt='43' src='<?= base_url() ?>/images/header/mitechnologies.png'>
    </div>
    <div class="form-data"> 
        <?php
        
        $inputtext = array('id'=>'search','name' => 'search', 'value' => $search, 'autocomplete' => 'on', 'maxlength' => '250','style'=> 'width:57%;height:20px; margin-top:25px;font-size:16px;');
        echo "Search:" . form_input($inputtext);
      
      
        echo "&nbsp;&nbsp;";
        $submit = array('name' => 'send', 'value' => 'Submit', 'type' => 'submit', 'class' => 'button','style'=> 'height:25px;');
        echo form_input($submit);
        $reset = array('name' => 'reset', 'value' => 'Reset', 'type' => 'submit', 'class' => 'button','style'=> 'height:25px;');
        echo form_input($reset);
        ?>
    </div>
</fieldset>
<br>
<?php
echo form_close();

?>


<div class="clear"></div>

<section id="grid_container">
<table id="list"></table>
<div id="pager" ></div>
</section>


<form method="post" action="<?= base_url() . 'index.php/admin' . $from . '/csvExport' ?>">
    <input type="hidden" name="csvBuffer" id="csvBuffer" value="" />
</form>


<script type="text/javascript">
    
    
  function resize_the_grid()
    {
        $("#list").fluidGrid({base:'#grid_wrapper', offset:-20});
    }

    
      $(document).ready(function(){

        var myGrid = $("#list");

        myGrid.jqGrid({
            url:'getData',
            postData: {
                ds:   function() { return jQuery("#search").val(); },
            },
            datatype: "json",
            colNames:<?= $colNames ?>,
            colModel:<?= $colModel ?>,
            autowidth: true,
            pager: jQuery('#pager'),
            rowNum: 50,
            sortname: "PJD.[CatalogID]",
            sortorder: "asc",
            rowList:[50,300,500,100000000],
            loadComplete: function() { $("option[value=100000000]").text('ALL');},
            rownumbers: true,
            viewrecords: true,
            caption: "<?= $caption ?>",
            height: 600, 
            toppager:true,
            shrinkToFit: false
        });

        myGrid.jqGrid('navGrid','#pager',{cloneToTop:true,view:false,del:false,add:false,edit:false,reload:false,search:false},{},{},{},{multipleSearch:true})
        myGrid.jqGrid('navButtonAdd', '#' + jQuery("#list")[0].id + '_toppager_left', { // "#list_toppager_left"
            caption: "Excel",
            buttonicon: 'myicon',
            onClickButton: function(){
                exportExcel(myGrid,'<?= $export ?>'); 
            }
        });
    
         resize_the_grid(myGrid); 

        var myReload = function() {
             myGrid.setGridParam({ page: 1, datatype: "json" }).trigger('reloadGrid');
        }; 


          // Recargar el grid en el evento submit del formulario
        $( 'form' ).on( 'submit', function( e ){
            e.preventDefault();
            myReload();
        });

        //Recargar el grid en el evento onChange del select search del formulario
        $( "#search" ).on('change',function(){
            myReload();
        });
    
});





    var topPagerDiv = $('#' + jQuery("#list")[0].id + '_toppager')[0];         // "#list_toppager"
    $("#edit_" + jQuery("#list")[0].id + "_top", topPagerDiv).remove();        // "#edit_list_top"
    $("#del_" + jQuery("#list")[0].id + "_top", topPagerDiv).remove();         // "#del_list_top"
    $("#search_" + jQuery("#list")[0].id + "_top", topPagerDiv).remove();      // "#search_list_top"
    
    var bottomPagerDiv = $("div#pager14")[0];
    $("#add_" +jQuery("#list")[0].id, bottomPagerDiv).remove();               // "#add_list"





    $(window).resize(resize_the_grid);



</script>