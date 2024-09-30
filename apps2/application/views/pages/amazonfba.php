
<?php

$formopen = array( 'id' => 'target' , 'id' => "fba_form" , 'class' => 'myform' ) ;
echo form_open( base_url() . 'index.php' . $from , $formopen ) ;

?>
<fieldset class="bluegradiant">
    <div class="logo"><img longdesc="MI Technologies"
                           title="Mi Tech" alt="43" src="<?= base_url() ?>/images/header/mitechnologies.png">
    </div>
    <div class="form-data"> 
        
	<?php
	$inputsearch = array( 'id'=>'mysearch','name' => 'search' , 'value' => '' , 'autocomplete' => 'on' , 'maxlength' => '250' , 'size' => '20' , 'onclick' => "this.value=''" ) ;
	echo "Search:" . form_input( $inputsearch ) ;

	$dropdown = 'id = "orderselect" class="form-select" onChange="this.form.submit()"' ;
	echo form_dropdown( 'selectedorder' , $orderOptions , isset( $lineselect->selectedorder ) ? $lineselect->selectedorder : $this->input->post( 'selectedorder' ) , $dropdown ) ;
	

	if( $selectedorder != 0 ) {
	    echo '<br>' ;
	    $inputfrom = array( 'id' => 'from' , 'name' => 'datefrom' , 'class' => 'date-pick' , 'value' => $datefrom , 'size' => '10' , 'onchange' => "this.form.submit()" , 'style' => 'width:80px;text-align:center' ) ;
	    echo "from:" . form_input( $inputfrom ) ;
	    echo "&nbsp;&nbsp;" ;
	    $inputto = array( 'id' => 'to' , 'name' => 'dateto' , 'class' => 'date-pick' , 'value' => $dateto , 'size' => '10' , 'onchange' => "this.form.submit()" , 'style' => 'width:80px;text-align:center' ) ;
	    echo "to:" . form_input( $inputto ) ;
	}
	
	echo "&nbsp;&nbsp;" ;
	$submit = array( 'name' => 'send' , 'value' => 'Submit' , 'type' => 'submit' , 'class' => 'button' ) ;
	echo form_input( $submit ) ;

	$reset = array( 'name' => 'reset' , 'value' => 'Reset' , 'type' => 'submit' , 'class' => 'button' ) ;
	echo form_input( $reset ) ;

	?>
    </div>
</fieldset>
<br>
<?php

echo form_close() ; 

?>
 



<style type="text/css" media="screen">
    .ui-icon.excel {
        width: 16px;
        height: 16px;
        background-image:url('<?= base_url() ?>images/toolbar/Excel-icon2.png');
    }
    .ui-icon.add {
        width: 16px;
        height: 16px;
        background-image:url('<?= base_url() ?>images/toolbar/add.png');
    }
    .ui-icon.del {
        width: 16px;
        height: 16px;
        background-image:url('<?= base_url() ?>images/toolbar/del.png');
    }

    .ui-icon.order {
        width: 16px;
        height: 16px;
        background-image:url('<?= base_url() ?>images/toolbar/order.png');
    }

    .ui-icon.complete {
        width: 16px;
        height: 16px;
        background-image:url('<?= base_url() ?>images/toolbar/complete.png');
    }

     .ui-icon.return {
        width: 16px;
        height: 16px;
        background-image:url('<?= base_url() ?>images/toolbar/return.png');
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
    .dialogtable{
        font-size: 1.1em;
        line-height: 29px;
    }
    .dialogntable input[type=text]{
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
         
          $("#mysearch").focus();

        var myGrid = $("<?= '#' . $nameGrid ?>");

        myGrid.jqGrid({
            url:'<?= base_url() . 'index.php' . $from . $gridSearch ?>',
            datatype: "json", 
            rowNum:50,
            rowList:[50,300,1000,100000000],
            loadComplete: function() { $("option[value=100000000]").text('ALL');},
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
            editurl: '<?= base_url() . 'index.php' . $from . $edit ?>',
            multiselect: <?=$multiselect?>, 
        });

        //Ajustamos el grid a la ventana
        resize_the_grid(myGrid);    
        // agregamos el boton de excel
       
        add_top_bar(myGrid);
 
    });
   

     

    $(window).resize(resize_the_grid);
    
    function add_top_bar(grid){
        jQuery(grid).jqGrid('navGrid','<?= '#' . $namePager ?>',{edit:true,add:false,del:false,search:false,refresh:true,cloneToTop:true}, 
        {
            //Edit Options
            recreateForm: true,
            closeAfterEdit: true,
            modal:true,
        });
   
        jQuery(grid).jqGrid('navButtonAdd', '#' + jQuery(grid)[0].id + '_toppager_left', { // "#list_toppager_left"
            caption: "Excel",
            buttonicon: 'excel',
            onClickButton: function(){
                exportExcel(grid);
            }
        });
        


        if(<?=$selectedorder?> == 0){
        
            jQuery(grid).jqGrid('navButtonAdd', '#' + jQuery(grid)[0].id + '_toppager_left', { // "#list_toppager_left"
                id: "Asin_"+ jQuery(grid)[0].id +"_top", 
                title:"Add Asin", 
                caption: "Asin",
                buttonicon: 'add',
                onClickButton: function(){
                    $('#asin').val('');
                    $('#description').val('');
                    $('.enabledbyasin').val('');
                    $('.enabledbyasin').attr('disabled','disabled');
                    $('.enabledbyfnsku').attr('disabled','disabled');
                    $( "#dialog-form" ).dialog( "open" );
                } 
            });
        

            jQuery(grid).jqGrid('navButtonAdd', '#' + jQuery(grid)[0].id + '_toppager_left', { // "#list_toppager_left"
                id: "Order_"+ jQuery(grid)[0].id +"_top", 
                title:"Add Order",
                caption: "Order",
                buttonicon: 'order',
                onClickButton: function(){
                
                    var row = jQuery(grid).jqGrid('getGridParam','selrow');
                    if( row !== null ){
                        var ret = jQuery(grid).jqGrid('getRowData',row);    
                        
                        $('#oams').val(ret.MerchantSKU);
                        $('#oaasin').val(ret.ASIN);
                        $( "#obsku" ).val(ret.MITSKU);
                        $( "#oafnsku" ).val(ret.FNSKU);
                        $( "#odesc" ).val(ret.Title);                  
                        $( "#od" ).val(currentdate());
                        $( "#order-form" ).dialog( "open" );

                    } 
                    else alert("Please Select Row");
                
                } 
            });
        
            jQuery(grid).jqGrid('navButtonAdd', '#' + jQuery(grid)[0].id + '_toppager_left', { // "#list_toppager_left"
                id: "Asind_"+ jQuery(grid)[0].id +"_top", 
                title:"Del Asin", 
                caption: "Asin",
                buttonicon: 'del',
                onClickButton: function(){
                    var asin = jQuery(grid).jqGrid('getGridParam','selrow');
                    if( asin != null ){
                        //   var myasin = jQuery(grid).jqGrid('getRowData',asin);
                        $( "#dialog-confirm" )
                        .data('myasin', jQuery(grid).jqGrid('getRowData',asin))
                        .dialog( "open" );
                    } 
                    else alert("Please Select Row");
                } 
            });

        }
        
        if(<?=$selectedorder?> == 1){
        
            jQuery(grid).jqGrid('navButtonAdd', '#' + jQuery(grid)[0].id + '_toppager_left', { // "#list_toppager_left"
                id: "cOrder_"+ jQuery(grid)[0].id +"_top", 
                title:"Close Order", 
                caption: "Order completed",
                buttonicon: 'complete',
                onClickButton: function(){

                    var order = jQuery(grid).jqGrid('getGridParam','selarrrow');
                   
                    if( order != null ){
                        $( "#dialog2-confirm" )
                        .data('myorder', order)
                        .dialog( "open" );
                    } 
                    else alert("Please Select Row");
                } 
            });

            
            jQuery(grid).jqGrid('navButtonAdd', '#' + jQuery(grid)[0].id + '_toppager_left', { // "#list_toppager_left"
                id: "dOrder_"+ jQuery(grid)[0].id +"_top", 
                title:"Del Order", 
                caption: " Del Order",
                buttonicon: 'del',
                onClickButton: function(){
                    var order = jQuery(grid).jqGrid('getGridParam','selarrrow');
                    
                    if( order != null ){
                        $( "#dialog4-confirm" )
                        .data('myorder', order)
                        .dialog( "open" );
                    } 
                    else alert("Please Select Row");
                } 
            });
            
        }

       
       if(<?=$selectedorder?> == 2){
        
            jQuery(grid).jqGrid('navButtonAdd', '#' + jQuery(grid)[0].id + '_toppager_left', { // "#list_toppager_left"
                id: "rOrder_"+ jQuery(grid)[0].id +"_top", 
                title:"Return Pendig Order", 
                caption: "Return Pending Order",
                buttonicon: 'return',
                onClickButton: function(){
                    var order = jQuery(grid).jqGrid('getGridParam','selrow');
                    
                    if( order != null ){
                        $( "#dialog3-confirm" )
                        .data('myorder', jQuery(grid).jqGrid('getRowData',order))
                        .dialog( "open" );
                    } 
                    else alert("Please Select Row");
                } 
            });    
        }


        
        var user =<?= $user ?>
     
        if (!user){  //si el usuario no es admistrador remueve los botones Asin y Order
            var topPagerDiv = $('#' + jQuery(grid)[0].id + '_toppager')[0]; 
            $("#Asin_" + jQuery(grid)[0].id + "_top", topPagerDiv).remove();        // "#edit_list_top"
            $("#Order_" + jQuery(grid)[0].id + "_top", topPagerDiv).remove();        // "#edit_list_top"
            $("#Asind_" + jQuery(grid)[0].id + "_top", topPagerDiv).remove();        // "#edit_list_top"
            $("#dOrder_" + jQuery(grid)[0].id + "_top", topPagerDiv).remove(); 
            $("#cOrder_" + jQuery(grid)[0].id + "_top", topPagerDiv).remove();
            $("#rOrder_" + jQuery(grid)[0].id + "_top", topPagerDiv).remove();
           
        }
        
        var topPagerDiv = $('#' + jQuery(grid)[0].id + '_toppager')[0];         // "#list_toppager"
       // $("#edit_" + jQuery(grid)[0].id + "_top", topPagerDiv).remove();        // "#edit_list_top"
        $("#del_" + jQuery(grid)[0].id + "_top", topPagerDiv).remove();         // "#del_list_top"
        $("#search_" + jQuery(grid)[0].id + "_top", topPagerDiv).remove();      // "#search_list_top"
        $("#refresh_" + jQuery(grid)[0].id + "_top", topPagerDiv).remove();     // "#refresh_list_top
        $("#view_" + jQuery(grid)[0].id + "_top", topPagerDiv).remove();         // "#add_list_top"
        

    }
  
    function currentdate(){   
        var d=new Date();
        var dat=d.getDate();
        var mon=d.getMonth()+1;
        var year=d.getFullYear();
        var hours = d.getHours()
        var minutes = d.getMinutes()
        var seconds = d.getSeconds()
        var todayDate = mon+"/"+dat+"/"+year+" "+hours+":"+minutes+":"+seconds;
        return todayDate;
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

    function printFnSku(cellvalue, options,rowData) {    
        var fnsku = rowData[5];

        return "<A HREF=\"javascript:popUp('showFnSku?fn="+fnsku+"')\"><span class=\"ui-icon ui-icon-print\" style=\"display:inline-block;\"></span></A>";
    }
    


    function popUp(URL) {
        day = new Date();
        id = day.getTime();
        eval("page" + id + " = window.open(URL, '" + id + "', 'toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=0,width=400,height=300,left = 640,top = 325');");
    }
    
    
    
    
    $(function() {
        var channel = $( "#channel" ),
        asin = $( "#asin" ),
        partnumber = $( "#partnumber" ),
        fnsku = $( "#fnsku" ),
        description = $( "#description" ),
        merchansku = $( "#merchansku" ),
        sku = $( "#skusend" ),

        oams = ( "#oams" ),
        orderid = $( "#orderid" ),
        oasin = $( "#oaasin" ),
        osku = $( "#obsku" ),
        ofnsku = $( "#oafnsku" ),
        odescription = $( "#odesc" ),
        orderdate = $( "#od" ),
        orderqty = $( "#oqty" ),
        ordernotes = $( "#on" ),
        addtoamazon = $( "#addtoamazon" ),
            
        orderFields = $( [] ).add( oams ).add( orderid ).add( oasin ).add( osku ).add( ofnsku ).add( odescription ).add( orderdate ).add( orderqty ).add( ordernotes ).add( addtoamazon ),

            
        asinFields = $( [] ).add( channel ).add( asin ).add( partnumber ).add( fnsku ).add( description ).add( merchansku ).add( sku ),
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
            height: 350,
            width: 500,
            modal: true,
            buttons: {
                "Create an Asin": function() {
                    var bValid = true;
                    $.trim(fnsku);
                    asinFields.removeClass( "ui-state-error" );
                   // bValid = bValid && checkLength( channel, "channel", 4, 200 );
                    bValid = bValid && checkLength( asin, "asin", 10, 15 );
                    bValid = bValid && checkLength( partnumber, "partnumber", 1, 30 );
                    bValid = bValid && checkLength( fnsku, "fnsku", 10, 10 );
                    bValid = bValid && checkLength( description, "description", 5, 250 );
                    bValid = bValid && checkLength( merchansku, "merchansku", 10, 15 );
                    bValid = bValid && checkLength( sku, "sku", 6, 10 );
                 
                    if ( bValid ) {
                        
                        var request = $.ajax({
                            url:"<?= base_url() ?>index.php/tools/amazonfba/saveAsin",
                            type: "POST",
                            data:asinFields
                        });
                                    
                        request.done(function() {
                            alert('Asin Added')
                            jQuery("<?= '#' . $nameGrid ?>").trigger("reloadGrid");
                        });
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
//        $('#asin').change(function(){
//               var ms = $.ajax({
//                            url:"<?= base_url() ?>index.php/tools/amazonfba/getMerchantSKU",
//                            type: "POST",
//                            data:asin
//                        });
//                                    
//                        request.done(function(ret) {
//                            alert('Asin Added')
//                          $('#merchansku').val(ret.merchantsku);
//                        });
//        
//            $('.enabledbyasin').removeAttr('disabled');
//         //   $('#merchansku').val($('#asin').val());
//        });
                
        $('#asin').keyup(function() { // Se activa inmediatemente cuando elusuario presiona una tecla dentro del input
            if( $('#asin').val() != $('#asin').data('val') ){ // checa si el valor cambio
                $('#asin').data('val',  $('#asin').val() ); // guarda el valor 
                $(this).change(); // simila el evento "change"
            } 
        });    
                
        $('#asin').focusout(function(){
            var Asinval= $('#asin').val();
            var Chann = $("#channel").val();
                    
            var  request = $.ajax({
                url:"<?= base_url() ?>index.php/tools/amazonfba/validateAsin",
                type: "POST",
                data:{av: Asinval,
                      cs: Chann}
            });

            var ret1='';
            request.done(function(ret1) {
                ret1=ret1.replace(/\n/gi,"");
                       
                if(ret1 != 0){
                    alert('Duplicated Asin')
                    $('#asin').val('');
                    $('#merchansku').val('');
                }
            });           
        });
                 
                
        $('#fnsku').click(function() {
            $('.enabledbyfnsku').removeAttr('disabled');  
        });
                

        $("#fnsku").focusout(function() {
            var partnumber = $("#partnumber").val(); 
            var stAsin = $('#asin').val();
            var pureasin = stAsin.substr(stAsin.length - 10);
                    
            var  request = $.ajax({
                url:"<?= base_url() ?>index.php/tools/amazonfba/getDescription",
                type: "POST",
                data:{pa: pureasin}
            });
                    
            request.done(function(ret) {
                $( "#description" ).val( ret );
            });
                    

            var  request = $.ajax({
                url:"<?= base_url() ?>index.php/tools/amazonfba/getSKU",
                type: "POST",
                data:{pn: partnumber}
            });
                    
            request.done(function(ret) {
                $("#sku").html( ret );
                $( "#skusend" ).removeAttr("disabled");
                $('input#skusend').val($("#sku option:selected").val());
                  
            });
        }); 
        
        $("#sku").change(function(){
            var str = "";
            $("#sku option:selected").each(function () {
                str += $(this).text();
            });
            $('input#skusend').val(str)
        });
        
        
        $("#channelselect").change(function(){
           var str = $("#channelselect option:selected").val();
           $('input#channelsend').val(str)           
        });
        
           
        $( "#order-form" ).dialog({
            autoOpen: false,
            height: 400,
            width: 500,
            modal: true,
            buttons: {
                "Create Order": function() {
                    var bValid = true;
                    orderFields.removeClass( "ui-state-error" );
 
                    bValid = bValid && checkRegexp( orderqty, /^([0-9])+$/, "Only allow : 0-9" );
                    
                    if ( bValid ) {
                        
                        var request = $.ajax({
                            url:"<?= base_url() ?>index.php/tools/amazonfba/saveOrder",
                            type: "POST",
                            data:orderFields
                        });
                                    
                        request.done(function() {
                            alert('Order Added')
                            jQuery("<?= '#' . $nameGrid ?>").trigger("reloadGrid");
                        });
                        $( this ).dialog( "close" );  
                    }
                       
                },
                Cancel: function() {
                    $( this ).dialog( "close" );
                }
            },
            close: function() {
                orderFields.val( "" ).removeClass( "ui-state-error" );
            }
        });


    });
    
   
    $(function() {
        $( "#dialog-confirm" ).dialog({
            autoOpen: false,
            resizable: false,
            height:160,
            modal: true,
            buttons: {
                "Delete Asin": function() {
                    var myasin = $(this).data('myasin'); //recuperams el objeto con los datos del renglon.
                    var request = $.ajax({
                        url:"<?= base_url() ?>index.php/tools/amazonfba/deleteAsin",
                        type: "POST",
                        data:{da: myasin.ASIN}
                    });
                                    
                    request.done(function() {
                        alert('Asin deleted')
                        jQuery("<?= '#' . $nameGrid ?>").trigger("reloadGrid");
                    });
                            
                    $( this ).dialog( "close" );
                },
                Cancel: function() {
                    $( this ).dialog( "close" );
                }
            }
        });
    });
   

    $(function() {
        $( "#dialog1-confirm" ).dialog({
            autoOpen: false,
            resizable: false,
            height:160,
            modal: true,
            buttons: {
                "Delete Order": function() {
                    var myorder = $(this).data('myorder'); //recuperams el objeto con los datos del renglon.
                    var request = $.ajax({
                        url:"<?= base_url() ?>index.php/tools/amazonfba/deleteOrder",
                        type: "POST",
                        data:{order: myorder}
                    });
                                    
                    request.done(function() {
                        alert('Order deleted')
                        jQuery("<?= '#' . $nameGrid ?>").trigger("reloadGrid");
                    });
                            
                    $( this ).dialog( "close" );
                },
                Cancel: function() {
                    $( this ).dialog( "close" );
                }
            }
        });
    });
 
    $(function() {
        $( "#dialog2-confirm" ).dialog({
            autoOpen: false,
            resizable: false,
            height:160,
            modal: true,
            buttons: {
                "Complete Order": function() {
                    var myorder = $(this).data('myorder'); //recuperams el objeto con los datos del renglon.

                    var request = $.ajax({
                       url:"<?= base_url() ?>index.php/tools/amazonfba/completeOrder",
                        type: "POST",
                        data:{orders: myorder}
                    });
                                    
                    request.done(function() {
                        jQuery("<?= '#' . $nameGrid ?>").trigger("reloadGrid");
                    });
                            
                    $( this ).dialog( "close" );
                },
                Cancel: function() {
                    $( this ).dialog( "close" );
                }
            }
        });
    });

    $(function() {
        $( "#dialog3-confirm" ).dialog({
            autoOpen: false,
            resizable: false,
            height:160,
            modal: true,
            buttons: {
                "Return to Pending Order": function() {
                    var myorder = $(this).data('myorder'); //recuperams el objeto con los datos del renglon.

                    var request = $.ajax({
                        url:"<?= base_url() ?>index.php/tools/amazonfba/returnOrder",
                        type: "POST",
                        data:{order: myorder.OrderID}
                    });
                                    
                    request.done(function() {
                        jQuery("<?= '#' . $nameGrid ?>").trigger("reloadGrid");
                    });
                            
                    $( this ).dialog( "close" );
                },
                Cancel: function() {
                    $( this ).dialog( "close" );
                }
            }
        });
    });

     $(function() {
        $( "#dialog4-confirm" ).dialog({
            autoOpen: false,
            resizable: false,
            height:160,
            modal: true,
            buttons: {
                "Delete Order": function() {
                    var myorder = $(this).data('myorder'); //recuperams el objeto con los datos del renglon.

                    var request = $.ajax({
                        url:"<?= base_url() ?>index.php/tools/amazonfba/deletePendingOrder",
                        type: "POST",
                        data:{orders: myorder}
                    });
                                    
                    request.done(function() {
                        alert('Orders deleted')
                        jQuery("<?= '#' . $nameGrid ?>").trigger("reloadGrid");
                    });
                            
                    $( this ).dialog( "close" );
                },
                Cancel: function() {
                    $( this ).dialog( "close" );
                }
            }
        });
    });


</script>


<div id="dialog-form" title="Create Assin">
	    <?php

	    $formopen1 = array( 'id' => "fba_adasin" , 'class' => 'myform' ) ;
	    echo form_open( '' , $formopen1 ) ;

	    ?>
    <fieldset class="bluegradiant">
        <div > 
	    <?php

        $options = array(
                  'small'  => 'Small Shirt',
                  'med'    => 'Medium Shirt',
                  'large'   => 'Large Shirt',
                  'xlarge' => 'Extra Large Shirt',
                );

	    $inputasin = array( 'name' => 'Asin' , 'id' => 'asin' , 'value' => '' , 'maxlength' => '250' , 'size' => '40' ) ;
	    $inputPN = array( 'name' => 'PartNumber' , 'id' => 'partnumber' , 'class' => 'enabledbyasin' , 'maxlength' => '250' , 'size' => '40' ) ;
	    $inputFNKU = array( 'name' => 'FNSKU' , 'id' => 'fnsku' , 'class' => 'enabledbyasin' , 'maxlength' => '250' , 'size' => '40' ) ;
	    $inputAD = array( 'name' => 'Description' , 'id' => 'description' , 'value' => '' , 'class' => 'enabledbyfnsku' , 'maxlength' => '250' , 'size' => '40' ) ;
	    $inputMSKU = array( 'name' => 'MerchanSKU' , 'id' => 'merchansku' , 'class' => 'enabledbyasin' , 'maxlength' => '250' , 'size' => '40' ) ;
	    $submit1 = array( 'name' => 'sendasin' , 'value' => 'Submit' , 'type' => 'submit' , 'class' => 'button' , 'style' => 'visibility:hidden' ) ;
	    echo '<table class="dialogtable" style="text-align:left;width:100%">' ;
        echo '<tr><td>Channel:</td><td>' . form_dropdown('channel', $channels,'Dart','id="channel"'). '</td></tr>' ;
	    echo '<tr><td>Amazon Asin:</td><td>' . form_input( $inputasin ) . '</td></tr>' ;
	    echo '<tr><td>PartNumber:</td><td>' . form_input( $inputPN ) . '</td></tr>' ;
	    echo '<tr><td>Amazon FNSKU:</td><td>' . form_input( $inputFNKU ) . '</td></tr>' ;
	    echo '<tr><td>Amazon Description:</td><td>' . form_input( $inputAD ) . '</td></tr>' ;
	    echo '<tr><td>Merchant SKU:</td><td>' . form_input( $inputMSKU ) . '</td></tr>' ;
	    echo '<tr><td>Build SKU:</td><td><select name="SKU" id="sku" style="width: 200px;" ></select><input name="skusend" id="skusend" value="" class = "enabledbyasin" style="margin-left: -199px; margin-top:-5; width: 174px; height: 12px; border: 1px;" /></td></tr>' ;
	    echo '</table>' ;
	    echo form_input( $submit1 ) ;
            
	    ?>
        </div>
    </fieldset>
    <br>
<?php

echo form_close() ;

?>
</div>

<div id="order-form" title="Create Order">
	    <?php

	    $formopen2 = array( 'id' => "order" , 'class' => 'myform' ) ;
	    echo form_open( '' , $formopen2 ) ;

	    ?>
    <fieldset class="bluegradiant">
        <div > 
	    <?php

	//    $inputOID = array('name' => 'OrderID', 'id' => 'orderid', 'value' => '', 'class' => 'itemdisable', 'maxlength' => '250', 'size' => '40');
        $inputOAMS    = array( 'name' => 'OAMS' , 'id' => 'oams' , 'class' => 'itemdisable' , 'maxlength' => '250' , 'size' => '40' , 'readonly' => 'true' ) ;
	    $inputOAASIN  = array( 'name' => 'OAASIN' , 'id' => 'oaasin' , 'class' => 'itemdisable' , 'maxlength' => '250' , 'size' => '40' , 'readonly' => 'true' ) ;
	    $inputOBSKU = array( 'name' => 'OBSKU' , 'id' => 'obsku' , 'class' => 'itemdisable' , 'maxlength' => '250' , 'size' => '40' , 'readonly' => 'true' ) ;
	    $inputOAFNKU = array( 'name' => 'OAFNSKU' , 'id' => 'oafnsku' , 'value' => '' , 'class' => 'itemdisable' , 'maxlength' => '250' , 'size' => '40' , 'readonly' => 'true' ) ;
	    $inputOADesc = array( 'name' => 'ODesc' , 'id' => 'odesc' , 'class' => 'itemdisable' , 'maxlength' => '250' , 'size' => '40' , 'readonly' => 'true' ) ;
	    $inputOD = array( 'name' => 'OD' , 'id' => 'od' , 'class' => 'itemdisable' , 'maxlength' => '250' , 'size' => '40' , 'readonly' => 'true' ) ;
	    $inputOQTY = array( 'name' => 'OQTY' , 'id' => 'oqty' , 'value' => '' , 'maxlength' => '250' , 'size' => '40' ) ;
	    $inputON = array( 'name' => 'ON' , 'id' => 'on' , 'rows' => '4' , 'cols' => '38' ) ;
	    $submit2 = array( 'name' => 'sendorder' , 'value' => 'Submit' , 'type' => 'submit' , 'class' => 'button' , 'style' => 'visibility:hidden' ) ;
	  
            
            
        echo '<table class="dialogtable" style="text-align:left;width:100%">' ;
	 //   echo '<tr><td>OrderID:</td><td>' . form_input($inputOID) . '</td></tr>';
        echo '<tr><td>Amazon MerchantSKU:</td><td>' . form_input( $inputOAMS ) . '</td></tr>' ;
	    echo '<tr><td>Amazon Asin:</td><td>' . form_input( $inputOAASIN ) . '</td></tr>' ;
	    echo '<tr><td>Build SKU:</td><td>' . form_input( $inputOBSKU ) . '</td></tr>' ;
	    echo '<tr><td>Amazon FNSKU:</td><td>' . form_input( $inputOAFNKU ) . '</td></tr>' ;
	    echo '<tr><td>Amazon Description:</td><td>' . form_input( $inputOADesc ) . '</td></tr>' ;
	    echo '<tr><td>Order Date:</td><td>' . form_input( $inputOD ) . '</td></tr>' ;
	    echo '<tr><td>Order Quantity:</td><td>' . form_input( $inputOQTY ) . '</td></tr>' ;
	    echo '<tr><td>Order Notes:</td><td>' . form_textarea( $inputON ) . '</td></tr>' ;
	    echo '<tr><td>Add to Amazon:</td><td><select name="Completed" id="addtoamazon" ><option value="No">NO</option><option value="Yes">YES</option></select></td></tr>' ;
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

<div id="dialog-confirm" title="Delete Asin?">
    <p><span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 20px 0;"></span>This asin will be permanently deleted and cannot be recovered. Are you sure?</p>
</div>

<div id="dialog1-confirm" title="Delete Order?">
    <p><span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 20px 0;"></span>This order will be permanently deleted and cannot be recovered. Are you sure?</p>
</div>

<div id="dialog2-confirm" title="Order Complete?">
    <p><span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 20px 0;"></span>This order will be marked as completed. Are you sure?</p>
</div>
<div id="dialog3-confirm" title="Return to Pending Order?">
    <p><span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 20px 0;"></span>This order will be marked as Pending. Are you sure?</p>
</div>

<div id="dialog4-confirm" title="Delete Pending Order?">
    <p><span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 20px 0;"></span>This order will be permanentry delete and cannot be recovered. Are you sure?</p>
</div>
