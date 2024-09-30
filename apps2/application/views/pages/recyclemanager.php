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
    echo "&nbsp;";
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

<section id="grid_container">
    <table id="grid"></table>
    <div id="pager"></div>
</section>

<form method="post" action="<?= base_url() . 'index.php' . $from . 'csvExport' ?>">
    <input type="hidden" name="csvBuffer" id="csvBuffer" value="" />
</form>  

<script type="text/javascript">

    function resize_the_grid()
    {
        $("#grid").fluidGrid({base:'#grid_wrapper', offset:-20});
    }
    $(function(){

        var myGrid = $("#grid");

        myGrid.jqGrid({
            url:'getData',
            postData: {
                ds:   function() { return jQuery("#search").val(); },
            },
            datatype: "json",
            colNames:<?= $colNames ?>,
            colModel:<?= $colModel ?>,
            pager: jQuery('#pager'),
            rowNum: 50,
            sortname: 'C.ID',
            sortorder: "asc",
            rowList:[50,300,500,100000000],
            loadComplete: function() { $("option[value=100000000]").text('ALL');},
            rownumbers: true,
            viewrecords: true,
            caption: "<?=$caption?>",
            height: 600,
            toppager:true,
            shrinkToFit: false,
            cellEdit: true,
            cellurl:'editData',
            editurl:'crudOperation',
            afterEditCell: function (id,name,val,iRow,iCol){
                if(name=='DateContacted') {
                    jQuery("#"+iRow+"_DateContacted","#grid").datepicker({dateFormat:"mm-dd-yy"});
                }
                if(name=='CheckSentDate') {
                    jQuery("#"+iRow+"_CheckSentDate","#grid").datepicker({dateFormat:"mm-dd-yy"});
                }
            },
        });

        myGrid.jqGrid('navGrid','#pager',{cloneToTop:true,edit:false,add:false,del:true,search:false,refresh:false,deltext:'Del SKU'},
            {
                recreateForm:true,
                jqModal:false,
                reloadAfterSubmit:false,
                closeAfterEdit:true,
                savekey: [true,13],
                closeAfterEdit:true,
                zIndex:1000,
                width: 450,

                beforeInitData: function(formid) {
                    $("#grid").jqGrid('setColProp','ID',{editable:true});
                },
                afterShowForm: function (formid) {
                    $("#grid").jqGrid('setColProp','ID',{editable:false});
                },
            });
        myGrid.jqGrid('setFrozenColumns');
        add_top_bar(myGrid);
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

    });


    $(window).resize(resize_the_grid);
    function add_top_bar(grid){

        jQuery(grid).jqGrid('navButtonAdd', '#' + jQuery(grid)[0].id + '_toppager_left', { // "#list_toppager_left"
            caption: "Excel",
            buttonicon: 'ui-icon-document',
            onClickButton: function(){
                exportExcel(grid,'MRP_Report');
            }
        });
    }


function linksku(cellvalue,options,rowData){
    if (cellvalue!=null){
        return '<a id="t'+cellvalue+'" href="#"  onclick="showDialog('+cellvalue+',\'<?=base_url()?>\');">' + cellvalue + '</a>';
    }else{
        return '';
    }
}

    function formatPhoneNumber(cellvalue, options, rowObject) {
        var re = new RegExp("([0-9])([0-9]{3})([0-9]{3})([0-9]{3,6})", "g");
        cellvalue=cellvalue.replace(re, "+$1($2)-$3-$4");
        return cellvalue;
    }

    function checkboxFormatter(cellvalue, options, rowObject) {
        cellvalue = cellvalue + "";
        cellvalue = cellvalue.toLowerCase();
        var bchk = cellvalue.search(/(false|0|no|off|n)/i) < 0 ? " checked=\"checked\"" : "";
        return "<input type='checkbox' onclick=\"ajaxSave('" + options.rowId + "', this,'"+grid+"');\" " + bchk + " value='" + cellvalue + "' name='"+options.colModel.name+"' id='"+options.colModel.name+options.rowId+"' offval='no' />";
    }

    function ajaxSave(rowid, curCheckbox) {
        var field = curCheckbox.name;
        var value = curCheckbox.checked;
        var request = $.ajax({
            url: "editHousing",
            type: "POST",
            data: { id : rowid,
                checkbox: field,
                value: value
            },
        });

        request.done(function( msg ) {
            jQuery("#grid").trigger('reloadGrid');
        });
    }


</script>

