<style type="text/css" media="screen">
    .ui-icon.myicon {
        width: 16px;
        height: 16px;
        background-image:url('<?= base_url() ?>images/toolbar/Excel-icon2.png')
    }

</style>
<?php
$formopen = array('id' => 'target', 'class' => 'myform');
$asin = array('id'=>'search','name' => 'search', 'value' => '', 'autocomplete' => 'on', 'maxlength' => '250', 'size' => '20');
$dropdown = 'class="form-select" id="countries"';
$submit = array('name' => 'send', 'value' => 'Submit', 'type' => 'submit', 'class' => 'button');
$reset = array('name' => 'reset', 'value' => 'Reset', 'type' => 'submit', 'class' => 'button');

echo form_open(base_url() . 'index.php' . $from, $formopen);
?>
<fieldset class="bluegradiant">
    <div class="logo"><img longdesc="MI Technologies"  title="Mi Tech" alt="43" src="<?= base_url() ?>/images/header/mitechnologies.png"></div>
    <div class="form-data ">
        <center>
            <table>
                <tbody>
                <tr>
                    <td><?=form_label('Asin: ')?></td>
                    <td><?=form_input($asin)?></td>
                    <td><?=form_dropdown('countries', $countries,'US', $dropdown);?></td>
                    <td colspan="4"><?=form_input($submit)?></td>
                </tr>
                </tbody>
            </table>
        </center>
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

         executeSP = function (Asin, Country) {

            var result=0;
            var request = $.ajax({
                url: 'executeSP',
                type: "POST",
                data: { asin : Asin, country:Country },
                dataType: "json",
                async: false,
            });
            request.done(function( ret ) {
                result = ret;
            });

            return result;
                  
        };


        myGrid.jqGrid({
            url:'getData',
            datatype: "json",
            colNames:<?= $colNames ?>,
            colModel:<?= $colModel ?>,
            autowidth: true,
            pager: jQuery('#pager'),
            rowNum: 50,
            sortname: 'ASIN',
            sortorder: 'asc',
            rowList:[50,300,500,100000000],
            loadComplete: function() { $("option[value=100000000]").text('ALL');},
            rownumbers: true,
            viewrecords: true,
            caption: "<?= $caption ?>",
            height: 600,
            toppager:true,
            editurl:'editData',
        });

        jQuery("#list").jqGrid('navGrid','#pager',{cloneToTop:true,edit:false,add:false,del:false,search:false,refresh:true},
            {},//editOptions
            {}, //addOptions
            {});


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

            var Asin = function() { return jQuery("#search").val(); };
            var Country = function() { return jQuery("#countries option:selected").val(); };

            var ret = executeSP(Asin,Country);

            if (ret.responseText){

                alert('This ASIN was destroyed.');
            }else{

                alert('This ASIN could not be destroyed.');

            }



            myReload();
        });

        //Recargar el grid en el evento onChange del select search del formulario
        $( "#search" ).on('change',function(){
            myReload();
        });


         //Recargar el grid en el evento onChange del select search del formulario
        $( "#fpor" ).on('change',function(){
            myReload();
        });

    });

    $(window).resize(resize_the_grid);



</script>




