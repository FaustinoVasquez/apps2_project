
<?php
$formopen = array('id' => 'target', 'class'=>'myform');
echo form_open(base_url() . 'index.php/' . $from, $formopen);


?>
<fieldset class="bluegradiant">
    <div class="logo"><img longdesc="MI Technologies" title="Mi Tech" alt="43" src="<?= base_url() ?>/images/header/mitechnologies.png">
    </div>
    <div class="form-data"> 
        <?php

        $inputsearch = array('id'=>'search', 'name' => 'search', 'value' => '', 'autocomplete' => 'on', 'maxlength' => '250', 'size' => '20');
        $inputfrom = array('id' => 'from', 'name' => 'datefrom', 'class' => 'date-pick', 'value' => $datefrom, 'size' => '10', 'style' => 'width:80px;text-align:center');
        $inputto = array('id' => 'to', 'name' => 'dateto', 'class' => 'date-pick', 'value' => $dateto, 'size' => '10', 'style' => 'width:80px;text-align:center');
        $submit = array('name' => 'send', 'value' => 'Submit', 'type' => 'submit', 'class' => 'button');
        $reset = array('name' => 'reset', 'value' => 'Reset', 'type' => 'submit', 'class' => 'button');

        
        echo "Search:" . form_input($inputsearch);
        echo '&nbsp;&nbsp;';
        echo '<br>';
        echo "from:" . form_input($inputfrom);
        echo "&nbsp;&nbsp;";
        echo "to:" . form_input($inputto);
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


<style type="text/css" media="screen">
    .ui-icon.excel {
        width: 16px;
        height: 16px;
        background-image:url('<?= base_url() ?>images/toolbar/Excel-icon2.png');
    }
</style>


<div class="clear"></div>

<section id="grid_container">
    <table id="grid"></table>
    <div id='pager'></div> 
</section>

 <form method="post" action="<?= base_url() . 'index.php/' . $from . '/csvExport' ?>">
    <input type="hidden" name="csvBuffer" id="csvBuffer" value="" />
</form>  



<script type="text/javascript">
    

    function resize_the_grid()
    {
        $("#grid").fluidGrid({base:'#grid_wrapper', offset:-20});
    }
    

    $(function(){

        var myGrid = $('#grid');

       myGrid.jqGrid({
            url:'getData',
             postData: {
                search: function() { return jQuery("#search").val(); },
                from:   function() { return jQuery("#from").val(); },
                to:   function() { return jQuery("#to").val(); },
            },
            datatype: "json", 
            rowNum:50,
            rowList:[50,300,1000],
            colNames:<?= $colNames ?>,
            colModel:<?= $colModel ?>,
            pager:'#pager',
            viewrecords: true,
            rownumbers: true,
            sortname: 'Product_Catalog_ID',
            sortorder: 'asc',
            caption: '<?= $caption ?>',
            height: 600, 
            toppager:true,
            subGrid : true, 
            subGridOptions: { "plusicon" : "ui-icon-triangle-1-e", "minusicon" : "ui-icon-triangle-1-s", "openicon" : "ui-icon-arrowreturn-1-e" },
            subGridRowExpanded: function(subgrid_id, row_id) {    
               var subgrid_table_id, pager_id; 
               subgrid_table_id = subgrid_id+"_t"; 
               pager_id = "p_"+subgrid_table_id;
               $("#"+subgrid_id).html("<div style='background-color:#363737;width:100%;'>\n\
                                          <center>\n\
                                            <br>\n\
                                              <table id='"+subgrid_table_id+"' class='scroll'></table>\n\
                                            <br>\n\
                                         </center>\n\
                                       </div>\n\
                                     <div id='"+pager_id+"' class='scroll'></div>"
                                     ); 
                   jQuery("#"+subgrid_table_id).jqGrid({ 
                       url:'getDataSubgrid',  
                        postData: {
                            from:   function() { return jQuery("#from").val(); },
                             to:   function() { return jQuery("#to").val(); },
                       },
                       datatype: "json",  
                       colNames: <?= $subGridColNames?>, 
                       colModel: <?= $subGridColModel?>, 
                       width:900,
                       rowNum:20, 
                       pager: pager_id, 
                       caption: "Detailed Bin Information ",
                       sortname: 'Stamp', 
                       sortorder: "asc", 
                       height: '100%'
                       
                   });
              jQuery("#"+subgrid_table_id).jqGrid('navGrid',"#"+pager_id,{edit:false,add:true,del:false})
            } 
        });
        //Ajustamos el grid a la ventana
        resize_the_grid(myGrid);    
       // agregamos el boton de excel
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
        $( "#search" ).on('change',function(){
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
    });
     
   

   
   $(window).resize(resize_the_grid);
    
     function add_top_bar(grid){
     jQuery(grid).jqGrid('navGrid','#pager',{cloneToTop:true,add:false,edit:false,del:false});
        jQuery(grid).jqGrid('navButtonAdd', '#' + jQuery(grid)[0].id + '_toppager_left', { // "#list_toppager_left"
            caption: "Excel",
            buttonicon: 'excel',
            onClickButton: function(){
                exportExcel(grid);
            }
        });
        
        var topPagerDiv = $('#' + jQuery(grid)[0].id + '_toppager')[0];         // "#list_toppager"
        $("#edit_" + jQuery(grid)[0].id + "_top", topPagerDiv).remove();        // "#edit_list_top"
        $("#del_" + jQuery(grid)[0].id + "_top", topPagerDiv).remove();         // "#del_list_top"
        $("#search_" + jQuery(grid)[0].id + "_top", topPagerDiv).remove();      // "#search_list_top"
        $("#refresh_" + jQuery(grid)[0].id + "_top", topPagerDiv).remove();     // "#refresh_list_top"
        $("#add_" + jQuery(grid)[0].id + "_top", topPagerDiv).remove();         // "#add_list_top"
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
        document.forms[1].action='csvExport'; //TRabajar en esto;;;
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
    
</script>
