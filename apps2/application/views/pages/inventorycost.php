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
        $("list").fluidGrid({base:'#grid_wrapper', offset:-20});
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
            rowNum: 300,
            sortname: 'asc',
            rowList:[300,500,100000000],
            loadComplete: function() { $("option[value=100000000]").text('ALL');},
            rownumbers: true,
            viewrecords: true,
            caption: "<?= $caption ?>",
            height: 600, 
            toppager:true,
            loadonce:true,       

            subGrid: <?= $subgrid ?>,
            subGridRowExpanded: function(subgrid_id, row_id) {    
            var subgrid_table_id, pager_id; 
            subgrid_table_id = subgrid_id+"_t";
            var rData = jQuery("#list").getRowData(row_id);
            var colData = rData['Category'];
            pager_id = "p_"+subgrid_table_id;
            $("#"+subgrid_id).html("<div style='background-color:#363737;width:100%;'>\n\
                                       <center>\n\
                                         <br>\n\
                                           <table id='"+subgrid_table_id+"' class='scroll'></table>\n\
                                         <br>\n\
                                      </center>\n\
                                    </div>\n\
                                    <div id='"+pager_id+"' class='scroll'></div>"
                                    ); 
                jQuery("#"+subgrid_table_id).jqGrid({ 
                    url:'<?= base_url() . 'index.php/' . $from . '/categoryData?on=' ?>'+colData,  
                    datatype: "json",  
                    colNames: ['SKU','Item','QOH','Cost X1','Cost ALL', 'Category'], 
                colModel: [ 
                    {name:'SKU',index:'SKU', width:60, align:'center'},
                    {name:'Item',index:'Item', width:300, align:'left'},
                    {name:'QOH',index:'QOH', width:60, align:'center'},
                    {name:'Cost X1',index:'Cost X1', width:60, align:'center'},
                    {name:'Cost ALL',index:'Cost ALL', width:80, align:'center'},
                    {name:'Category',index:'Category', width:150, align:'center'},
                ], 
                    width:1000,
                    rowNum:300,
                    rowList:[300,500,100000000],
                    loadComplete: function() { $("option[value=100000000]").text('ALL');}, 
                    pager: pager_id, 
                    caption: "Category Information",
                    sortname: 'num', 
                    sortorder: "asc", 
                    height: '100%'
                   
                });
            jQuery("#"+subgrid_table_id).jqGrid('navGrid',"#"+pager_id,{edit:false,add:false,del:false})
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

    });
    
    $(window).resize(resize_the_grid);

function linksku(cellvalue, options,rowData){         
    if (cellvalue!=null){
        return '<a id="t'+cellvalue+'" href="#"  onclick="showDialog('+cellvalue+',\'<?=base_url()?>\');">' + cellvalue + '</a>'; 
    }else{
        return '';
    }
}



</script>