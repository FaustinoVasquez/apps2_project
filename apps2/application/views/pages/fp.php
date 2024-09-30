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
$selectManufarurer = 'class="form-select" id="manufacturer"';
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
    echo form_dropdown('Manufacturer', $manufacturer,'0', $selectManufarurer);
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
            url:'getData',
            postData: {
                ds:   function() { return jQuery("#search").val(); },
                man:  function() { return jQuery("#manufacturer option:selected").val(); },
            },
            datatype: "json",
            colNames:<?= $colNames ?>,
            colModel:<?= $colModel ?>,
            autowidth: true,
            pager: jQuery('<?= '#' . $namePager ?>'),
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
        });
 
        jQuery("#list").jqGrid('navGrid','#pager',{edit:false,add:false,del:false,search:false,refresh:true});
        resize_the_grid();
        add_top_bar(myGrid);


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
            $( "#manufacturer" ).on('change',function(){
               myReload();
            });

    });
    
    $(window).resize(resize_the_grid);

    
    function add_top_bar(grid){
        jQuery(grid).jqGrid('navGrid','<?= '#' . $namePager ?>',{edit:false,add:false,del:false,search:false,refresh:true,cloneToTop:true},{},{width:600});
   
        jQuery(grid).jqGrid('navButtonAdd', '#' + jQuery(grid)[0].id + '_toppager_left', { // "#list_toppager_left"
            caption: "Excel",
            buttonicon: 'excel',
            onClickButton: function(){
                exportExcel(grid);
            }
        }); 
    }



    function exportExcel(grid)
    {
        var mya=new Array();
        mya=$(grid).getDataIDs();  // Get All IDs
        var data=$(grid).getRowData(mya[0]);     // Get First row to get the labels
        if (data['stats']){delete data['stats']}
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
            data=$(grid).getRowData(mya[i]); // get each row
            for(j=0;j<colNames.length;j++)
            {   
                if (colNames[j]=='OrderNumber'){               
                    data[colNames[j]]=data[colNames[j]].toUpperCase();
                    var startPos=(data[colNames[j]].indexOf('>')) +1;
                    var endPos=(data[colNames[j]].indexOf('<',startPos));
                    data[colNames[j]]=data[colNames[j]].toString().substring(startPos,endPos);
                }
 
                html=html+data[colNames[j]]+"\t"; // output each Row as tab delimited
            }

            html=html+"\n";  // output each row with end of line
        }

        html=html+"\n";  // end of line at the end
        document.forms[1].csvBuffer.value=html;
        document.forms[1].method='POST';
        document.forms[1].action='<?= base_url() . 'index.php' . $from . 'csvExport/' . $export ?>'; 
        document.forms[1].target='_blank';
        document.forms[1].submit();
    }


function linksku(cellvalue, options,rowData){         
    if (cellvalue!=null){
        return '<a id="t'+cellvalue+'" href="#"  onclick="showDialog('+cellvalue+');">' + cellvalue + '</a>'; 
    }else{
        return '';
    }
}


function showDialog(sku)
{   
    var newTitle = "SKU( "+sku+" ) Details." 
   // $("#dialog-modal").attr("title", newTitle);
    $("#dialog-modal").empty();
    $("#dialog-modal").dialog(
    {
        width: 1200,
        height: 530,
        modal:true,
        open: function(event, ui)
        {
            var prueba = ('<div id="tabs">'+
                            '<ul>'+
                                '<li><a href="#tabs-1">SkuDAta</a></li>'+
                                '<li><a href="#tabs-2">Assembly Requirements</a></li>'+
                                '<li><a href="#tabs-3">Compatibilities</a></li>'+
                                '<li><a href="#tabs-4">Images</a></li>'+
                                '<li><a href="#tabs-5">Attachment</a></li>'+
                                '<li><a href="#tabs-6">History</a></li>'+
                                '<li><a href="#tabs-7">Inventory Details</a></li>'+
                                '<li><a href="#tabs-8">Assambly Requirements</a></li>'+
                                '<li><a href="#tabs-9">PO History</a></li>'+
                                '<li><a href="#tabs-10">Advanced</a></li>'+
                            '</ul>'+
                            '<div id="tabs-1">'+
                                '<iframe src="<?=$baseUrl?>index.php/Tabs/showskudata/'+sku+'" width="100%" height=400px frameborder="0"></iframe>'+
                            '</div>'+
                            '<div id="tabs-2">'+
                                '<iframe src="<?=$baseUrl?>index.php/Tabs/customfields/'+sku+'" width="100%" height=400px frameborder="0"></iframe>'+
                            '</div>'+
                            '<div id="tabs-3">'+
                                '<iframe src ="<?=$baseUrl?>index.php/Tabs/compat?q='+sku+'" width="100%" height=400px frameborder="0"></iframe>'+
                            '</div>'+
                            '<div id="tabs-4">'+
                                '<iframe src ="http://photos.discount-merchant.com/photos/sku/'+sku+'/" width="100%" height=400px frameborder="0"></iframe>'+
                            '</div>'+
                            '<div id="tabs-5">'+
                                '<iframe src ="http://photos.discount-merchant.com/photos/sku/'+sku+'/Attachments/" width="100%" height=400px frameborder="0"></iframe>'+
                            '</div>'+
                            '<div id="tabs-6">'+
                                '<iframe src ="<?=$baseUrl?>index.php/Tabs/history/'+sku+'" style="width:100%;height:400px; border-width:0px;"></iframe>'+
                            '</div>'+
                            '<div id="tabs-7">'+
                                ' <iframe src ="<?=$baseUrl?>index.php/Tabs/invdetailsnew/'+sku+'" style="width:100%;height:400px; border-width:0px;"></iframe>'+
                            '</div>'+
                            '<div id="tabs-8">'+
                                '<iframe src ="<?=$baseUrl?>index.php/Tabs/assemblyreq?q='+sku+'" style="width:100%;height:400px; border-width:0px;"></iframe>'+
                            '</div>'+
                            '<div id="tabs-9">'+
                                '<iframe src ="<?=$baseUrl?>index.php/Tabs/pohistory/'+sku+'" style="width:100%;height:400px; border-width:0px;"></iframe>'+
                            '</div>'+
                            '<div id="tabs-10">'+
                                '<iframe src ="<?=$baseUrl?>index.php/Tabs/advanced/'+sku+'" style="width:100%;height:400px; border-width:0px;"></iframe>'+
                            '</div>'+
                        '</div>'); 

           $(this).append(prueba);
           $( "#tabs" ).tabs();

        }
    }); 
}




</script>

<div id="dialog-modal" title="Details" style="display: none;"></div>
