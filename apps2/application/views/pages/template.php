<style type="text/css" media="screen">
    
    .ui-icon.myicon {
        width: 16px;
        height: 16px;
        background-image:url('<?= base_url() ?>images/toolbar/Excel-icon2.png')
    }

</style> 
<?php
$formopen = array('id' => 'target', 'class' => 'myform');
$selectTemplateStore = 'class="form-select" id="TemplateStore"';

echo form_open(base_url() . 'index.php' . $from, $formopen);
?>
<fieldset class="bluegradiant">
    <div class="logo"><img longdesc="MI Technologies"  title="Mi Tech" alt="43" src="<?= base_url() ?>/images/header/mitechnologies.png"></div>
    <div class="form-data"> 
    <center>
            <table>
                <tbody>
                    <tr>
                        <td><?=form_label('Template Store: ')?></td>
                        <td><?=form_dropdown('selectMenu', $TemplateStore,'0', $selectTemplateStore);?></td>
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
                ts:  function() { return jQuery("#TemplateStore option:selected").val(); },
            },
            datatype: "json",
            colNames:<?= $colNames ?>,
            colModel:<?= $colModel ?>,
            autowidth: true,
            pager: jQuery('#pager'),
            rowNum: 300,
            sortname: 'asc',
            rowList:[300,500,100000000],
            loadComplete: function() { $("option[value=100000000]").text('ALL');},
            rownumbers: true,
            cellEdit: true,
            cellurl:'EditData',
            editurl:'<?= base_url() . 'index.php' . $from . '/saveData' ?>',
            viewrecords: true,
            caption: "<?= $caption ?>",
            height: 600, 
            toppager:true,
            subGrid: true,
            subGridOptions: { "expandOnLoad": false, "reloadOnExpand" : false },
            subGridRowExpanded: function(subgrid_id, row_id) {    
                var rData   = jQuery("#list").getRowData(row_id);
                var myid = rData['IndexID'];

                var request = $.ajax({
                url: "getTabs",
                type: "GET",
                data: { id:myid },
                dataType: "html"
                });
                request.done(function( msg ) {
                    $("#"+subgrid_id).html("<div>"+msg+"</div>"); 
                    $( ".tabs" ).tabs().css({'resize':'none','min-height':'380px'});
                });
            },

        
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

        //Recargar el grid en el evento onChange del select search del formulario
        $( "#TemplateStore" ).on('change',function(){
           myReload();
        });

    });
    
    $(window).resize(resize_the_grid);

</script>