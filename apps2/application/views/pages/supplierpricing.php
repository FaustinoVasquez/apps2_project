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
$selectCategory = 'class="form-select" id="categoria"';
$selectSupplier = 'class="form-select" id="supplier"';
$selectEnable = 'class="form-select" id="selectData"';
$submit = array('name' => 'send', 'value' => 'Submit', 'type' => 'submit', 'class' => 'button');
$reset = array('name' => 'reset', 'value' => 'Reset', 'type' => 'submit', 'class' => 'button');

echo form_open(base_url() . 'index.php' . $from, $formopen);
?>
<fieldset class="bluegradiant">
    <div class="logo"><img longdesc="MI Technologies"  title="Mi Tech" alt="43" src="<?= base_url() ?>/images/header/mitechnologies.png"></div>
    <div class="form-data"> 
    <center>
        <table>
            <tbody>
                <tr>
                    <td><?=form_label('Search: ')?></td>
                    <td><?=form_input($inputsearch)?></td> 
                </tr>
                <tr>
                    <td><?=form_label('SupplierName: ')?></td>
                    <? '&nbsp;' ?><td><?=form_dropdown('selectMenu', $supplier,'0', $selectSupplier);?></td>
                    <td><?=form_label('Category: ')?></td>
                    <? '&nbsp;' ?><td><?=form_dropdown('selectMenu', $categoria,'0', $selectCategory);?></td>
                    <td><?=form_label('Enable?: ')?></td>
                    <? '&nbsp;' ?><td><?=form_dropdown('selectMenu', $selectData,'0', $selectEnable);?></td>
                </tr>
                <tr>
                    <td colspan="6"><center><?=form_input($submit).'&nbsp;'.form_input($reset);?></center></td>
                </tr>
            </tbody>
        </table>
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
        
    $(document).ready(function(){

        var myGrid = $("#list");

        myGrid.jqGrid({
            url:'getData',
            postData: {
                ds:   function() { return jQuery("#search").val(); },
                ca:   function() { return jQuery("#categoria option:selected").val(); },
                su:   function() { return jQuery("#supplier option:selected").val(); },
                en:   function() { return jQuery("#selectData option:selected").val(); },
            },
            datatype: "json",
            colNames:<?= $colNames ?>,
            colModel:<?= $colModel ?>,
            autowidth: true,
            pager: jQuery('#pager'),
            rowNum: 50,
            sortname: 'asc',
            rowList:[50,300,500],
            loadComplete: function() { $("option[value=100000000]").text('ALL');},
            sortname: "SupplierID",
            rownumbers: true,
            cellEdit: true,
            editurl:'<?= base_url() . 'index.php' . $from . '/EditData' ?>',
            viewrecords: true,
            caption: "<?= $caption ?>",
            height: 600, 
            toppager:true,
            cellurl:"editSupplier" ,
        });
 
        jQuery("#list").jqGrid('navGrid','#pager',{cloneToTop:true,edit:false,add:false,del:false,search:false,refresh:true});


        myGrid.jqGrid('navButtonAdd', '#' + myGrid[0].id + '_toppager_left', { // "#list_toppager_left"
            caption: "Excel",
            buttonicon: 'myicon',
            onClickButton: function(){
                exportExcel(myGrid,'<?= $export ?>'); 
            }
        });

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
            myReload();
        });

        //Recargar el grid en el evento onChange del select categoria del formulario
        $( "#categoria" ).on('change',function(){
            myReload();
        });

        //Recargar el grid en el evento onChange del select categoria del formulario
        $( "#supplier" ).on('change',function(){
            myReload();
        });
        
        //Recargar el grid en el evento onChange del select categoria del formulario
        $( "#selectData" ).on('change',function(){
            myReload();
        });

    });    
    
    $(window).resize(resize_the_grid);

    function ajaxSave(rowid, curCheckbox,grid) {
     var field = curCheckbox.name;
     var value = curCheckbox.checked;
     var request = $.ajax({
                url: "editSupplier",
                type: "POST",
                data: { id : rowid,
                        checkbox: field,
                        value: value
                       },
                });

     request.done(function( msg ) {
           jQuery("#"+grid).trigger('reloadGrid');
        });
     }

    function checkboxFormatter(cellvalue, options, rowObject) {
        cellvalue = cellvalue + "";
        cellvalue = cellvalue.toLowerCase();
        var bchk = cellvalue.search(/(false|0|no|off|n)/i) < 0 ? " checked=\"checked\"" : "";
        return "<input type='checkbox' onclick=\"ajaxSave('" + options.rowId + "', this,'myGrid');\" " + bchk + " value='" + cellvalue + "' name='"+options.colModel.name+"' id='"+options.colModel.name+"' offval='0' />";
    }

</script>