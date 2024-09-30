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
$selectCategory = 'class="form-select" id="category"';
$submit = array('name' => 'send', 'value' => 'Submit', 'type' => 'submit', 'class' => 'button');
$reset = array('name' => 'reset', 'value' => 'Reset', 'type' => 'submit', 'class' => 'button');


echo form_open(base_url() . 'index.php' . $from, $formopen);
?>
<fieldset class="bluegradiant">
    <div class="logo"><img longdesc="MI Technologies"  title="Mi Tech" alt="43" src="<?= base_url() ?>/images/header/mitechnologies.png"></div>
    <div class="form-data"> 
    <center>
           
    <?php echo form_label('Search: ');
          echo form_input($inputsearch);
          echo '&nbsp;';
          echo form_label('Category: ');
          echo form_dropdown('selectMenu', $category,'0', $selectCategory);
          echo '<br>';
          echo '&nbsp;&nbsp;&nbsp;&nbsp;';
          echo form_input($submit).'&nbsp;'.form_input($reset);
    ?>
        <center>
    </div>
</fieldset>
<br>
<?php
echo form_close();
?>

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

    
    var colNames = ['SKU','Name','LastQuotedPrice','AvgPOCost'];
    var colModel = [
                    {name:'SKU',index:'SKU', width:45, align:'center'},
                    {name:'Name',index:'Name', width:60, align:'left'},
                    {name:'LastQuotedPrice',index:'LastQuotedPrice', width:80, align:'center', editable:true,editrules:{number:true}},  
                    {name:'AvgPOCost',index:'AvgPOCost', width:60, align:'center'},  
                ];

    $(document).ready(function(){
        

        var myGrid = $("#list");

        myGrid.jqGrid({
            url:'getData',
            postData: {
                ds:   function() { return jQuery("#search").val(); },
                cat:  function() { return jQuery("#category option:selected").val(); },
            },
            datatype: "json",
            colNames:colNames,
            colModel:colModel,
            autowidth: true,
            pager: jQuery('#pager'),
            rowNum: 50,
            sortname: 'asc',
            rowList:[50,300,500,100000000],
            loadComplete: function() { $("option[value=100000000]").text('ALL');},
            rownumbers: true,
            viewrecords: true,
            caption: "<?= $caption ?>",
            height: 600, 
            toppager:true,
            reloadOnce: false,
            cellEdit: true,
            cellurl:"saveDefault",
        });

        jQuery("#list").jqGrid('navGrid','#pager',{cloneToTop:true,edit:false,add:false,del:false,search:false,refresh:true});

        myGrid.jqGrid('navButtonAdd', '#' + myGrid[0].id + '_toppager_left', { // "#list_toppager_left"
            caption: "Excel",
            buttonicon: 'myicon',
            onClickButton: function(){
                exportExcel(myGrid,'<?= $export ?>'); 
            }
        });

        // var totalRows= jQuery('#list').jqGrid('getGridParam','records');
        // $("#totals").append('<h1>'+totalRows+'<h1>');
        resize_the_grid();

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

            var valor = jQuery("#category option:selected").val();
            switch(valor)
            {
                case '60':
                    var colNames = ['SKU','Name','LowestCost','AvgPOCost','MITCost','MgtSKU','MgtCost','GBSKU','GBCost','STCSKU','STCCost','KWSKU','KWCost','LeaderSKU','LeaderCost','YitaSKU','YitaCost'];
                    var colModel = [
                                    {name:'SKU',index:'SKU', width:45, align:'center'},
                                    {name:'Name',index:'Name', width:200, align:'left'},
                                    {name:'LowestCost',index:'LowestCost', width:50, align:'center'},  
                                    {name:'AvgPOCost',index:'AvgPOCost', width:50, align:'center'},  
                                    {name:'MITCost',index:'MITCost', width:50, align:'center', editable:true,editrules:{number:true}}, 
                                    {name:'MgtSKU',index:'MgtSKU', width:70, align:'center', editable:true}, 
                                    {name:'MgtCost',index:'MgtCost', width:50, align:'center', editable:true,editrules:{number:true}}, 
                                    {name:'GBSKU',index:'GBSKU', width:60, align:'center', editable:true}, 
                                    {name:'GBCost',index:'GBCost', width:50, align:'center', editable:true,editrules:{number:true}}, 
                                    {name:'STCSKU',index:'STCSKU', width:50, align:'center', editable:true}, 
                                    {name:'STCCost',index:'STCCost', width:50, align:'center', editable:true,editrules:{number:true}}, 
                                    {name:'KWSKU',index:'KWSKU', width:60, align:'center', editable:true}, 
                                    {name:'KWCost',index:'KWCost', width:50, align:'center', editable:true,editrules:{number:true}}, 
                                    {name:'LeaderSKU',index:'LeaderSKU', width:70, align:'center', editable:true}, 
                                    {name:'LeaderCost',index:'LeaderCost', width:50, align:'center', editable:true,editrules:{number:true}}, 
                                    {name:'YitaSKU',index:'YitaSKU', width:70, align:'center', editable:true}, 
                                    {name:'YitaCost',index:'YitaCost', width:50, align:'center', editable:true,editrules:{number:true}}, 
                                ];
                    var cellurl = "saveHousing";
                break;
                case '24':
                    var colNames = ['SKU','Name','LowestCost','AvgPOCost','MITCost','ArcliteSKU','ArcliteCost','GlorySKU','GloryCost','CLPSKU','CLPCost','GBSKU','GBCost','LeaderSKU','LeaderCost','YitaSKU','YitaCost'];
                    var colModel = [
                                    {name:'SKU',index:'SKU', width:45, align:'center'},
                                    {name:'Name',index:'Name', width:200, align:'left'},
                                    {name:'LowestCost',index:'LowestCost', width:50, align:'center'},  
                                    {name:'AvgPOCost',index:'AvgPOCost', width:50, align:'center'},  
                                    {name:'MITCost',index:'MITCost', width:50, align:'center', editable:true,editrules:{number:true}}, 
                                    {name:'ArcliteSKU',index:'ArcliteSKU', width:70, align:'center', editable:true}, 
                                    {name:'ArcliteCost',index:'ArcliteCost', width:50, align:'center', editable:true,editrules:{number:true}}, 
                                    {name:'GlorySKU',index:'GlorySKU', width:70, align:'center', editable:true,},
                                    {name:'GloryCost',index:'GloryCost', width:50, align:'center', editable:true,editrules:{number:true}}, 
                                    {name:'CLPSKU',index:'CLPSKU', width:70, align:'center', editable:true},
                                    {name:'CLPCost',index:'CLPCost', width:50, align:'center', editable:true,editrules:{number:true}}, 
                                    {name:'GBSKU',index:'GBSKU', width:70, align:'center', editable:true}, 
                                    {name:'GBCost',index:'GBCost', width:50, align:'center', editable:true,editrules:{number:true}},
                                    {name:'LeaderSKU',index:'LeaderSKU', width:70, align:'center', editable:true},
                                    {name:'LeaderCost',index:'LeaderCost', width:50, align:'center', editable:true,editrules:{number:true}}, 
                                    {name:'YitaSKU',index:'YitaSKU', width:70, align:'center', editable:true}, 
                                    {name:'YitaCost',index:'YitaCost', width:50, align:'center', editable:true,editrules:{number:true}}, 
                                ];
                    var cellurl = "saveGeneric";
                break;
                default:
                    var colNames = ['SKU','Name','LastQuotedPrice','AvgPOCost'];
                    var colModel = [
                                    {name:'SKU',index:'SKU', width:45, align:'center'},
                                    {name:'Name',index:'Name', width:60, align:'left'},
                                    {name:'LastQuotedPrice',index:'LastQuotedPrice', width:80, align:'center', editable:true,editrules:{number:true}},  
                                    {name:'AvgPOCost',index:'AvgPOCost', width:60, align:'center'},  
                                ];
                    var cellurl = "saveDefault";
                break;
            }

            $("#grid_container").empty().html("<table id='list'></table><div id='pager'></div> ");

            var myGrid = $("#list");
            myGrid.jqGrid({
            url:'getData',
            postData: {
                ds:   function() { return jQuery("#search").val(); },
                cat:  function() { return jQuery("#category option:selected").val(); },
            },
            datatype: "json",
            colNames:colNames,
            colModel:colModel,
            autowidth: true,
            pager: jQuery('#pager'),
            rowNum: 50,
            sortname: 'asc',
            rowList:[50,300,500,100000000],
            loadComplete: function() { $("option[value=100000000]").text('ALL');},
            rownumbers: true,
            viewrecords: true,
            caption: "<?= $caption ?>",
            height: 600, 
            toppager:true,
            reloadOnce: false,
            cellEdit: true,
            cellurl:cellurl ,
        });

        jQuery("#list").jqGrid('navGrid','#pager',{cloneToTop:true,edit:false,add:false,del:false,search:false,refresh:true});
        jQuery("#list").trigger('reloadGrid');

        myGrid.jqGrid('navButtonAdd', '#' + myGrid[0].id + '_toppager_left', { // "#list_toppager_left"
            caption: "Excel",
            buttonicon: 'myicon',
            onClickButton: function(){
                exportExcel(myGrid,'<?= $export ?>'); 
            }
        });
           myReload();
        });

        //Recargar el grid en el evento onChange del select search del formulario
        $( "#category" ).on('change',function(){
            var valor = jQuery("#category option:selected").val();
            switch(valor)
            {
                case '60':
                    var colNames = ['SKU','Name','LowestCost','AvgPOCost','MITCost','MgtSKU','MgtCost','GBSKU','GBCost','STCSKU','STCCost','KWSKU','KWCost','LeaderSKU','LeaderCost','YitaSKU','YitaCost'];
                    var colModel = [
                                    {name:'SKU',index:'SKU', width:45, align:'center'},
                                    {name:'Name',index:'Name', width:200, align:'left'},
                                    {name:'LowestCost',index:'LowestCost', width:50, align:'center'},  
                                    {name:'AvgPOCost',index:'AvgPOCost', width:50, align:'center'},  
                                    {name:'MITCost',index:'MITCost', width:50, align:'center', editable:true,editrules:{number:true}}, 
                                    {name:'MgtSKU',index:'MgtSKU', width:70, align:'center', editable:true}, 
                                    {name:'MgtCost',index:'MgtCost', width:50, align:'center', editable:true,editrules:{number:true}}, 
                                    {name:'GBSKU',index:'GBSKU', width:60, align:'center', editable:true}, 
                                    {name:'GBCost',index:'GBCost', width:50, align:'center', editable:true,editrules:{number:true}}, 
                                    {name:'STCSKU',index:'STCSKU', width:50, align:'center', editable:true}, 
                                    {name:'STCCost',index:'STCCost', width:50, align:'center', editable:true,editrules:{number:true}}, 
                                    {name:'KWSKU',index:'KWSKU', width:60, align:'center', editable:true}, 
                                    {name:'KWCost',index:'KWCost', width:50, align:'center', editable:true,editrules:{number:true}}, 
                                    {name:'LeaderSKU',index:'LeaderSKU', width:70, align:'center', editable:true}, 
                                    {name:'LeaderCost',index:'LeaderCost', width:50, align:'center', editable:true,editrules:{number:true}}, 
                                    {name:'YitaSKU',index:'YitaSKU', width:70, align:'center', editable:true}, 
                                    {name:'YitaCost',index:'YitaCost', width:50, align:'center', editable:true,editrules:{number:true}}, 
                                ];
                    var cellurl = "saveHousing";
                break;
                case '24':
                    var colNames = ['SKU','Name','LowestCost','AvgPOCost','MITCost','ArcliteSKU','ArcliteCost','GlorySKU','GloryCost','CLPSKU','CLPCost','GBSKU','GBCost','LeaderSKU','LeaderCost','YitaSKU','YitaCost'];
                    var colModel = [
                                    {name:'SKU',index:'SKU', width:45, align:'center'},
                                    {name:'Name',index:'Name', width:200, align:'left'},
                                    {name:'LowestCost',index:'LowestCost', width:50, align:'center'},  
                                    {name:'AvgPOCost',index:'AvgPOCost', width:50, align:'center'},  
                                    {name:'MITCost',index:'MITCost', width:50, align:'center', editable:true,editrules:{number:true}}, 
                                    {name:'ArcliteSKU',index:'ArcliteSKU', width:70, align:'center', editable:true}, 
                                    {name:'ArcliteCost',index:'ArcliteCost', width:50, align:'center', editable:true,editrules:{number:true}}, 
                                    {name:'GlorySKU',index:'GlorySKU', width:70, align:'center', editable:true},
                                    {name:'GloryCost',index:'GloryCost', width:50, align:'center', editable:true,editrules:{number:true}}, 
                                    {name:'CLPSKU',index:'CLPSKU', width:70, align:'center', editable:true},
                                    {name:'CLPCost',index:'CLPCost', width:50, align:'center', editable:true,editrules:{number:true}}, 
                                    {name:'GBSKU',index:'GBSKU', width:70, align:'center', editable:true}, 
                                    {name:'GBCost',index:'GBCost', width:50, align:'center', editable:true,editrules:{number:true}},
                                    {name:'LeaderSKU',index:'LeaderSKU', width:70, align:'center', editable:true},
                                    {name:'LeaderCost',index:'LeaderCost', width:50, align:'center', editable:true,editrules:{number:true}}, 
                                    {name:'YitaSKU',index:'YitaSKU', width:70, align:'center', editable:true}, 
                                    {name:'YitaCost',index:'YitaCost', width:50, align:'center', editable:true,editrules:{number:true}}, 
                                ];
                    var cellurl = "saveGeneric";
                break;
                default:
                    var colNames = ['SKU','Name','LastQuotedPrice','AvgPOCost'];
                    var colModel = [
                                    {name:'SKU',index:'SKU', width:45, align:'center'},
                                    {name:'Name',index:'Name', width:60, align:'left'},
                                    {name:'LastQuotedPrice',index:'LastQuotedPrice', width:80, align:'center', editable:true,editrules:{number:true}},  
                                    {name:'AvgPOCost',index:'AvgPOCost', width:60, align:'center'},  
                                ];
                    var cellurl = "saveDefault";
                break;
            }

            $("#grid_container").empty().html("<table id='list'></table><div id='pager'></div> ");

            var myGrid = $("#list");
            myGrid.jqGrid({
            url:'getData',
            postData: {
                ds:   function() { return jQuery("#search").val(); },
                cat:  function() { return jQuery("#category option:selected").val(); },
            },
            datatype: "json",
            colNames:colNames,
            colModel:colModel,
            autowidth: true,
            pager: jQuery('#pager'),
            rowNum: 50,
            sortname: 'asc',
            rowList:[50,300,500,100000000],
            loadComplete: function() { $("option[value=100000000]").text('ALL');},
            rownumbers: true,
            viewrecords: true,
            caption: "<?= $caption ?>",
            height: 600, 
            toppager:true,
            reloadOnce: false,
            cellEdit: true,
            cellurl:cellurl ,
        });

        jQuery("#list").jqGrid('navGrid','#pager',{cloneToTop:true,edit:false,add:false,del:false,search:false,refresh:true});
        jQuery("#list").trigger('reloadGrid');

        myGrid.jqGrid('navButtonAdd', '#' + myGrid[0].id + '_toppager_left', { // "#list_toppager_left"
            caption: "Excel",
            buttonicon: 'myicon',
            onClickButton: function(){
                exportExcel(myGrid,'<?= $export ?>'); 
            }
        });

            myReload();
            //$("#grid_container").empty().html("<table id='list'></table><div id='pager'></div> ");
        
        });
    });
    
    $(window).resize(resize_the_grid);

</script>

