

<?php
$formopen = array('id' => 'target', 'id' => "fba_form", 'class' => 'myform');
echo form_open(base_url() . 'index.php' . $from, $formopen);
?>
<fieldset class="bluegradiant">
    <div class="logo"><img longdesc="MI Technologies"
                           title="Mi Tech" alt="43" src="<?= base_url() ?>/images/header/mitechnologies.png">
    </div>
    <div class="form-data"> 
        <?php
        $inputsearch = array('name' => 'search', 'value' => $search, 'autocomplete' => 'on', 'maxlength' => '250', 'size' => '20', 'onclick' => "this.value=''");
        echo "Search:" . form_input($inputsearch);

        $orderdropdown = 'class="form-select" onChange="this.form.submit()"';
        echo form_dropdown('selectedorder', $orderOptions, isset($lineselect->selectedorder) ? $lineselect->selectedorder : $this->input->post('selectedorder'), $orderdropdown);

        echo '<br>';
        $inputfrom = array('id' => 'from', 'name' => 'datefrom', 'class' => 'date-pick', 'value' => $datefrom, 'size' => '10', 'onchange' => "this.form.submit()", 'style' => 'width:80px;text-align:center');
        echo "from:" . form_input($inputfrom);
        echo "&nbsp;&nbsp;";
        $inputto = array('id' => 'from', 'name' => 'dateto', 'class' => 'date-pick', 'value' => $dateto, 'size' => '10', 'onchange' => "this.form.submit()", 'style' => 'width:80px;text-align:center');
        echo "to:" . form_input($inputto);

        echo "&nbsp;&nbsp;";
        $submit = array('name' => 'send', 'value' => 'Submit', 'type' => 'submit', 'class' => 'button');
        echo form_input($submit);

        $reset = array('name' => 'reset', 'value' => 'Reset', 'type' => 'submit', 'class' => 'button');
        echo form_input($reset);
        ?>
    </div>
</fieldset>
<br>
<?php
echo form_close();
?>

 

<style type="text/css" media="screen">
    .ui-icon.excel {
        width: 16px;
        height: 16px;
        background-image:url('<?= base_url() ?>images/toolbar/Excel-icon2.png');
    }
    .ui-icon.asin {
        width: 16px;
        height: 16px;
        background-image:url('<?= base_url() ?>images/toolbar/asin.png');
    }
     .ui-icon.order {
        width: 16px;
        height: 16px;
        background-image:url('<?= base_url() ?>images/toolbar/order.png');
    }

    .ui-autocomplete {
                max-height: 150px;
                overflow-y: auto;
                /* prevent horizontal scrollbar */
                overflow-x: hidden;
            }
            /* IE 6 doesn't support max-height
             * we use height instead, but this forces the menu to always be this tall
            */
    *  html .ui-autocomplete {
                height: 150px;
            }
    #asintable{
                font-size: 1.1em;
                line-height: 29px;
            }
    #asintable input[type=text]{
                height: 20px;
            }

</style>



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
            url:'<?= base_url() . 'index.php' . $from . $gridSearch ?>',
            datatype: "json", 
            rowNum:50,
            rowList:[50,300,1000],
            colNames:<?= $colNames ?>,
            colModel:<?= $colModel ?>,
            pager: jQuery('<?= '#' . $namePager ?>'),
            viewrecords: true,
            rownumbers: true,
            sortname: '<?= $sortname ?>',
            sortorder: '<?= $sortorder ?>',
            caption: '<?= $caption ?>',
            height: 600, 
            toppager:true,
            cloneToTop:true,
            editurl: '<?= base_url() . 'index.php' . $from . 'add' ?>' 
        });

        //Ajustamos el grid a la ventana
        resize_the_grid(myGrid);    
        // agregamos el boton de excel
       
        add_top_bar(myGrid);
 
    });
   

     

    $(window).resize(resize_the_grid);
    
    function add_top_bar(grid){
        jQuery(grid).jqGrid('navGrid','<?='#'.$namePager ?>',{edit:false,add:false,del:false,search:false,refresh:true,cloneToTop:true},{},{width:600});
   
        jQuery(grid).jqGrid('navButtonAdd', '#' + jQuery(grid)[0].id + '_toppager_left', { // "#list_toppager_left"
            caption: "Excel",
            buttonicon: 'excel',
            onClickButton: function(){
                exportExcel(grid);
            }
        });
        
         jQuery(grid).jqGrid('navButtonAdd', '#' + jQuery(grid)[0].id + '_toppager_left', { // "#list_toppager_left"
            caption: "Asin",
            buttonicon: 'asin',
            onClickButton: function(){
                $('#asin').val('');
                $('.enabledbyasin').val('');
                $('.enabledbyasin').attr('disabled','disabled');
                $('.enabledbyfnsku').attr('disabled','disabled');
                $( "#dialog-form" ).dialog( "open" );
            } 
        });
        
        jQuery(grid).jqGrid('navButtonAdd', '#' + jQuery(grid)[0].id + '_toppager_left', { // "#list_toppager_left"
            caption: "Order",
            buttonicon: 'order',
            onClickButton: function(){
                $( "#order-form" ).dialog( "open" );
            } 
        });
        
        var topPagerDiv = $('#' + jQuery(grid)[0].id + '_toppager')[0];         // "#list_toppager"
        $("#edit_" + jQuery(grid)[0].id + "_top", topPagerDiv).remove();        // "#edit_list_top"
        $("#del_" + jQuery(grid)[0].id + "_top", topPagerDiv).remove();         // "#del_list_top"
        $("#search_" + jQuery(grid)[0].id + "_top", topPagerDiv).remove();      // "#search_list_top"
        $("#refresh_" + jQuery(grid)[0].id + "_top", topPagerDiv).remove();     // "#refresh_list_top
        $("#view_" + jQuery(grid)[0].id + "_top", topPagerDiv).remove();         // "#add_list_top"
        

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
   
   
    $(function() {
        $( ".date-pick" ).datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat:"mm/dd/yy"
        });
    }); 
    



    function printticket(cellvalue, options,rowData) {    
        var build_sku = rowData[1];
        var asin = rowData[2];
        var fnsku = rowData[3];
        var description =rowData[8]

        return "<A HREF=\"javascript:popUp('showTicket?bs="+build_sku+"&as="+asin+"&fn="+fnsku+"&des="+description+"')\"><span class=\"ui-icon ui-icon-print\" style=\"display:inline-block;\"></span></A>";
    }
    

    
    
    function popUp(URL) {
        day = new Date();
        id = day.getTime();
        eval("page" + id + " = window.open(URL, '" + id + "', 'toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=0,width=400,height=300,left = 640,top = 325');");
    }
    
    
    
    
    
    $(function() {
        var asin = $( "#asin" ),
            partnumber = $( "#partnumber" ),
            fnsku = $( "#fnsku" ),
            description = $( "#description" ),
            merchansku = $( "#merchansku" ),
            sku = $( "#sku" ),
            orderid = $( "#orderid" ),
            orderdate = $( "#orderdate" ),
            orderqty = $( "#orderqty" ),
            ordernotes = $( "#ordernotes" ),
            addtoamazon = $( "#addtoamazon" ),
            AsinFields = $( [] ).add( asin ).add( partnumber ).add( fnsku ).add( description ).add( merchansku ).add( sku ),
            OrderFields = $( [] ).add( orderid ).add( asin ).add( sku ).add( fnsku ).add( description ).add( orderdate ).add( orderqty ).add( ordernotes ).add( addtoamazon ),
            tips = $( ".validateTips" );
 
 
        function updateTips( t ) {
            tips
                .text( t )
                .addClass( "ui-state-highlight" );
            setTimeout(function() {
                tips.removeClass( "ui-state-highlight", 1500 );
            }, 500 );
        }
 
        function checkLength( o, n, min, max ) {
            if ( o.val().length > max || o.val().length < min ) {
                o.addClass( "ui-state-error" );
                updateTips( "Length of " + n + " must be between " +
                    min + " and " + max + "." );
                return false;
            } else {
                return true;
            }
        }
 
        function checkRegexp( o, regexp, n ) {
            if ( !( regexp.test( o.val() ) ) ) {
                o.addClass( "ui-state-error" );
                updateTips( n );
                return false;
            } else {
                return true;
            }
        }
 
        $( "#dialog-form" ).dialog({
            autoOpen: false,
            height: 310,
            width: 500,
            modal: true,
            buttons: {
                "Create an account": function() {
                    var bValid = true;
                    AsinFields.removeClass( "ui-state-error" );
 
                    bValid = bValid && checkLength( asin, "asin", 10, 15 );
                    bValid = bValid && checkLength( partnumber, "partnumber", 1, 30 );
                    bValid = bValid && checkLength( fnsku, "fnsku", 10, 10 );
                    bValid = bValid && checkLength( description, "description", 5, 250 );
                    bValid = bValid && checkLength( merchansku, "merchansku", 10, 15 );
                    
                    if ( bValid ) {
                        $("#fba_adasin").submit(); 
                        $( this ).dialog( "close" );  
                    }
                       
                },
                Cancel: function() {
                    $( this ).dialog( "close" );
                }
            },
            close: function() {
                AsinFields.val( "" ).removeClass( "ui-state-error" );
            }
        });
        
 
        $( "#partnumber" ).autocomplete({
                    source: "<?= base_url() ?>index.php/tools/amazonfba/FillPartNumber"
                });
                
        $('#asin').click(function() {
                    $('.enabledbyasin').removeAttr('disabled');
                });
                
                $('#asin').data('val', $('#asin').val());
                $('#asin').change(function(){
                    $('#merchansku').val($('#asin').val());
                });
                
                $('#asin').keyup(function() { // Se activa inmediatemente cuando elusuario presiona una tecla dentro del input
                    if( $('#asin').val() != $('#asin').data('val') ){ // checa si el valor cambio
                        $('#asin').data('val',  $('#asin').val() ); // guarda el valor 
                        $(this).change(); // simila el evento "change"
                    } 
                });        
                
          $('#fnsku').click(function() {
                    $('.enabledbyfnsku').removeAttr('disabled');  
                });
                

                $("#fnsku").focusout(function() {
                    var partnumber = $("#partnumber").val(); 
                    
                 var request = $.ajax({
                        url:"<?= base_url() ?>index.php/tools/amazonfba/getSKU",
                        type: "POST",
                        data:{pn: partnumber}
                    });
                    
                    request.done(function(ret) {
                        $("#sku").html( ret );
                    });
                });   
                
                
                
             
                
    });
    
   

 

</script>
<div id="dialog-form" title="Create Assin">
 <?php
        $formopen1 = array('id' => "fba_adasin", 'class' => 'myform');
        echo form_open(base_url() . 'index.php/tools/amazonfba/saveasin', $formopen1);
        ?>
        <fieldset class="bluegradiant">
            <div > 
                <?php
                $inputasin = array('name' => 'Asin', 'id' => 'asin', 'value' => '', 'maxlength' => '250', 'size' => '40');
                $inputPN = array('name' => 'PartNumber', 'id' => 'partnumber', 'class' => 'enabledbyasin', 'maxlength' => '250', 'size' => '40');
                $inputFNKU = array('name' => 'FNSKU', 'id' => 'fnsku', 'class' => 'enabledbyasin', 'maxlength' => '250', 'size' => '40');
                $inputAD = array('name' => 'Description', 'id' => 'description', 'value' => '','class' => 'enabledbyfnsku', 'maxlength' => '250', 'size' => '40');
                $inputMSKU = array('name' => 'MerchanSKU', 'id' => 'merchansku', 'class' => 'enabledbyasin', 'maxlength' => '250', 'size' => '40');
                $submit1 = array('name' => 'sendasin','value' => 'Submit', 'type' => 'submit', 'class' => 'button', 'style'=>'visibility:hidden');
                echo '<table id="asintable" style="text-align:left;width:100%">';
                echo '<tr><td>Amazon Asin:</td><td>' . form_input($inputasin) . '</td></tr>';
                echo '<tr><td>PartNumber:</td><td>' . form_input($inputPN) . '</td></tr>';
                echo '<tr><td>Amazon FNSKU:</td><td>' . form_input($inputFNKU) . '</td></tr>';
                echo '<tr><td>Amazon Description:</td><td>' . form_input($inputAD) . '</td></tr>';
                echo '<tr><td>Merchant SKU:</td><td>' . form_input($inputMSKU) . '</td></tr>';
                echo '<tr><td>Build SKU:</td><td><select name="SKU" id="sku" ></select></td></tr>';
                echo '</table>';
                echo form_input($submit1);
                ?>
            </div>
        </fieldset>
        <br>
        <?php
        echo form_close();
        ?>
</div>

<div id="order-form" title="Create Order">
 <?php
        $formopen2 = array('id' => "order", 'class' => 'myform');
        echo form_open(base_url() . 'index.php/tools/amazonfba/saveorder', $formopen2);
        ?>
        <fieldset class="bluegradiant">
            <div > 
                <?php
                $inputOID = array('name' => 'OrderID', 'id' => 'orderid', 'value' => '', 'class' => 'itemdisable', 'maxlength' => '250', 'size' => '40');
                $inputASIN = array('name' => 'ASIN', 'id' => 'asin', 'class' => 'itemdisable', 'maxlength' => '250', 'size' => '40');
                $inputSKU = array('name' => 'SKU', 'id' => 'sku', 'class' => 'itemdisable', 'maxlength' => '250', 'size' => '40');
                $inputfnsku = array('name' => 'FNSKU', 'id' => 'fnsku', 'value' => '','class' => 'itemdisable', 'maxlength' => '250', 'size' => '40');
                $inputDesc = array('name' => 'Description', 'id' => 'description', 'class' => 'itemdisable', 'maxlength' => '250', 'size' => '40');
                $inputOD = array('name' => 'OrderDate', 'id' => 'orderdate', 'class' => 'itemdisable', 'maxlength' => '250', 'size' => '40');
                $inputQTY = array('name' => 'OrderQTY', 'id' => 'orderqty', 'value' => '', 'maxlength' => '250', 'size' => '40');
                $inputON = array('name' => 'OrderNotes', 'id' => 'ordernotes', 'maxlength' => '250', 'size' => '40');
                $submit2 = array('name' => 'sendasin','value' => 'Submit', 'type' => 'submit', 'class' => 'button', 'style'=>'visibility:hidden');
                echo '<table id="asintable" style="text-align:left;width:100%">';
                echo '<tr><td>OrderID:</td><td>' . form_input($inputOID) . '</td></tr>';
                echo '<tr><td>Amazon Asin:</td><td>' . form_input($inputASIN) . '</td></tr>';
                echo '<tr><td>Build SKU:</td><td>' . form_input($inputSKU) . '</td></tr>';
                echo '<tr><td>Amazon FNSKU:</td><td>' . form_input($inputfnsku) . '</td></tr>';
                echo '<tr><td>Amazon Description:</td><td>' . form_input($inputDesc) . '</td></tr>';
                echo '<tr><td>Order Date:</td><td>' . form_input($inputOD) . '</td></tr>';
                echo '<tr><td>Order Quantity:</td><td>' . form_input($inputQTY) . '</td></tr>';
                echo '<tr><td>Order Notes:</td><td>' . form_input($inputON) . '</td></tr>';
                echo '<tr><td>Add to Amazon:</td><td><select name="AddToAmazon" id="addtoamazon" ><option>YES</option><option>NO<option></select></td></tr>';
                echo '</table>';
                echo form_input($submit2);
                ?>
            </div>
        </fieldset>
        <br>
        <?php
        echo form_close();
        ?>
</div>