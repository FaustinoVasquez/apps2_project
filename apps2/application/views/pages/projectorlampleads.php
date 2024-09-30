
<?php
$formopen = array('id' => 'target', 'id' => "omc_form", 'class'=>'myform');
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

        $activatedropdown = 'class="form-select" onChange="this.form.submit()"';
        echo form_dropdown('selectedcart', $cartOptions, isset($lineselect->selectedcart) ? $lineselect->selectedcart : $this->input->post('selectedcart'), $activatedropdown);
       
        echo form_dropdown('qtyOrdered', $qtyorderedoptions, isset($lineselect->qtyOrdered) ? $lineselect->qtyOrdered : $this->input->post('qtyOrdered'),$activatedropdown );
        
        echo form_dropdown('status', $statusOptions, isset($lineselect->status) ? $lineselect->status : $this->input->post('status'), $activatedropdown);

        
        echo '<br>';
       
        $inputfrom = array('id' => 'from', 'name' => 'datefrom', 'class' => 'date-pick', 'value' => $datefrom, 'size' => '10', 'onchange' => "this.form.submit()", 'style' => 'width:80px;text-align:center');
        echo "from:" . form_input($inputfrom);
        echo "&nbsp;&nbsp;";
        $inputto = array('id' => 'to', 'name' => 'dateto', 'class' => 'date-pick', 'value' => $dateto, 'size' => '10', 'onchange' => "this.form.submit()", 'style' => 'width:80px;text-align:center');
        echo "to:" . form_input($inputto);
        echo "&nbsp;&nbsp;";
      //  $qtyOrderedCheckbox = array('name' => 'qtyOrdered', 'id' => 'qtyOrdered', 'value' => 'TRUE', 'checked' => $qtyOrdered, 'onClick' => "this.form.submit()", 'class' => 'checkbox');
     //   echo form_checkbox($qtyOrderedCheckbox);
     //   echo' Quantity ordered&nbsp;&nbsp;</label>';
        
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
    .ui-icon.myicon {
        width: 16px;
        height: 16px;
        background-image:url('<?= base_url() ?>images/toolbar/Excel-icon2.png')
    }
</style>


<div class="clear"></div>

<section id="grid_container">
    <table id="<?= $nameGrid ?>"></table>
    <div id="<?= $namePager?>"></div> 
</section>

 <form method="post" action="<?= base_url() . 'index.php' . $from . 'csvExport' ?>">
    <input type="hidden" name="csvBuffer" id="csvBuffer" value="" />
</form>  



<script type="text/javascript">
    
    

    function resize_the_grid()
    {
        $("<?='#'.$nameGrid ?>").fluidGrid({base:'#grid_wrapper', offset:-20});
    }
    

    $(document).ready(function(){

        var myGrid = $("<?='#'.$nameGrid ?>");

        myGrid.jqGrid({
            url:'<?= base_url() . 'index.php' . $from . $gridSearch ?>',
            datatype: "json", 
            rowNum:50,
            rowList:[50,300,1000],
            colNames:<?= $colNames ?>,
            colModel:<?= $colModel ?>,
            pager: jQuery('<?='#'.$namePager ?>'),
            viewrecords: true,
            rownumbers: true, 
            sortname: 'O.OrderNumber',
            sortorder: '<?= $sort ?>',
            caption: '<?= $caption ?>',
            height: 600, 
            toppager:true,
           // jsonReader: { repeatitems : false }, 
            shrinkToFit: false,
            editurl:'<?= base_url() . 'index.php' . $from . 'saveData' ?>',
            
        });
   
       jQuery("<?='#'.$nameGrid ?>").jqGrid('setFrozenColumns');
       
        //Ajustamos el grid a la ventana
        resize_the_grid(myGrid);    
       // agregamos el boton de excel
       
        add_top_bar(myGrid);
 
    });
     
 

   
   
   $(window).resize(resize_the_grid);
    
     function add_top_bar(grid){
        jQuery(grid).jqGrid('navGrid','<?= '#' . $namePager ?>',{cloneToTop:true,edit:true,add:false,del:false,search:false},{
             // edit option
              beforeShowForm: function(form) { $('#tr_Adjustment_Id', form).hide(); },
              reloadAfterSubmit:false,
              closeAfterEdit: true,
              closeOnEscape:true,
              recreateForm: true,
              width:500
          });
        jQuery(grid).jqGrid('navButtonAdd', '#' + jQuery(grid)[0].id + '_toppager_left', { // "#list_toppager_left"
            caption: "Excel",
            buttonicon: 'excel',
            onClickButton: function(){
                exportExcel(grid);
            }
        });
        
        var topPagerDiv = $('#' + jQuery(grid)[0].id + '_toppager')[0];         // "#list_toppager"
       
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
        document.forms[1].action='<?= base_url() . 'index.php' . $from . 'csvExport/'.$export?>'; //TRabajar en esto;;;
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
    

 // esta funcion genera un link 
// http://localhost/apps3/index.php/Orders/omc/details?cid=0&on=5356301&csid=319671&from=0
// $formatLink = details?cid=0
// cid = cartID
// on = OrderNumber 
// csid = CustomerID
// from = puede ser OrderManagerCente(omc) = 0 u Details = 1 
//Funciones para OMC
    function formatLink(cellvalue, options,rowData) {
        if (cellvalue!=null){    
            return "<a href=<?= base_url()  . 'index.php' .$formatLink?>"+'&on='+ cellvalue+">" + cellvalue + "</a>";
        }
        
    }
    
    function trackingLink(cellvalue, options,rowData) {
        if (cellvalue!=null){
           
            var carrier = rowData[2];
            switch (carrier){
                case 'UPS':
                   return "<a href= 'http://wwwapps.ups.com/WebTracking/processInputRequest?sort_by=status&tracknums_displayed=1&TypeOfInquiryNumber=T&loc=en_us&InquiryNumber1="+cellvalue+"&track.x=0&track.y=0' target='_blank'>" + cellvalue + "</a>";
                   break;
                 case 'USPS':
                   return "<a href= 'http://trkcnfrm1.smi.usps.com/PTSInternetWeb/InterLabelInquiry.do?origTrackNum="+cellvalue+"' target='_blank'>" + cellvalue + "</a>";
                   break; 
                case 'FDXE':
                   return "<a href= 'http://www.fedex.com/Tracking?language=english&cntry_code=us&tracknumbers="+cellvalue+"' target='_blank'>" + cellvalue + "</a>";
                   break;
                case 'FXM':
                   return "<a href= 'http://www.fedex.com/Tracking?language=english&cntry_code=us&tracknumbers="+cellvalue+"' target='_blank'>" + cellvalue + "</a>";
                   break; 
                case 'FDXG':
                   return "<a href= 'http://www.fedex.com/Tracking?language=english&cntry_code=us&tracknumbers="+cellvalue+"' target='_blank'>" + cellvalue + "</a>";
                   break;
               default:
                   return cellvalue;
                   break;
            }
            
        }
        
    }
</script>
