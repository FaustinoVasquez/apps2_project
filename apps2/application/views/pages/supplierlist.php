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
            sortname: 'SupplierID',
            sortorder: "asc",
            rowList:[50,300,500,100000000],
            loadComplete: function() { $("option[value=100000000]").text('ALL');},
            rownumbers: true,
            cellEdit: true,
            editurl:'<?= base_url() . 'index.php' . $from . '/EditData' ?>',
            viewrecords: true,
            caption: "<?= $caption ?>",
            height: 600, 
	    loadonce:true,
            toppager:true,
            cellurl:"editSupplier" ,
            editurl:"SaveSupplier",
        
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
                id: "supplier_"+ myGrid[0].id +"_top", 
                title:"Add Supplier", 
                caption: "Supplier",
                buttonicon: 'add',
                onClickButton: function(){

                    $('#SupplierName').val('');
                    $('#ContactName').val('');
                    $('#Email').val('');
                    $('#Phone').val('');
                    $('#Address').val('');
                    $('#City').val('');
                    $('#State').val('');
                    $('#Country').val('');
                    $('#CreditLimit').val('');

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
        var SupplierName = $('#suppliername'),
        ContactName = $('#contactname'),
        Email = $('#email'),
        Phone = $('#phone'),
        Address = $('#address'),
        City = $('#city'),
        State = $('#state'),
        Country = $('#country'),
        CreditLimit = $('#creditlimit'),
       
        allFields = $( [] ).add( SupplierName )
        .add( ContactName )
        .add( Email )
        .add( Phone )
        .add( Address )
        .add( City )
        .add( State )
        .add( Country )
        .add( CreditLimit );

    $( "#dialog-form" ).dialog({
            autoOpen: false,
            resizable: false,
            height:450,
            width:500,
            modal: true,
            buttons: {
                "Add Supplier": function() {
                    var request = $.ajax({
                        url:"SaveSupplier",
                        type: "POST",
                        data:allFields
                    });
                          
                    request.done(function() {
                        alert('Supplier Added')
                        myGrid.trigger("reloadGrid");
                    });
                    $( this ).dialog( "close" );
                },
                Cancel: function() {
                    $('#suppliername').val('');
                    $('#contactname').val('');
                    $('#email').val('');
                    $('#phone').val('');
                    $('#address').val('');
                    $('#city').val('');
                    $('#state').val('');
                    $('#country').val('');
                    $('#creditlimit').val('');
                    $( this ).dialog( "close" );
                }
            },
            close: function() {
                $('#suppliername').val('');
                $('#contactname').val('');
                $('#email').val('');
                $('#phone').val('');
                $('#address').val('');
                $('#city').val('');
                $('#state').val('');
                $('#country').val('');
                $('#creditlimit').val('');
                $( this ).dialog( "close" );
            }
        });
    });

    $(window).resize(resize_the_grid);

</script>

<div id="dialog-form" title="Add Supplier">
        <?php

        $formopen2 = array( 'id' => "supplier" , 'class' => 'myform' ) ;
        echo form_open( '' , $formopen2 ) ;

        ?>
    <fieldset class="bluegradiant">
        <div > 
        <?php

    //    $inputOID = array('name' => 'OrderID', 'id' => 'orderid', 'value' => '', 'class' => 'itemdisable', 'maxlength' => '250', 'size' => '40');
        $inputSupplierName    = array( 'name' => 'SupplierName' , 'id' => 'suppliername' , 'maxlength' => '250' , 'size' => '40' ) ;
        $inputContactName  = array( 'name' => 'ContactName' , 'id' => 'contactname' , 'maxlength' => '250' , 'size' => '40' ) ;
        $inputEmail = array( 'name' => 'Email' , 'id' => 'email' , 'maxlength' => '250' , 'size' => '40' ) ;
        $inputPhone = array( 'name' => 'Phone' , 'id' => 'phone' , 'value' => '' , 'maxlength' => '250' , 'size' => '40' ) ;
        $inputAddress = array( 'name' => 'Address' , 'id' => 'address' , 'maxlength' => '250' , 'size' => '40' ) ;
        $inputCity = array( 'name' => 'City' , 'id' => 'city' , 'maxlength' => '250' , 'size' => '40' ) ;
        $inputState = array( 'name' => 'State' , 'id' => 'state' , 'value' => '' ,  'maxlength' => '250' , 'size' => '40' ) ;
        $inputCountry = array( 'name' => 'Country' , 'id' => 'country' , 'value' => '' ,  'maxlength' => '250' , 'size' => '40' ) ;
        $inputCreditLimit = array( 'name' => 'CreditLimit' , 'id' => 'creditlimit' , 'value' => '' ,  'maxlength' => '250' , 'size' => '40' ) ;
        $submit2 = array( 'name' => 'sendorder' , 'value' => 'Submit' , 'type' => 'submit' , 'class' => 'button' , 'style' => 'visibility:hidden' ) ;          
            
        echo '<table class="dialogtable" style="text-align:left;width:100%">' ;
     //   echo '<tr><td>OrderID:</td><td>' . form_input($inputOID) . '</td></tr>';
        echo '<tr><td>Supplier Name:</td><td>' . form_input( $inputSupplierName ) . '</td></tr>' ;
        echo '<tr><td>Contact Name:</td><td>' . form_input( $inputContactName ) . '</td></tr>' ;
        echo '<tr><td>Email:</td><td>' . form_input( $inputEmail ) . '</td></tr>' ;
        echo '<tr><td>Phone:</td><td>' . form_input( $inputPhone ) . '</td></tr>' ;
        echo '<tr><td>Address:</td><td>' . form_input( $inputAddress ) . '</td></tr>' ;
        echo '<tr><td>City:</td><td>' . form_input( $inputCity ) . '</td></tr>' ;
        echo '<tr><td>State:</td><td>' . form_input( $inputState ) . '</td></tr>' ;
        echo '<tr><td>Country:</td><td>' . form_input( $inputCountry ) . '</td></tr>' ;
        echo '<tr><td>CreditLimit:</td><td>' . form_input( $inputCreditLimit ) . '</td></tr>' ;
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
