<style type="text/css" media="screen">

    .ui-icon.myicon {
        width: 16px;
        height: 16px;
        background-image:url('<?= base_url() ?>images/toolbar/Excel-icon2.png')
    }

</style>
<?php
$formopen = array('id' => 'target', 'class' => 'myform');
$inputsearch = array('id'=>'search','name' => 'search', 'value' => '', 'autocomplete' => 'on', 'maxlength' => '250', 'size' => '20');
$submit = array('name' => 'send', 'value' => 'Submit', 'type' => 'submit', 'class' => 'button');
$reset = array('name' => 'reset', 'value' => 'Reset', 'type' => 'submit', 'class' => 'button');


echo form_open(base_url() . 'index.php' . $from, $formopen);
?>
<fieldset class="bluegradiant">
    <div class="logo"><img longdesc="MI Technologies"  title="Mi Tech" alt="43" src="<?= base_url() ?>/images/header/mitechnologies.png"></div>
    <div class="form-data">
        <?php
        echo "Search:" . form_input($inputsearch);
        echo "<br>";
        echo "&nbsp;&nbsp;";
        $inputfrom = array('id' => 'from', 'name' => 'datefrom', 'class' => 'date-pick', 'value' => $datefrom, 'size' => '10', 'style' => 'width:80px;text-align:center');
        echo "from:" . form_input($inputfrom);
        echo "&nbsp;&nbsp;";
        $inputto = array('id' => 'to', 'name' => 'dateto', 'class' => 'date-pick', 'value' => $dateto, 'size' => '10',  'style' => 'width:80px;text-align:center');
        echo "to:" . form_input($inputto);
        echo "&nbsp;&nbsp;";
        echo form_input($submit);
        echo form_input($reset);
        ?>
    </div>
</fieldset>
<br>
<?php
echo form_close();
?>

<div class="clear"></div>


<div class="tabs">
    <ul>
        <li><a href="#tabs-1">Sales Data</a></li>
        <li><a href="#tabs-2">Catalog</a></li>
        <li><a href="#tabs-3">Categories</a></li>
        <li><a href="#tabs-4">Mapping</a></li>
    </ul>
    <div id="tabs-1">
            <table id="grid"></table>
            <div id="pager"></div>
    </div>
    <div id="tabs-2">
            <table id="grid1"></table>
            <div id="pager1"></div>
    </div>
    <div id="tabs-3">
            <table id="grid2"></table>
            <div id="pager2"></div>
    </div>
    <div id="tabs-4">
            <table id="grid3"></table>
            <div id="pager3"></div>
    </div>
</div>




<form method="post" action="<?= base_url() . 'index.php' . $from . 'csvExport' ?>">
    <input type="hidden" name="csvBuffer" id="csvBuffer" value="" />
</form>

<script type="text/javascript">

    function resize_the_grid()
    {
       // myGrid.fluidGrid({base:'#grid_wrapper', offset:-20});
        $("#grid").setGridWidth( Math.round($(".tabs").width(), true) -30 );
        $("#grid1").setGridWidth( Math.round($(".tabs").width(), true) -30 );
        $("#grid2").setGridWidth( Math.round($(".tabs").width(), true) -30 );
        $("#grid3").setGridWidth( Math.round($(".tabs").width(), true) -30 );


    }
    $(function(){
         $( ".tabs" ).tabs();

        var myGrid  = $("#grid");
        var myGrid1 = $("#grid1");
        var myGrid2 = $("#grid2");
        var myGrid3 = $("#grid3");

        var colnames = <?=$colNames ?>;
        var colmodel = <?=$colModel ?>;

        var colnames1 = <?=$colNames1 ?>;
        var colmodel1 = <?=$colModel1 ?>;

        var colnames2 = <?=$colNames2 ?>;
        var colmodel2 = <?=$colModel2 ?>;

        var colnames3 = <?=$colNames3 ?>;
        var colmodel3 = <?=$colModel3 ?>;

        createGrid(myGrid,'#pager','getData',colnames,colmodel,'o.OrderDate','<?=$caption?>', 'Sales_Data');
        createGrid(myGrid1,'#pager1','getData1',colnames1,colmodel1,'pc.ID','<?=$caption1?>', 'Catalog');
        createGrid(myGrid2,'#pager2','getData2',colnames2,colmodel2,'ctg.ID','<?=$caption2?>', 'Categories');
        createGrid(myGrid3,'#pager3','getData3',colnames3,colmodel3,'PC.ID','<?=$caption3?>', 'Mapping');

 
       

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



    
    $(window).resize(resize_the_grid);


        function createGrid(myGrid,pager,url,colnames,colmodel,orderby,caption,fileName){
            myGrid.jqGrid({
            url:url,
            postData: {
                ds:   function() { return jQuery("#search").val(); },
                df:   function() { return jQuery("#from").val(); },
                dt:   function() { return jQuery("#to").val(); },
            },
            datatype: "json",
            colNames:colnames,
            colModel:colmodel,
            pager: jQuery(pager),
            rowNum: 300,
            sortname: orderby,
            sortorder: "asc",
            rowList:[300,500,100000000],
            loadComplete: function() { $("option[value=100000000]").text('ALL');},
            rownumbers: true,
            viewrecords: true,
            caption: caption,
            height: 600,
            toppager:true,
        });

         myGrid.jqGrid('navGrid',pager,{cloneToTop:true,edit:false,add:false,del:false,search:false,refresh:false});
         add_top_bar(myGrid,fileName);
         resize_the_grid();

         return false;

        }


    function add_top_bar(grid,fileName){

        jQuery(grid).jqGrid('navButtonAdd', '#' + jQuery(grid)[0].id + '_toppager_left', { // "#list_toppager_left"
            caption: "Excel",
            buttonicon: 'ui-icon-document',
            onClickButton: function(){
                exportExcel(grid,fileName);
            }
        });
    }


$(function(){
        $('#grid3_toppager_center').hide();
        $('#grid2_toppager_center').hide();
        $('#pager3_center').hide();
        $('#pager2_center').hide();
});


   


</script>

