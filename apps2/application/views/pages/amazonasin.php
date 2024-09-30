<style type="text/css" media="screen">


</style>
<?php
$formopen = array('id' => 'target', 'class' => 'myform');
$inputsearch = array('id'=>'search','name' => 'search', 'value' => '', 'autocomplete' => 'on', 'maxlength' => '250', 'size' => '20');
$inputSelect = 'class="form-select" id="selectmenu"';
$inputbusqueda = array('id'=>'busqueda','name' => 'busqueda', 'value' => '', 'autocomplete' => 'on', 'maxlength' => '250', 'size' => '20');
$submit = array('name' => 'send', 'value' => 'Submit', 'type' => 'submit', 'class' => 'button');
$reset = array('name' => 'reset', 'value' => 'Reset', 'type' => 'submit', 'class' => 'button');

echo form_open(base_url() . 'index.php' . $from, $formopen);
?>
<fieldset class="bluegradiant">
    <div class="logo"><img longdesc="MI Technologies"  title="Mi Tech" alt="43" src="<?= base_url() ?>/images/header/mitechnologies.png"/></div>
    <div class="form-data"> 
        <center>
            <table>
                <tbody>
                    <tr>
                        <td><?=form_label('Submit: ')?></td>
                        <td><?=form_input($inputsearch)?></td>
                        <? '&nbsp;' ?><td><?=form_dropdown('status',$selectData,'0',$inputSelect);?></td>
                        <td><?=form_label('Search: ')?></td>
                        <td><?=form_input($inputbusqueda)?></td>
                    </tr>
                    <tr>
                        <td colspan="5"><center><?=form_input($submit).'&nbsp;'.form_input($reset);?></center></td>
                    </tr>
                </tbody>
            </table>
        <center>
        <br>
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
                ds:  function() { return jQuery("#search").val(); },
                op:  function() { return jQuery("#selectmenu option:selected").val(); },
                bu:  function() { return jQuery("#busqueda").val(); }, 
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
            editurl:'<?= base_url() . 'index.php' . $from . '/saveData' ?>',       
        });

         jQuery("#list").jqGrid('navGrid','#pager',{cloneToTop:true,edit:true,add:false,del:false,search:false,refresh:true},{
            beforeShowForm: function(form) { $('#tr_Adjustment_Id', form).hide(); },
              reloadAfterSubmit:false,
              closeAfterEdit: true,
              closeOnEscape:true,
              recreateForm: true,
              width:500
        });

        myGrid.jqGrid('navButtonAdd', '#' + myGrid[0].id + '_toppager_left',{ // "#list_toppager_left"
            caption: "Excel",
            buttonicon: 'myicon',
            onClickButton: function(){
                exportExcel(myGrid); 
            }
        });

        myGrid.jqGrid('navButtonAdd', '#' + myGrid[0].id + '_toppager_left', { // "#list_toppager_left"
            caption: "Delete",
            buttonicon: 'del',
            onClickButton: function(){
                deleteAllInfo();
                myReload(); 
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

            var search = $("#search").val();
            var selectOption = $("#selectmenu option:selected").val();

            var isDuplicated = miAjax('getDuplicated',search);
            var doesntExist = miAjax('getExistence',search);
            var hasTracking = miAjax('getTracking',search);

            switch(selectOption){
                case '0':
                    alert('You cant submit orders in Consolidate');
                    break;

                case '1':
                    if(isDuplicated){alert('Repeated Order');} else {
                        if(hasTracking){alert('This Order has Tracking');}
                        else {myReload();}
                    }
                    break;

                case '2':
                    if(!doesntExist){alert('Order doesnt Exist');} 
                    else {myReload();}
                    break;

                case '3':
                    if(!hasTracking){alert('This Order has not Tracking');}
                    else {myReload();}
                    break;
            }
            
            $("#search").val('').focus();
        });



        $( "#selectmenu" ).on('change',function(){
               myReload();
            });

        miAjax = function(myUrl,myData){
            var result=0;
            var request = $.ajax({
                url: myUrl,
                type: "GET",
                data: { sh : myData },
                dataType: "json",
                async: false,
            });
            request.done(function( ret ) {
                result = ret;
            });
            request.fail(function( jqXHR, textStatus ) {
                alert( "Request failed: " + textStatus );
            });
            
            return result;
        }

        deleteAllInfo = function()
        {
            var request = $.ajax({
                url: "deleteInfo",
                type: "POST",
                dataType: "json",
                async: false,
            });
            myReload();
        }
    });

$(window).resize(resize_the_grid);

</script>