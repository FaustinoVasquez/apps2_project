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
            <table>
                <tbody>
                    <tr>
                        <td><?=form_label('Search: ')?></td>
                        <td><?=form_input($inputsearch)?></td>
                        <td><?=form_dropdown('selectMenu', $category,'0', $selectCategory);?></td>
                    </tr>
                    <tr>
                        <td colspan="4"><center><?=form_input($submit).'&nbsp;'.form_input($reset);?></center></td>
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
                ca:  function() { return jQuery("#category option:selected").val(); },
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
            viewrecords: true,
            caption: "<?= $caption ?>",
            height: 600, 
            toppager:true,
            shrinkToFit: false,

            subGrid: <?= $subgrid ?>,
            subGridOptions: { "expandOnLoad": true, "reloadOnExpand" : false },
            subGridRowExpanded: function(subgrid_id, row_id) {    
            var subgrid_table_id, pager_id; 
            subgrid_table_id = subgrid_id+"_t";
            var rData = jQuery("#list").getRowData(row_id);
            var itemId = rData['ITEMID'];
            var dir = 1001+(Math.floor((itemId-1000000)/4000));
            pager_id = "p_"+subgrid_table_id;
            var urlTh = "http://photos.discount-merchant.com/photos/"+dir+"/th/"+itemId+"001th.jpg";
            var url = "http://photos.discount-merchant.com/photos/"+dir+"/"+itemId+"001.jpg";
            $("#"+subgrid_id).html("<div style='width:100%;'>\n\
                                    <a href='"+url+"' target='_blank'><img src='"+urlTh+"'></img></a> \n\
                                    </div>\n\
                                    "
                                    );
        }        
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

        //Recargar el grid en el evento onChange del select search del formulario
        $( "#category" ).on('change',function(){
           myReload();
        });

    });
    
    $(window).resize(resize_the_grid);

</script>