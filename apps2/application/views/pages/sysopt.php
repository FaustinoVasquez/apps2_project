
<?php
    $frmOpen   = array('id'=>'target','class' => 'myform');

    $proccessKiller = array('name' => 'ProccessKiller','id' => 'proccessKiller','value' => true, 'content' => 'Execute', 'type'=>'button');
    $proccessKillerLb = 'This action kill all processes that have more than 5 minutes of running on the database:';

    $createAmazonMerchantSKU = array('name' => 'CreateAmazonMerchantSKU','id' => 'createAmazonMerchantSKU','value' => true, 'content' => 'Execute' , 'type'=>'button');
    $createAmazonMerchantSKULb = 'Invoke AmazonMerchantSKU Generation:';

    $upProdCatFlPr = array('name' => 'UpProdCatFlPr','id' => 'upProdCatFlPr','value' => true, 'content' => 'Execute', 'type'=>'button');
    $upProdCatFlPrLb = 'Update Floor and Ceiling Prices (Runs Every Week Automatically):';
    
    $calculateAvgShipCost= array('name' => 'CalculateAvgShipCost','id' => 'calculateAvgShipCost','value' => true, 'content' => 'Execute', 'type'=>'button');
    $calculateAvgShipCostLb = 'Update Average Shipping Costs (Runs Every Week Automatically):';

    $updateAmazonTable= array('name' => 'UpdateAmazonTable','id' => 'updateAmazonTable','value' => true, 'content' => 'Execute', 'type'=>'button');
    $updateAmazonTableLb = 'Update MITSKU In Amazon Table (Runs Every Night Automatically):';

    $upVirtStockBulbsAndKits= array('name' => 'UpVirtStockBulbsAndKits','id' => 'upVirtStockBulbsAndKits','value' => true, 'content' => 'Execute', 'type'=>'button');
    $upVirtStockBulbsAndKitsLb = 'Update Virtual Stock for Bulbs and Kits (Runs Every 2 Hours Automatically):';

    $updateVirtLampWithHousing= array('name' => 'UpdateVirtLampWithHousing','id' => 'updateVirtLampWithHousing','value' => true, 'content' => 'Execute', 'type'=>'button');
    $updateVirtLampWithHousingLb = 'Update Virtual Stock for Lamps with Housing (Runs Every 2 Hours Automatically):';

    $reindexInventoryDatabase= array('name' => 'ReindexInventoryDatabase','id' => 'reindexInventoryDatabase','value' => true, 'content' => 'Execute', 'type'=>'button');
    $reindexInventoryDatabaseLb = 'Reindex Inventory Database (Warning May Halt Database for 30 Minutes):';

    $reindexOrderManagerDatabase= array('name' => 'ReindexOrderManagerDatabase','id' => 'reindexOrderManagerDatabase','value' => true, 'content' => 'Execute', 'type'=>'button');
    $reindexOrderManagerDatabaseLb = 'Reindex Order Manager Database (Warning May Halt Database for 30 Minutes):';

    $emailInventoryAtDate= array('name' => 'emailInventoryAtDate','id' => 'emailInventoryAtDate','value' => true, 'content' => 'Execute', 'type'=>'button');
    $emailInventoryAtDateLb = 'Send By Email the Inventory at Specific Date:';


echo form_open(base_url(), $frmOpen);
?>
    <fieldset class="bluegradiant">
    <div class="logo"><img longdesc="MI Technologies" title="Mi Tech" alt="43" src="<?= base_url() ?>/images/header/mitechnologies.png"></div>
        <div class="form-data"> 
            <center>
            <table class="skudata">
              <tr><td colspan=2 ><strong>MITSQL Server Process</strong></td></tr> 
              <tr><td id ='td1'><?=form_label($proccessKillerLb)?></td><td><?=form_button($proccessKiller)?></td></tr> 
              <tr><td id ='td2'><?=form_label($createAmazonMerchantSKULb)?></td><td><?=form_button($createAmazonMerchantSKU)?></td></tr> 
              <tr><td id ='td3'><?=form_label($upProdCatFlPrLb)?></td><td><?=form_button($upProdCatFlPr)?></td></tr> 
              <tr><td id ='td4'><?=form_label($calculateAvgShipCostLb)?></td><td><?=form_button($calculateAvgShipCost)?></td></tr> 
              <tr><td id ='td5'><?=form_label($updateAmazonTableLb)?></td><td><?=form_button($updateAmazonTable)?></td></tr> 
              <tr><td id ='td6'><?=form_label($upVirtStockBulbsAndKitsLb)?></td><td><?=form_button($upVirtStockBulbsAndKits)?></td></tr> 
              <tr><td id ='td7'><?=form_label($updateVirtLampWithHousingLb)?></td><td><?=form_button($updateVirtLampWithHousing)?></td></tr> 
              <tr><td id ='td8'><?=form_label($reindexInventoryDatabaseLb)?></td><td><?=form_button($reindexInventoryDatabase)?></td></tr> 
              <tr><td id ='td9'><?=form_label($reindexOrderManagerDatabaseLb)?></td><td><?=form_button($reindexOrderManagerDatabase)?></td></tr> 
            </table>
            </center>
        </div>
    </fieldset>
<br>
<?php echo form_close(); ?>  
         
    
<div class="clear"></div>

    
    
<script type="text/javascript">

    $(function(){
        var dialog;

        $('#proccessKiller').on( "click", function(e) {
            e.preventDefault();
            openDialog('processkiller','#td1');
        });

        $('#createAmazonMerchantSKU').on( "click", function(e) {
            e.preventDefault();
            openDialog('amazonMerchantSKU','#td2');
        });


        $('#upProdCatFlPr').on( "click", function(e) {
            e.preventDefault();
            openDialog('UpdateProductCatalogFloorPrice','#td3');
        });


        $('#calculateAvgShipCost').on( "click", function(e) {
            e.preventDefault();
            openDialog('CalculateAvgShipCost','#td4');
        });

         $('#updateAmazonTable').on( "click", function(e) {
            e.preventDefault();
            openDialog('UpdateAmazonTable','#td5');
        });


        $('#upVirtStockBulbsAndKits').on( "click", function(e) {
            e.preventDefault();
            openDialog('updateVirtStockBulbsAndKits','#td6');
        });


        $('#updateVirtLampWithHousing').on( "click", function(e) {
            e.preventDefault();
            openDialog('updateVirtStockLampWithHousing','#td7');
        });


        $('#reindexInventoryDatabase').on( "click", function(e) {
            e.preventDefault();
            openDialog('reindexInventoryDatabase','#td8');
        });

         $('#reindexOrderManagerDatabase').on( "click", function(e) {
            e.preventDefault();
            openDialog('reindexOrderManagerDatabase','#td9');
        });

        // $('#emailInventoryAtDate').on( "click", function(e) {
        //     e.preventDefault();
           
        //     openDialog('inventory');
        // });

        function openDialog(url,td){
            $('#MyDialog').dialog({
                title: 'ALERT...',
                autoOpen: false,
                modal: true,
                open: function(event, ui) {
                    $('#MyDialog').empty();
                    $('#MyDialog').append('<p>Are you sure run this process?</p>');
                },
                buttons: {
                    'Execute': function() {
                        request = $.ajax({
                        url: url,
                        async: false,
                        });

                       $(td).append('<div style="color:red">This process was started</div>');
                       $('#MyDialog').dialog('close'); 
                    },
                    'Cancel': function() {
                       $('#MyDialog').dialog('close');
                    },
                   
                }
            });
            $('#MyDialog').dialog('open');
        }
    });

    
</script>


<div id="MyDialog" title=""></div>

