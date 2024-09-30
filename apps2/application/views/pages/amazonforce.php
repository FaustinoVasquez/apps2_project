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
$dropdown = 'class="form-select" id="fpor"';
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
                    <td><?=form_label('Search: ')?></td>
                    <td><?=form_input($inputsearch)?></td>
                    <td colspan="4"><?=form_input($submit).'&nbsp;'.form_input($reset);?></td>
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

         validateAsin = function (value, colname) {

            var result=0;
            var request = $.ajax({
                url: 'validateAsin',
                type: "GET",
                data: { asin : value },
                dataType: "json",
                async: false,
            });
            request.done(function( ret ) {
                result = ret;
            });


           if (result == 0){

            return [false,"This ASIN is not in the ASIN table"];
           }
           else
           {
            return [true];
           };

                  
        };


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
            rowNum: 50,
            sortname: 'AZFD.[ASIN]',
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

        jQuery("#list").jqGrid('navGrid','#pager',{cloneToTop:true,edit:false,add:true,del:true,search:false,refresh:true},
            {},//editOptions
            { 
                recreateForm:true,
                jqModal:false,
                reloadAfterSubmit:false,
                closeAfterAdd:true,
                savekey: [true,13],
                zIndex:1000,
                 beforeSubmit: function(postdata, formid){

                                    var result=0;
                                    var request = $.ajax({
                                        url: 'validateAsin1',
                                        type: "GET",
                                        data: { data : postdata },
                                        dataType: "json",
                                        async: false,
                                    });
                                    request.done(function( ret ) {
                                        result = ret;
                                    });


                                   if (result == 0){
                                        return [false,"This ASIN an country Exists! "];
                                   }
                                   else{
                                        return [true];
                                   };

                                },
                afterSubmit:function(response,postdata)
                 {
                 $("#list").jqGrid().trigger('reloadGrid');
                 return [true,"",''];
                 },
            }, //addOptions
            {
                reloadAfterSubmit:false
            });


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
        $( "#fpor" ).on('change',function(){
            myReload();
        });

    });

    $(window).resize(resize_the_grid);



</script>


