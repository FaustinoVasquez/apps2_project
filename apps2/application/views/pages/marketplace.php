<style type="text/css" media="screen">

.ui-icon.myicon {
        width: 16px;
        height: 16px;
        background-image:url('<?= base_url() ?>images/toolbar/Excel-icon2.png')
    }
.redclass { background: #D11D20 }
    
</style>

<?php
$formopen = array('id' => 'target', 'class' => 'myform');
$inputsearch = array('id'=>'search','name' => 'search', 'value' => '', 'autocomplete' => 'on', 'maxlength' => '250', 'size' => '20');
$inputPartNumber = array('id'=>'mypart','name' => 'mypart', 'value' => '', 'autocomplete' => 'on', 'maxlength' => '250', 'size' => '20');
$selectListingType = 'class="form-select" id="listingType"';
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
                        <? '&nbsp;' ?><td><?=form_label('PartNumber: ')?></td>
                        <td><?=form_input($inputPartNumber)?></td>
                        <? '&nbsp;' ?><td><?=form_label('ListingType: ')?></td>
                        <td><?=form_dropdown('selectMenu', $listingType,'0', $selectListingType);?></td>
                    </tr>
                    <tr>
                        <td colspan="6"><center><?=form_input($submit).'&nbsp;'.form_input($reset);?></center></td>
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

<script type="text/javascript">

    function resize_the_grid()
    {
        $("#list").fluidGrid({base:'#grid_wrapper', offset:-20});
    }


    function ajaxSave(rowid, curCheckbox,grid) {
     var field = curCheckbox.name;
     var value = curCheckbox.checked;
     var request = $.ajax({
                url: "storeEdit",
                type: "POST",
                data: { id : rowid,
                        checkbox: field,
                        value: value
                       },
                });

     request.done(function( msg ) {
           jQuery("#"+grid).trigger('reloadGrid');
        });
     }

    var ebaygrid = function (id,grid,pager,field1,field2,field3,field4,field5,field6,field7,field8,field9,field10,field11,field12,field13,field14,field15,field16,field17,storename,cellurl,FeedID,gridurl){

    function checkboxFormatter(cellvalue, options, rowObject) {
        cellvalue = cellvalue + "";
        cellvalue = cellvalue.toLowerCase();
        var bchk = cellvalue.search(/(false|0|no|off|n)/i) < 0 ? " checked=\"checked\"" : "";
        return "<input type='checkbox' onclick=\"ajaxSave('" + options.rowId + "', this,'"+grid+"');\" " + bchk + " value='" + cellvalue + "' name='"+options.colModel.name+"' id='"+options.colModel.name+options.rowId+"' offval='no' />";
    }


        switch(id) {
        case 4:
            var colnames = ['FeedID','Title','TitleOverride','StoreCategory','MainCategory','UpsellLink','Template','Price','PriceOverride','ListingID','ListingDate','UpdatedDate','IsActive'];
            var colmodel = [{name:field1,index:field1, width:80, align:'left', hidden:true},
                            {name:field2,index:field2, width:350, align:'left', editable:true},
                            {name:field3,index:field3, width:80, align:'center', editable:true, formatter: checkboxFormatter, edittype: 'checkbox' },
                            {name:field5,index:field5, width:100, align:'left'},
                            {name:field6,index:field6, width:100, align:'left'},
                            {name:field7,index:field7, width:100, align:'left', formatter: formatLink,},
                            {name:field8,index:field8, width:120},
                            {name:field9,index:field9, width:80, align:'center', editable:true, editrules:{number:true}, align:'right',formatter:'currency', formatoptions:{decimalSeparator:'.', thousandsSeparator: ',', decimalPlaces: 2, prefix: '$ '} },
                            {name:field10,index:field10, width:80, align:'center', editable:true, formatter: checkboxFormatter, edittype: 'checkbox' },
                            {name:field11,index:field11, width:80, align:'left'},
                            {name:field12,index:field12, width:80, align:'left'},
                            {name:field13,index:field13, width:80, align:'left'},
                            {name:field14,index:field14, width:50, align:'center', editable: true, formatter: checkboxFormatter, edittype: 'checkbox' },];
            var subfields = 'MLMexicoPriceOverrideStamp';
        break;

            case 5:
            case 6:
                var colnames = ['FeedID','Title','TitleOverride','StoreName','UpsellLink','Template','Price','PriceOverride','IsActive'];
                var colmodel = [{name:field1,index:field1, width:80, align:'left', hidden:true},
                                {name:field2,index:field2, width:350, align:'left', editable:true},
                                {name:field3,index:field3, width:80, align:'center', editable:true, formatter: checkboxFormatter, edittype: 'checkbox' },
                                {name:field4,index:field5, width:100, align:'left'},
                                {name:field7,index:field7, width:100, align:'left', formatter: formatLink,},
                                {name:field8,index:field8, width:120},
                                {name:field9,index:field9, width:80, align:'center', editable:true, editrules:{number:true}, align:'right',formatter:'currency', formatoptions:{decimalSeparator:'.', thousandsSeparator: ',', decimalPlaces: 2, prefix: '$ '} },
                                {name:field10,index:field10, width:80, align:'center', editable:true, formatter: checkboxFormatter, edittype: 'checkbox' },
                                {name:field14,index:field14, width:50, align:'center', editable: true, formatter: checkboxFormatter, edittype: 'checkbox' },];
        break;

        case 7:
            var colnames = ['FeedID','InsertedStamp','UpdatedStamp','MarkForDeletionStamp'];
            var colmodel = [{name:field1,index:field1, width:80, align:'left', hidden:true},
                            {name:field15,index:field12, width:150, align:'left'},
                            {name:field16,index:field13, width:150, align:'left'},
                            {name:field17,index:field14, width:150, align:'left'}];
        break;

        default:
            var colnames = ['FeedID','Title','TitleOverride','StoreName','StoreCategory','MainCategory','UpsellLink','Template','Price','PriceOverride','IsActive'];
            var colmodel = [{name:field1,index:field1, width:80, align:'left', hidden:true}, 
                            {name:field2,index:field2, width:350, align:'left', editable:true},
                            {name:field3,index:field3, width:80, align:'center', editable:true,formatter:checkboxFormatter, edittype: 'checkbox' },
                            {name:field4,index:field4, width:150, align:'left'},
                            {name:field5,index:field5, width:100, align:'left'},
                            {name:field6,index:field6, width:100, align:'left'},
                            {name:field7,index:field7, width:100, align:'left', formatter: formatLink,},
                            {name:field8,index:field8, width:120},
                            {name:field9,index:field9, width:80, align:'center', editable:true, editrules:{number:true}, align:'right',formatter:'currency', formatoptions:{decimalSeparator:'.', thousandsSeparator: ',', decimalPlaces: 2, prefix: '$ '} },
                            {name:field10,index:field10, width:80, align:'center', editable:true,formatter:checkboxFormatter, edittype: 'checkbox' },
                            {name:field14,index:field14, width:50, align:'center', editable: true, formatter: checkboxFormatter, edittype: 'checkbox' }];
        } 


//Campos para subgrid
     switch(id){
        case 1:
          var subgrid = true;
          var subname = ['FeedID','eBay1TitleOverrideStamp','eBay1PriceOverrideStamp'];
          var fields = 'eBay1PriceOverrideStamp';
        break;

        case 2:
            var subgrid = true;
            var subname = ['FeedID','eBay2TitleOverrideStamp','eBay2PriceOverrideStamp']
            var fields = 'eBay2PriceOverrideStamp';
        break;

        case 3:
            var subgrid = true;
            var subname = ['FeedID','eBay3TitleOverrideStamp','eBay3PriceOverrideStamp']
            var fields =  'eBay3PriceOverrideStamp';
        break;

        case 4:
            var subgrid = true;
            var subname = ['FeedID', 'MLMexicoTitleOverrideStamp','MLMexicoPriceOverrideStamp']
            var fields =  'MLMexicoPriceOverrideStamp';
        break;

        case 5:
            var subgrid = true;
            var subname = ['FeedID', 'NewEgg1TitleOverrideStamp','NewEgg1PriceOverrideStamp']
            var fields =  'NewEgg1PriceOverrideStamp';
        break;

        case 6:
            var subgrid = true;
            var subname = ['FeedID', 'Rakuten1TitleOverrideStamp','Rakuten1PriceOverrideStamp']
            var fields =  'Rakuten1TitleOverrideStamp';
        break;

        case 7:
            subgrid = false;
        break;


     }
         jQuery("#"+grid).jqGrid({
                        url:gridurl,
                        postData: { ds: FeedID},
                        datatype: "json",
                        colNames:colnames,
                        colModel:colmodel,
                        pager: "#"+pager,
                        rownumbers: true,
                        sortname: "FeedID",
                        viewrecords: true,
                        sortorder: "asc",
                        caption: storename,
                        mtype: "GET",
                        cellEdit: true,
                        cellurl:cellurl,
                        afterSaveCell: function () {
                            jQuery("#"+grid).trigger('reloadGrid');
                        },
                        subGrid: subgrid,
                        subGridUrl: 'subsubgrid/'+fields,
                        subGridModel: [{
                                         name : subname, 
                                         width : [55,150,150,]
                                      }], 
                        caption: "TimeStamps",
                        
                        loadComplete: function() {
                            var id = jQuery("#"+grid).jqGrid('getDataIDs');
                            var temp = $("#"+field10+id).val();
                            var temp2 = $("#"+field3+id).val();
                            if (temp == 0) {
                                jQuery("#"+grid).jqGrid('setCell',id,field9,'','not-editable-cell');
                            }
                            if (temp2 == 0) {
                                jQuery("#"+grid).jqGrid('setCell',id,field2,'','not-editable-cell');
                            }

                        }
                    });

                    jQuery('#'+grid).navGrid('#'+pager, {edit: false, add: false, del: false});
                    jQuery('#'+grid).trigger('reloadGrid');
    }
        
    $(document).ready(function(){

        var myGrid = $("#list");

        myGrid.jqGrid({
            url:'getData',
            postData: {
                ds:   function() { return jQuery("#search").val(); },
                fil:  function() { return jQuery("#mypart").val(); },
                lis:  function() { return jQuery("#listingType option:selected").val(); },
            },
            datatype: "json",
            colNames:<?= $colNames ?>,
            colModel:<?= $colModel ?>,
            pager: jQuery('#pager'),
            rowNum: 50,
            sortname: 'asc',
            rowList:[50,100,300,500],
            rownumbers: true,
            viewrecords: true,
            caption: "<?= $caption ?>",
            height: 600, 
            toppager:true,  
            editurl:'saveData',
            afterInsertRow:function (rowid,aData){
            if (aData.MarkForDeletion == 1) { 
                myGrid.jqGrid('setCell',rowid,'MarkForDeletion','',{color:'white','background':'#D11D20','font-weight':'bold'})}; 
                },
            subGrid: <?= $subgrid ?>,
            subGridOptions: { "expandOnLoad": false, "reloadOnExpand" : false },
            subGridRowExpanded: function(subgrid_id, row_id) {    
                var rData = jQuery("#list").getRowData(row_id);
                var colData = rData['FeedID'];
                var Store1 = rData['eBay1StoreName'];
                var Store2 = rData['eBay2StoreName'];
                var Store3 = rData['eBay3StoreName'];
                var Store4 = (rData['MLMexicoTitle']!='') ? rData['MLMexicoTitle']:'No Name';
                var Store5 = rData['NewEgg1StoreName'];

                var request = $.ajax({
                url: "getTabs",
                type: "GET",
                data: { id : colData },
                dataType: "html"
                });
                request.done(function( msg ) {
                    $("#"+subgrid_id).html("<div>"+msg+"</div>"); 
                    $( ".tabs" ).tabs()
               
                ebaygrid(1,'ebay1'+colData,'pager1','FeedID','eBay1Title','eBay1TitleOverride','eBay1StoreName','eBay1StoreCategory','eBay1MainCategory','eBay1UpsellLink','eBay1Template','eBay1Price','eBay1PriceOverride','','','','eBay1IsActive','','','','eBay-Discount-Merchant','storeEdit',colData,'store/1');
                ebaygrid(2,'ebay2'+colData,'pager2','FeedID','eBay2Title','eBay2TitleOverride','eBay2StoreName','eBay2StoreCategory','eBay2MainCategory','eBay2UpsellLink','eBay2Template','eBay2Price','eBay2PriceOverride','','','','eBay2IsActive','','','','eBay-DiscountTVLamps','storeEdit',colData,'store/2');
                ebaygrid(3,'ebay3'+colData,'pager3','FeedID','eBay3Title','eBay3TitleOverride','eBay3StoreName','eBay3StoreCategory','eBay3MainCategory','eBay3UpsellLink','eBay3Template','eBay3Price','eBay3PriceOverride','','','','eBay3IsActive','','','','eBay-LampTycoon','storeEdit',colData,'store/3');
                ebaygrid(4,'ml'+colData,'pager4','FeedID','MLMexicoTitle','MLMexicoTitleOverride','','MLMexicoStoreCategory','MLMexicoMainCategory','MLMexicoUpsellLink','MLMexicoTemplate','MLMexicoPrice','MLMexicoPriceOverride','MLMexicoListingID','MLMexicoListingDate','MLMexicoUpdatedDate','MLMexicoIsActive','','','','ML-Mexico','storeEdit',colData,'store/4');
                ebaygrid(5,'ne'+colData,'pager5','FeedID','NewEgg1Title','NewEgg1TitleOverride','NewEgg1StoreName','','','NewEgg1UpsellLink','NewEgg1Template','NewEgg1Price','NewEgg1PriceOverride','','','','NewEgg1IsActive','','','','NewEgg-Discount-Merchant','storeEdit',colData,'store/5');
                ebaygrid(6,'ra'+colData,'pager6','FeedID','Rakuten1Title','Rakuten1TitleOverride','Rakuten1StoreName','','','Rakuten1UpsellLink','Rakuten1Template','Rakuten1Price','Rakuten1PriceOverride','','','','Rakuten1IsActive','','','','Rakuten-Discount-Merchant','storeEdit',colData,'store/6');
                ebaygrid(7,'tabs'+colData,'pager7','FeedID','','','','','','','','','','','','','','InsertedStamp','UpdatedStamp','MarkForDeletionStamp','Timestamp','storeEdit',colData,'store/7');
                });
            },
            loadComplete: function() {
                    var ids = myGrid.jqGrid('getDataIDs');
                    for (var i=0;i<ids.length;i++) {
                        var id=ids[i];
                        var rowData = myGrid.jqGrid('getRowData',id);
                        if (rowData.MarkForDeletion == 'Yes'){
                            $('#'+id,myGrid[0]).attr('title', 'Deleted at: '+rowData.MarkForDeletionStamp );
                        }
                    }
                }
        });


        jQuery("#list").jqGrid('navGrid','#pager',{cloneToTop:true,edit:false,add:false,del:false,search:false,refresh:true});

        myGrid.jqGrid('navButtonAdd', '#' + myGrid[0].id + '_toppager_left', { // "#list_toppager_left"
            caption: "Excel",
            buttonicon: 'myicon',
            onClickButton: function(){
                exportExcel(myGrid,'<?= $export ?>'); 
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

            //Recargar el grid en el evento onChange del select search del formulario
            $( "#listingType" ).on('change',function(){
               myReload();
            });

            //Recargar el grid en el evento onChange del select search del formulario
            $( "#mypart" ).on('change',function(){
               myReload();
            });

    });
    
    $(window).resize(resize_the_grid);

    $(function() { $( "#mypart" ).autocomplete({source: "<?= base_url() ?>index.php/tools/marketplace/fillselect/"}) });;


    function formatLink(cellvalue, options,rowData){         
        if (cellvalue==0)
        {
            return '';
        }
        else
        {
            return '<a href="'+cellvalue+'" target="_blank">Click To Store</a>'; 
        }
    }

    function yesno(cellvalue, options,rowObject){         
        if (cellvalue==0)
        {
            return 'No'; 
        }
        else
        {
            return 'Yes';
        }
    }



</script>