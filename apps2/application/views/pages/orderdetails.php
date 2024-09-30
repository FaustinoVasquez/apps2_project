
<div class="headerdata" >
    <center><h1 class="h1title">Order Detail</h1></center>
    <b>Order Number: </b><?= strtoupper($ordernumber) ?>
    <div class="back">
        <a href="<?=base_url().'index.php'.$back?>">Back To: <?=$retwhere?></a>
    </div>
    <div class="statusOrder">
        <b>Sold By:</b> <?=$cartname?><br/>
        <b>Sales Rep :</b><?=$salesrep?> <br/>
        <b>Status:</b><?=$StatusOrder?>
    </div>
</div>




<div class="detailedOrder" >

    <!-- Sold To -->
    <?php
    if ($soldTo) {
        echo '<div class="soldto" style="float:left;margin-bottom:5px">';
        echo '<table id="soldto">';
        echo ' <thead>';
        echo '   <tr>';
        echo '     <th id="ConceptSold">Concept</th> ';
        echo '     <th id="DataSold">Data</th>';
        echo '   </tr>';
        echo '  <thead>';
        echo '   <tbody>';
        echo '   <tr><td>Name:</td><td>' . strtoupper($solddata["Name"]) . '</td></tr>';
        echo '   <tr><td>Address:</td><td>' . strtoupper($solddata["Address"]) . '</td></tr>';
        echo '   <tr><td>City:</td><td>' . strtoupper($solddata["City"]) . '&nbsp;&nbsp;' . strtoupper($solddata["State"]) . '&nbsp;&nbsp;' . strtoupper($solddata["Zip"]) . '</td></tr>';
        echo '   <tr><td>Country:</td><td>' . strtoupper($solddata["Country"]) . '</td></tr>';
        echo '   <tr><td>CustomerID:</td><td><label id="customerid">' . strtoupper($solddata["CustomerID"]) . '</td></tr>';
        echo '   <tr><td>Email:</td><td>' . $solddata["Email"] . '</td></tr>';
        echo '   <tr><td>Phone:</td><td>' . strtoupper($solddata['Phone']) . '</td></tr>';
        echo '   <tr><td>Company:</td><td>' . strtoupper($solddata['Company']) . '</td></tr>';
        echo '   </tbody>';
        echo '</table>';
        echo '</div>';
    }
    ?>


    <!-- Ship To -->
    <?php
    if ($shipTo) {
        echo '<div class="shipto" style="float:right ;margin-bottom:5px">';
        echo '<table id="shipto">';
        echo '   <tr>';
        echo '     <th id="ConceptShip">Concept</th> ';
        echo '     <th id="DataShip">Data</th>';
        echo '   </tr>';
        echo '   <tbody>';
        echo '   <tr><td>Name:</td><td>' . strtoupper($shipdata["ShipName"]) . '</td></tr>';
        echo '   <tr><td>Address:</td><td>' . strtoupper($shipdata["ShipAddress"]) . '</td></tr>';
        echo '   <tr><td>City:</td><td>' . strtoupper($shipdata["ShipCity"]) . '&nbsp;&nbsp;' . strtoupper($shipdata["ShipState"]) . '&nbsp;&nbsp;' . strtoupper($shipdata["ShipZip"]) . '</td></tr>';
        echo '   <tr><td>Country:</td><td>' . strtoupper($shipdata["ShipCountry"]) . '</td></tr>';
        echo '   <tr><td></td><td></td></tr>';
        echo '   <tr><td>Email:</td><td>' . $shipdata["ShipEmail"] . '</td></tr>';
        echo '   <tr><td>Phone:</td><td>' . strtoupper($shipdata['ShipPhone']) . '</td></tr>';
        echo '   <tr><td>Company:</td><td>' . strtoupper($shipdata['ShipCompany']) . '</td></tr>';
        echo '   </tbody>';
        echo '</table>';
        echo '</div>';
    }
    ?>


    <!-- Grid Product -->

    <?php
    if ($productList) {

        echo '<div id="product" class="product" style="float:left ;margin-bottom:5px">';
        echo '   <table id="products" border="1"></table>';
        echo ' <div id="pager1"></div>';
        echo '</div> ';
    }
    ?>


    <!-- Grid Serials -->
    <?php
    if ($serialofProductShipped) {
        echo '<div class="serials" style="float:left ;margin-bottom:5px">';
        echo '    <table id="product_serials" border="1"> </table>';
        echo '</div>';
    }
    ?>


    <!-- Order Comments -->
    <?php
    if ($orderComments) {
        echo '<div class="orderComments" style="float:left ;margin-bottom:5px">';
        echo '    <table id="orderComments" border="1">';
        echo '        <tr>';
        echo '      <th>Comments</th>';
        echo '   </tr>';
        echo '   <tbody>';
        echo '   <tr>';
        echo '      <td>' . $comments['Comments'] . '</td>';
        echo '   </tr>';
        echo '   </tbody>';
        echo '    </table>';
        echo '</div>';
    }
    ?>


    <!-- Grid Totals -->
    <?php
    if ($totals) {
        echo '<div class="totals" style="float:right ;margin-bottom:5px">';
        echo '    <table id="totals" border="1"></table>';
        echo '</div>';
    }
    ?>


    <!-- Order Tracking Information -->
    <?php
    if ($orderTrackingInformation) {
        echo '<div id="tracking" class="tracking" style="float:left ;margin-bottom:5px">';
        echo '    <table id="orderTrackingInformation" border="1"></table>';
        echo '    <div id="pager2"></div>';
        echo '</div>';
    }
    ?>


    <!-- Order Notes-->
    <?php
    if ($orderNotes) {
        echo '<div id="orderNotes" class="orderNotes" style="float:left ;margin-bottom:5px">';
        echo '    <table id="getOrderNotes" border="1"></table> ';
        echo '    <div id="pager3"></div>';
        echo '</div>';
    }
    ?>


    <!-- Notes -->
    <?php
    if ($notes) {
        echo '<div id="notes" class="notes" style="float:right;margin-bottom:5px">';
        echo '    <table id="getNotes" border="1"></table>';
        echo '    <div id="pager4"></div>';
        echo '</div>';
    }
    ?> 

</div>


<script type="text/javascript"> 
   
    /*
     * 
     * 
     * 
     * 
     * 
     */ 
    $(document).ready(function(){
         
    
     
     
        tableToGrid("#soldto", { 
          
            colModel: [
                { name: 'ConceptSold', width: 80,sortable:false},
                { name: 'DataSold', width: 320,align:'left',sortable:false },
            ],
            width:400,
            height:'100%',
            rowNum: 50,
            rowList: [25,50,75],
            viewrecords: true,
            caption: "Sold To:",
            sortorder: "asc",
            scrollOffset:0,
            mtype: "GET"});
 
        /*
         * 
         * 
         * 
         * 
         * 
         */ 
    
        tableToGrid("#shipto", {  
            colModel: [
                { name: 'ConceptShip', width: 80,sortable:false},
                { name: 'DataShip', width: 320,align:'left',sortable:false },
            ],
            width:400,
            height:'100%',
            rowNum: 50,
            rowList: [25,50,75],
            viewrecords: true,
            caption: "Ship To:",
            sortorder: "asc",
            scrollOffset:0,
            mtype: "GET"});
    
        /*
         * 
         * 
         * 
         * 
         * 
         */
 
        jQuery("#products").jqGrid({
            url:'<?= base_url() . 'index.php' . $from . 'getItemsData' ?>?on=<?= $ordernumber ?>',
           
            datatype: "json",
        
            colNames:['SKU','Product','Unit Price','Ordered','Item Status','Ext. Price'],
            colModel:[
                {name:'SKU',index:'SKU', width:50, align:'left'  },
                {name:'Product',index:'Product', width:200, align:'left'},
                {name:'PricePerUnit',index:'SerialNumber', width:55, align:'right',formatter:'currency', formatoptions:{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2, prefix: "$ "}},
                {name:'QuantityOrdered',index:'SKU', width:40, align:'center'  },
                {name:'Status',index:'SKU', width:50, align:'center'  },
                {name:'extprice',index:'Product', width:55, align:'right',formatter:'currency', formatoptions:{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2, prefix: "$ "}},
             
            ],
            width:1010,
            pager: "#pager1",
            rowNum: 50,
            rowList: [25,50,75],
            rownumbers: true,
            sortname: "SKU",
            viewrecords: true,
            sortorder: "asc",
            caption: "Product List",
            mtype: "GET"});

        jQuery('#products').navGrid('#pager1', {edit: false, add: false, del: false});
        jQuery('#products').trigger('reloadGrid');
    
    
    
    
        /*
         * 
         * 
         * 
         * 
         * 
         */

        jQuery("#product_serials").jqGrid({
            url:'<?= base_url() . 'index.php' . $from . 'getSkuAndSerialNumber' ?>?on=<?= $ordernumber ?>',
           
            datatype: "json",
            colNames:['SKU','Product','S/N'],
            colModel:[
                {name:'SKU',index:'SKU', width:50, align:'left'  },
                {name:'Product',index:'Product', width:150, align:'left'},
                {name:'SerialNumber',index:'SerialNumber', width:70, align:'center'},
            ],
            width:1010,
            height:100,
            rowNum: 50,
            rowList: [25,50,75],
            rownumbers: true,
            sortname: "SKU",
            viewrecords: true,
            sortorder: "asc",
            caption: "Serial# of Product(s) Shipped",
            mtype: "GET"});

        jQuery('#product_serials').navGrid('#pager2', {edit: false, add: false, del: false});
        jQuery('#product_serials').trigger('reloadGrid');
   
   
   
   
   
        /*
         * 
         * 
         * 
         * 
         */

        jQuery("#totals").jqGrid({
            url:'<?= base_url() . 'index.php' . $from . 'getTotals' ?>?on=<?= $ordernumber ?>', 
            datatype: "json",
            colNames:['Charge','Total'],
            colModel:[
                {name:'Charge',index:'Charge', width:100, align:'right'  },
                {name:'Total',index:'Total', width:100, align:'right',formatter:'currency', formatoptions:{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2, prefix: "$ "}},
            ],
            width:300,
            height:'100%',
            rowNum: 50,
            rowList: [25,50,75],
            viewrecords: true,
            caption: "Totals",
            sortorder: "asc",
            scrollOffset:0,
            mtype: "GET"});  
 

        /*
         * 
         * 
         * 
         * 
         * 
         * 
         */
        tableToGrid("#orderComments", {  
            width:600,
            height:154,
            rowNum: 50,
            rowList: [25,50,75],
            viewrecords: true,
            caption: "Order Comments",
            sortorder: "asc",
            mtype: "GET"});

        /*
         * 
         * 
         * 
         * 
         * 
         * 
         */
        jQuery("#orderTrackingInformation").jqGrid({
            url:'<?= base_url() . 'index.php' . $from . 'getOrderTrackingInformation' ?>?on=<?= $ordernumber ?>',
           
            datatype: "json",
            colNames:['Date','Tracking #','Carrier','Service','Weight','Ounces','Notes'],
            colModel:[
                {name:'DateAdded',index:'DateAdded', width:100, align:'left'  },
                {name:'TrackingID',index:'TrackingID', width:100, align:'left'},
                {name:'Carrier',index:'Carrier', width:70, align:'center'},
                {name:'ShippersMethod',index:'ShippersMethod', width:100, align:'center'  },
                {name:'Pounds',index:'Pounds', width:50, align:'center',formatter:formatpounds},
                {name:'Ounces',index:'Ounces', width:50, hidden:true },
                {name:'Notes',index:'Notes', width:100, align:'center'},
            ],
            width:1010,
            height:100,
            rowNum: 50,
            rowList: [25,50,75],
            rownumbers: true,
            sortname: "SKU",
            viewrecords: true,
            sortorder: "asc",
            caption: "Order Tracking Information"
        });
 
 
        function formatpounds(cellvalue, options,rowData) {
            return cellvalue +' Lb '+rowData[5]+' Oz';    
        }
    
        /*
         * 
         * 
         * 
         */
        jQuery("#getOrderNotes").jqGrid({
            url:'<?= base_url() . 'index.php' . $from . 'getOrderNotes' ?>?on=<?= $ordernumber ?>',
           
            datatype: "json",
            colNames:['AutoNumber','Date','Note By'],
            colModel:[
                {name:'AutoNumber',index:'AutoNumber', width:100, align:'left',hidden:true  },
                {name:'EntryDate',index:'EntryDate', width:100, align:'left'  },
                {name:'EnteredBy',index:'EnteredBy', width:100, align:'left'},
           
            ],
            width:500,
            height:100,
            pager: "#pager3",
            rowNum: 50,
            rowList: [25,50,75],
            rownumbers: true,
            sortname: "SKU",
            viewrecords: true,
            sortorder: "asc",
            caption: "Order Notes",
            onSelectRow: function(ids) {
                jQuery("#getNotes").jqGrid('setGridParam',{url:"<?= base_url() . 'index.php' . $from . 'getNotes?on=' . $ordernumber ?>"+"&ids="+ids});
                jQuery("#getNotes").jqGrid('setCaption',"Note: "+ids) .trigger('reloadGrid');
            }
        });

   
 
        /*
         * 
         * 
         *
         */
        jQuery("#getNotes").jqGrid({
       
            url:'<?= base_url() . 'index.php' . $from . 'getNotes' ?>?on=<?= $ordernumber ?>',
           
   
            datatype: "json",
            colNames:['Note'],
            colModel:[
                {name:'Note',index:'EntryDate', width:100, align:'left'  },
            
           
            ],
            width:500,
            height:100,
            pager: "#pager4",
            rowNum: 50,
            rowList: [25,50,75],
            rownumbers: true,
            sortname: "SKU",
            viewrecords: true,
            sortorder: "asc",
            caption: "Notes",
            scrollOffset:0
        });
    });


</script>
