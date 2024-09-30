<style type="text/css" media="screen">
    

    .ui-icon.myicon {
        width: 16px;
        height: 16px;
        background-image:url('<?= base_url() ?>images/toolbar/Excel-icon2.png')
    }
    
    .ui-icon.add {
        width: 16px;
        height: 16px;
        background-image:url('<?= base_url() ?>images/toolbar/add.png');
    }
    
    *  html .ui-autocomplete {
        height: 150px;
    }
    .dialogtable{
        font-size: 1.1em;
        line-height: 29px;
    }
    .dialogntable input[type=text]{
        height: 20px;
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
    <center>
            <table>
                <tbody>
                     <tr>
                        <td><?=form_label('Search: ')?></td>
                        <td><?=form_input($inputsearch)?></td>
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
            },
            datatype: "json",
            colNames:<?= $colNames ?>,
            colModel:<?= $colModel ?>,
            autowidth: true,
            pager: jQuery('#pager'),
            rowNum: 50,
            sortname: 'Stamp',
            sortorder: "desc",
            rowList:[50,300,500,100000000],
            loadComplete: function() { $("option[value=100000000]").text('ALL');},
            rownumbers: true,
            editurl:'<?= base_url() . 'index.php' . $from . '/EditData' ?>',
            viewrecords: true,
            caption: "<?= $caption ?>",
            height: 600, 
            toppager:true,
            editurl:"SaveRec",
        
        });
 
        jQuery("#list").jqGrid('navGrid','#pager',{cloneToTop:true,edit:false,add:false,del:false,search:false,refresh:true},{
            beforeShowForm: function(form) { $('#tr_Adjustment_Id', form).hide(); },
              reloadAfterSubmit:false,
              closeAfterEdit: true,
              closeOnEscape:true,
              recreateForm: true,
              width:500
        });


        myGrid.jqGrid('navButtonAdd', '#' + myGrid[0].id + '_toppager_left', { // "#list_toppager_left"
            caption: "Excel",
            buttonicon: 'myicon',
            onClickButton: function(){
                exportExcel(myGrid,'<?= $export ?>'); 
            }
        });

        myGrid.jqGrid('navButtonAdd', '#' + myGrid[0].id + '_toppager_left', { // "#list_toppager_left"
                id: "record_"+ myGrid[0].id +"_top", 
                title:"Add Record", 
                caption: "Record",
                buttonicon: 'add',
                onClickButton: function(){

                    $('#Stamp').val('');
                    $('#PO').val('');
                    $('#Tracking').val('');
                    $('#Called').val('');
                    $('#Notes').val('');

                    $( "#dialog-form" ).dialog( "open" );
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

    $(function() {
        var Stamp = $('#stamp'),
        PO = $('#po'),
        Tracking = $('#tracking'),
        Called = $('#called'),
        Notes = $('#notes'),
       
        allFields = $( [] ).add( Stamp )
        .add( PO )
        .add( Tracking )
        .add( Called )
        .add( Notes );


        $('#called').click(function(){
            if($(this).is(':checked'))
                { $(this).val(1); } 
            else 
                { $(this).val(0); }
        });

        $( "#dialog-form" ).dialog({
            autoOpen: false,
            resizable: false,
            height:300,
            width:350,
            modal: true,
            buttons: {
                "Add Record": function() {
                    var request = $.ajax({
                        url:"SaveRec",
                        type: "POST",
                        data:allFields
                    });
                          
                    request.done(function() {
                        alert('Record Added')
                        myGrid.trigger("reloadGrid");
                    });
                    $( this ).dialog( "close" );
                },
                Cancel: function() {
                    $('#stamp').val('');
                    $('#po').val('');
                    $('#tracking').val('');
                    $('#called').val('');
                    $('#notes').val('');
                    $( this ).dialog( "close" );
                }
            },
            close: function() {
                $('#stamp').val('');
                $('#po').val('');
                $('#tracking').val('');
                $('#called').val('');
                $('#notes').val('');
                $( this ).dialog( "close" );
            }
        });
    });

    $(function() {
        $( ".date-pick" ).datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat:"mm/dd/yy"
        });
    }); 

    $(window).resize(resize_the_grid);

    function yesno(cellvalue, options,rowObject)
    {         
        if (cellvalue==0)
        {
            return 'No'; 
        }
        else
        {
            return 'Yes';
        }
    }

</script>

<div id="dialog-form" title="Add Record">
        <?php

        $formopen2 = array( 'id' => "record" , 'class' => 'myform' ) ;
        echo form_open( '' , $formopen2 ) ;

        ?>
    <fieldset class="bluegradiant">
        <div > 
        <?php

    //    $inputOID = array('name' => 'OrderID', 'id' => 'orderid', 'value' => '', 'class' => 'itemdisable', 'maxlength' => '250', 'size' => '40');
        $inputStamp   = array('name' => 'Stamp' , 'id' => 'stamp' ,'class' => 'date-pick', 'value' => $Stamp, 'size' => '10', 'style' => 'width:80px;text-align:center') ;
        $inputPO  = array( 'name' => 'PO' , 'id' => 'po' , 'maxlength' => '250' , 'size' => '40' ) ;
        $inputTracking = array( 'name' => 'Tracking' , 'id' => 'tracking' , 'maxlength' => '250' , 'size' => '40' ) ;
        $inputCalled = array( 'name' => 'Called' , 'id' => 'called', 'value' => '1', 'checked' => 'checked','class' => 'checkbox') ;
        $inputNotes = array( 'name' => 'Notes' , 'id' => 'notes' , 'maxlength' => '250' , 'size' => '40' ) ;
        $submit2 = array( 'name' => 'sendorder' , 'value' => 'Submit' , 'type' => 'submit' , 'class' => 'button' , 'style' => 'visibility:hidden' ) ;          
            
        echo '<table class="dialogtable" style="text-align:left;width:100%">' ;
        //  echo '<tr><td>OrderID:</td><td>' . form_input($inputOID) . '</td></tr>';
            echo '<tr><td>Timestamp: </td><td>' . form_input($inputStamp).'</td></tr>' ;
            echo '<tr><td>PO: </td><td>' . form_input( $inputPO ) . '</td></tr>' ;
            echo '<tr><td>Tracking#: </td><td>' . form_input( $inputTracking ) . '</td></tr>' ;
            echo '<tr><td>Called?: </td><td>' .form_checkbox( $inputCalled ).'</td></tr>' ;
            echo '<tr><td>Notes: </td><td>' . form_input( $inputNotes ) . '</td></tr>' ;
        echo '</table>' ;
        echo form_input( $submit2 ) ;

        ?>
        </div>
    </fieldset>
    <br>
<?php

echo form_close() ;

?>
</div>