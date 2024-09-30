


<style type="text/css" media="screen">

    .ui-icon.excel {
        width: 16px;
        height: 16px;
        background-image:url('<?= base_url() ?>/images/toolbar/Excel-icon2.png');
    }

</style>
 


<div class="overlay" id="overlay" style="display:none;"></div>
<div class="box" id="box">
    <a class="boxclose" id="boxclose"></a>

    <center>
        <p><b> Consolidation for&nbsp;<?= $cartOptions[$selectedcart];if($customerId !=''){echo '<br> CustomerID  '.$customerId;} ?></b><br>
         <?= '<b>From:</b> ' . date("l jS \of F Y ", strtotime($datefrom)) . '<br><b>To</b>: ' . date("l jS \of F Y ", strtotime($dateto)) ?></p>
        <table>
            <tbody>
                <?php if($administrator){
                   echo' <tr><td id="paidfull">Paid in Full:</td><td id="tpaidfull"> $ ' . number_format($paidinfull, 2, '.', ',').'</td></tr>';
                  }?>
                
                <tr><td id="balancedue">Balance Due:</td><td id="tbalancedue"><?= '$ ' . number_format($balancedue, 2, '.', ','); ?><td></tr>
                <tr><td id="creditdue">Credit Due:</td><td id="tcreditdue"><?= '$ ' . number_format($creditdue, 2, '.', ','); ?></td></tr>
            </tbody>
        </table>
    </center>
    <br></br>
</div>




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
 
        $statusdropdown = 'class="form-select" onChange="this.form.submit()"';
        echo form_dropdown('status', $statusOptions, isset($lineselect->status) ? $lineselect->status : $this->input->post('status'), $statusdropdown);
    
                
        $cartdropdown = 'class="form-select" onChange="this.form.submit()"';
        echo form_dropdown('selectedcart', $cartOptions, isset($lineselect->selectedcart) ? $lineselect->selectedcart : $this->input->post('selectedcart'), $cartdropdown);

        $clickme = array('name' => 'button1','id' => 'button','value' => 'ClickMe','type' => 'button','content' => 'ClickMe','onClick'=>'consolidate()');
        echo form_button($clickme);
        
        
        echo '<br>';
        $customerId = array('name' => 'customerId', 'value' => $customerId, 'autocomplete' => 'on', 'maxlength' => '250', 'size' => '8', 'onclick' => "this.value=''");
        if(!$administrator){
        $customerId['disabled']='disabled';
        }
        echo "CustomerID:" . form_input($customerId);
      
        echo "&nbsp;&nbsp;";
        $inputfrom = array('id' => 'from', 'name' => 'datefrom', 'class' => 'date-pick', 'value' => $datefrom, 'size' => '10', 'onchange' => "this.form.submit()", 'style' => 'width:80px;text-align:center');
        echo "from:" . form_input($inputfrom);
        
        echo "&nbsp;&nbsp;";
        $inputto = array('id' => 'to', 'name' => 'dateto', 'class' => 'date-pick', 'value' => $dateto, 'size' => '10', 'onchange' => "this.form.submit()", 'style' => 'width:80px;text-align:center');
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


<div class="clear"></div>

<section id="grid_container">
<table id="list" class="scroll"></table>
<div id="pager" class="scroll"></div>
</section>


<script type="text/javascript">

    //funcion para hacer el autoresize
    
    
    function resize_the_grid()
    {
        $('#list').fluidGrid({base:'#grid_wrapper', offset:-20});
    }
    
 
    
$(document).ready(function(){
    
    var myGrid = $("#list");

    myGrid.jqGrid({
        url:'<?= base_url() . 'index.php' . $from . $gridSearch?>',
           
        datatype: "json",
        height: 575,
        colNames:<?= $headers ?>,
        colModel:<?= $body ?>,
        pager: '#pager',
        rowNum: 50,
        rowList: [50,100,1000],
        rownumbers: true,
        sortname: 'OrderNumber',
        sortorder: 'desc',
        viewrecords: true,
        caption: "<?= $caption ?>",
        toppager:true,
        afterInsertRow: function(rowid, aData){ 
            if (aData.BalanceDue == 0) { jQuery("#list").jqGrid('setCell',rowid,'BalanceDue','',{color:'black','background':'#49F574','font-weight':'bold'})}; 
            if (aData.BalanceDue > 0) { jQuery("#list").jqGrid('setCell',rowid,'BalanceDue','',{color:'white','background-color':'#D11D20','font-weight':'bold'})}; 
            if (aData.BalanceDue < 0) { jQuery("#list").jqGrid('setCell',rowid,'BalanceDue','',{color:'black','background-color':'#F5F249','font-weight':'bold'})}; 
                 
        },
         subGrid: true,
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
                   url:'<?= base_url() . 'index.php/Orders/omc/trackingData?on=' ?>'+row_id,  
                   datatype: "json",  
                   colNames: ['OrderNum','Tracking #','Carrier','Shipping Method','Date Shipped'], 
               colModel: [ 
                    {name:'OrderNum',index:'OrderNum', width:100, align:'center', hidden:true},
                    {name:'TrackingId',index:'TrackingId', width:200, align:'center', formatter: trackingLink},
                    {name:'Carrier',index:'Carrier', width:100, align:'center'},
                    {name:'ShippersMethod',index:'shippingMethod', width:100, align:'center'},
                    {name:'DateAdded',index:'DateAdded', width:100, align:'center',formatter:'date',formatoptions: {newformat:'F j, Y'}},
               ], 
                   width:700,
                   rowNum:20, 
                   pager: pager_id, 
                   caption: "Tracking Information",
                   sortname: 'num', 
                   sortorder: "asc", 
                   height: '100%'
                   
               });
          jQuery("#"+subgrid_table_id).jqGrid('navGrid',"#"+pager_id,{edit:false,add:true,del:false})
        } 
        
    });  
    myGrid.jqGrid('navGrid','#pager',{cloneToTop:true,view:true,del:false,add:false,edit:false},{},{},{},{multipleSearch:true}) 
    resize_the_grid(myGrid);  
    });
    
      $(window).resize(resize_the_grid);


    var topPagerDiv = $('#' + jQuery("#list")[0].id + '_toppager')[0]; 
    $("#reload_" + jQuery("#list")[0].id + "_top", topPagerDiv).remove();
    $("#view_" + jQuery("#list")[0].id + "_top", topPagerDiv).remove(); // "#list_toppager"
    $("#edit_" + jQuery("#list")[0].id + "_top", topPagerDiv).remove();        // "#edit_list_top"
    $("#del_" + jQuery("#list")[0].id + "_top", topPagerDiv).remove();         // "#del_list_top"
    $("#search_" + jQuery("#list")[0].id + "_top", topPagerDiv).remove();      // "#search_list_top"
    var bottomPagerDiv = $("div#pager14")[0];
    $("#add_" +jQuery("#list")[0].id, bottomPagerDiv).remove();               // "#add_list"




    
    
  
    


    /*
     *
     *  RowObject Trae todo el arreglo, desde ahi podemos acceder a otras celdas del renglon
     *
     */



    function gridReload(){
        var nm_mask = jQuery("#item_nm").val();
        var cd_mask = jQuery("#search_cd").val();
       
        if (nm_mask == 0) {
            nm_mask="";
 
            jQuery("#list").jqGrid('setGridParam',{url:"<?= base_url() . 'index.php' . $from . 'gridDataCatalog' ?>?cd_mask="+cd_mask+"&page="+1}).trigger("reloadGrid");
            cd_mask ="";
        }
        else {
            jQuery("#list").jqGrid('setGridParam',{url:"<?= base_url() . 'index.php' . $from . 'gridDataCatalog' ?>?nm_mask="+nm_mask+"&cd_mask="+cd_mask+"&page="+1}).trigger("reloadGrid");
            nm_mask ="";
            cd_mask ="";
        }


    }
    function enableAutosubmit(state){
        flAuto = state;
        jQuery("#submitButton").attr("disabled",state);
    }

    function formatLink(cellvalue, options,rowData) {
        if (cellvalue!=null){
            
           return "<a href=<?= base_url()  . 'index.php' .$formatLink?>"+'&on='+ cellvalue +'&csid='+ rowData[8]+">" + cellvalue + "</a>";
        }
        
    }
 

    function reload(){
        $("#list").jqGrid('setGridParam', { url:'<?= base_url() . 'index.php/admin/' . $from . '/gridDataCatalog/' ?>'}).trigger("reloadGrid");
    }
   
   
    function balanceduo(cellvalue, options,rowData) {
        if ((cellvalue == 0)) {
            return '<div class="colorBlock" style="background-color: white; color: green; font-weight: bold;">'+ cellvalue +'</div>';
        }
        if (cellvalue > 0) {
            return '<div class="colorBlock" style="background-color: white; color: red; font-weight: bold;">'+ cellvalue +'</div>';
        }
        
        if (cellvalue < 0) {
            return '<div class="colorBlock" style="background-color: white; color: yellow; font-weight: bold;">'+ cellvalue +'</div>';
        }
    }
    
   
       $(function() {
        $( ".date-pick" ).datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat:"mm/dd/yy"
        });
    });   
   
  function consolidate(){  
        
        $('#overlay').fadeIn('fast',function(){
            $('#box').animate({'top':'200px'},500);
        });
        $('#boxclose').click(function(){
            $('#box').animate({'top':'-300px'},500,function(){
                $('#overlay').fadeOut('fast');
            });
        });
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







