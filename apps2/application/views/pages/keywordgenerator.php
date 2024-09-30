<style type="text/css" media="screen">
    .ui-icon.excel {
        width: 16px;
        height: 16px;
        background-image:url('<?= base_url() ?>images/toolbar/Excel-icon2.png');
    }

    .ui-autocomplete {
        max-height: 200px;
        overflow-y: auto;
        /* prevent horizontal scrollbar */
        overflow-x: hidden;
        }
        /* IE 6 doesn't support max-height
        * we use height instead, but this forces the menu to always be this tall
        */
        * html .ui-autocomplete {
        height: 100px;
        }
</style>

<?php
    $formopen = array('id'  => 'target', 'id' => "omc_form", 'class' => 'myform');
    $inputSKU = array('id'  => 'sku', 'name' => 'sku', 'value' => '', 'autocomplete' => 'on', 'maxlength' => '250', 'size' => '20');
    $inputPN =  array('id'  => 'pn','name' => 'pn', 'value' => '', 'autocomplete' => 'on', 'maxlength' => '250', 'size' => '20');
    $submit =   array('name' => 'send', 'value' => 'Submit', 'type' => 'submit', 'class' => 'button');
    $reset =    array('name' => 'reset', 'value' => 'Reset', 'type' => 'submit', 'class' => 'button');

    echo form_open(base_url() . 'index.php' . $from, $formopen);
?>
    <fieldset class="bluegradiant">
        <div class="logo"><img longdesc="MI Technologies" title="Mi Tech" alt="43" src="<?= base_url() ?>/images/header/mitechnologies.png"></div>
        <div class="form-data"> 
            <?php
                echo "SKU:" . form_input($inputSKU);
                echo "&nbsp;&nbsp;";
                echo "Part Number:" . form_input($inputPN);
                echo "&nbsp;&nbsp;";
                echo form_input($submit);
                echo form_input($reset);
            ?>
        </div>
    </fieldset>
<br>
<?php echo form_close(); ?> 
	     
    
<div class="clear"></div>
    
<section id="grid_container">
    <table id="<?= $nameGrid ?>"></table>
    <div id="<?= $namePager ?>"></div> 
</section>

<div id="keyWords" style="margin-left:5%; width: 90%; height: 100px; overflow-y: scroll; border:1px solid #ccc; background-color:white; margin-top:20px; font-size:12px"></div>
    
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
        var uniq =[];
	
        myGrid.jqGrid({
            url:'Data',
            postData: {
                sku: function() { return jQuery("#sku").val(); },
                pn:   function() { return jQuery("#pn").val(); },
            },
            datatype: "json",
            colNames:<?= $colNames ?>,
            colModel:<?= $colModel ?>,
            autowidth: true,
            pager: jQuery('<?= '#' . $namePager ?>'),
            rowNum: 50,
            sortname: 'SKU',
            rowList:[50,300,100000000],
            loadComplete: function() { $("option[value=100000000]").text('ALL');},
            rownumbers: true,
            viewrecords: true,
            caption: "<?= $caption ?>",
            height: 500, 
            toppager:true,
            afterInsertRow: function(rowid, aData){ 

                var repeat = 0;

                for (var i = 0; i < uniq.length; i++) {
                   if (uniq[i] == aData.Keywords){
                      repeat = 1;
                   }
                }

                if (!repeat){
                    uniq.push(aData.Keywords);
                    $("#keyWords").append(aData.Keywords, ",");
               }
                 
        },
        });

	    resize_the_grid();
        add_top_bar(myGrid);

        var myReload = function() {
            myGrid.trigger('reloadGrid');
        }; 

       // Recargar el grid en el evento submit del formulario
        $( 'form' ).on( 'submit', function( e ){
             e.preventDefault();
              myReload();
         });

        //Recargar el grid en el evento onChange del select search del formulario
        $( "#sku" ).on('change',function(){
           myReload();
        });

        $( "#sku" ).autocomplete({
            source: "fillSKU"
        });

          //Recargar el grid en el evento onChange del select search del formulario
        $( "#pn" ).on('change',function(){
           myReload();
        });

        // $( "#pn" ).autocomplete({
        //     source: "fillPN"
        // });

        $( "#pn" ).autocomplete({ 
            source: function (request, response) {
            request['sku'] = $("#sku").val()

            if (request['sku'] !=''){
                $.ajax({
                type: "POST",
                url:"fillPN",
                data: request,
                success: response,
                dataType: 'json'
                });  
            }else{
                alert('Plase fill SKU field! ');
                $("#pn").val("");
                $("#sku").focus();
            }
          }
        }, {minLength: 1 });


    });
    
    $(window).resize(resize_the_grid);
    
    
    function add_top_bar(grid){
        jQuery(grid).jqGrid('navGrid','#projectordataPager',{cloneToTop:true, edit: false, add: false, del: false, search: false, refresh:false});

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
    
</script>


