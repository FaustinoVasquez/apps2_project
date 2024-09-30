<style type = "text/css" media = "screen">
    .ui-icon.myicon {
        width: 16px;
        height: 16px;
        background-image:url('<?= base_url() ?>images/toolbar/Excel-icon2.png')
    }
</style>


<!-- Defunimos los atributos para los elementos del formulario -->
<?php  

$formopen = array('id' => "form", 'class'=>'myform');
$selectCarrier = 'class="form-select" id="carrier"';
$carrierOptions = array(
                  'USPS'  => 'USPS',
                  'Other'    => 'Other',
                );

$inputsearch = array('name' => 'search','id'=>'search','value'=>'','autocomplete'=>'on','maxlength'=>'250','size'=>'20');
$submit = array('name' => 'send', 'id'=>'btnSubmit', 'value' => 'Submit', 'type' => 'submit', 'class' => 'button');
$reset = array('name' => 'reset', 'value' => 'Reset', 'type' => 'submit', 'class' => 'button');


?>


<!-- Creamos el formulario -->


<?php echo form_open(base_url() . 'index.php' . $from, $formopen); ?>
<fieldset class="bluegradiant">
    <div class="logo"><img longdesc="MI Technologies" title="Mi Tech" alt="43" src="<?= base_url() ?>/images/header/mitechnologies.png"></div>
    <div class="form-data"> 
        <?php
        echo "<label>Search:</label>" . form_input($inputsearch) .'&nbsp;';
        echo form_dropdown('Carrier', $carrierOptions, 'USPS', $selectCarrier).'&nbsp;';
        echo form_input($submit);
        echo form_input($reset).'<br>';
        echo '<label>From:</label> <input id="from"  type="text"  size="13">&nbsp;';
        echo '<label> To:</label> <input id ="to"  type="text" size="13">';
        ?>
    </div>
</fieldset>
<br>
<?php echo form_close(); ?>


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
     
     $(function(){

        $('#from').datetimepicker({value:'<?= $dateFrom?>',step:60});
        $('#to').datetimepicker({value:'<?= $dateTo?>',step:60});
        
         var myGrid= $("#list"),
            pagerSelector = "#pager", 
            myAddButton = function(options) {
                myGrid.jqGrid('navButtonAdd',pagerSelector,options);
                myGrid.jqGrid('navButtonAdd','#'+myGrid[0].id+"_toppager",options);
            };
      
        myGrid.jqGrid({
            url:'gridData',
            datatype: "json",
            postData: {
            ds:      function() { return jQuery("#search").val(); },
            from:    function() { return jQuery("#from").val(); },
            to:      function() { return jQuery("#to").val(); },
            carrier: function() { return jQuery("#carrier option:selected").val(); },
            },
            colNames:<?= $headers ?>,
            colModel:<?= $body ?>,
            height:600,
            autowidth: true,
            pager: '#pager',
            rowNum: 1000,
            rowList: [1000,1500,2000],
            rownumbers: true,
            sortname: 'COD.SKU',
            sortorder: 'asc',
            viewrecords: true,
            caption: "<?= $caption ?>",
            toppager:true,
        });
        jQuery("#list").jqGrid('navGrid','#pager',{cloneToTop:true,edit:false,add:false,del:false,search:false,refresh:true});


        myGrid.jqGrid('navButtonAdd', '#' + myGrid[0].id + '_toppager_left', { // "#list_toppager_left"
            caption: "Excel",
            buttonicon: 'myicon',
            onClickButton: function(){
                exportExcel();
            }
        });


       resize_the_grid();

    var myReload = function() {
        myGrid.trigger('reloadGrid');
    }; 

   // Recargar el grid en el evento submit del formulario
    $( 'form' ).on( 'submit', function( e ){
         e.preventDefault();
          myReload();
     });

      //Recargar el grid en el evento onChange del select search del formulario
    $( "#from" ).on('change',function(){
       myReload();
    });


     //Recargar el grid en el evento onChange del select search del formulario
    $( "#to" ).on('change',function(){
       myReload();
    });

    //Recargar el grid en el evento onChange del select search del formulario
    $( "#search" ).on('change',function(){
       myReload();
    });

    //Recargar el grid en el evento onChange del select categories del formulario
    $( "#carrier" ).on('change',function(){
        myReload();
    });

    });
    
    $(window).resize(resize_the_grid);

    

     function exportExcel()
        {
            var mya=new Array();
            mya=$("#list").getDataIDs();  // Get All IDs
            var data=$("#list").getRowData(mya[0]);     // Get First row to get the labels
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
                data=$("#list").getRowData(mya[i]); // get each row
                for(j=0;j<colNames.length;j++)
                {
                    html=html+data[colNames[j]]+"\t"; // output each Row as tab delimited
                }
                html=html+"\n";  // output each row with end of line

            }

            html=html+"\n";  // end of line at the end
            document.forms[1].csvBuffer.value=html;
            document.forms[1].method='POST';
            document.forms[1].action='<?= base_url() . 'index.php/' . $from . '/csvExport/palletContent' ?>';  // send it to server which will open this contents in excel file
            document.forms[1].target='_blank';
            document.forms[1].submit();
        }

</script>




