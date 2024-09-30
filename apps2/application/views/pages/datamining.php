<style type="text/css" media="screen">
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

    .ui-icon.myicon {
        width: 16px;
        height: 16px;
        background-image:url('<?=base_url()?>images/toolbar/Excel-icon2.png')
    }

    .ui-icon.order {
        width: 16px;
        height: 16px;
        background-image:url('<?=base_url()?>images/toolbar/order.png');
    }

    .ui-icon.del {
        width: 16px;
        height: 16px;
        background-image:url('<?=base_url()?>images/toolbar/del.png');
    }


</style>

<?php
$formopen       = array('id' => 'target', 'class' => 'myform');
$inputsearch    = array('id' => 'search', 'name' => 'search', 'value' => '', 'autocomplete' => 'on', 'maxlength' => '250', 'size' => '20');
$selectChannel  = 'class="form-select" id="channel"';
$selectCategory = 'class="form-select" id="categoria"';
$selectCountry  = 'class="form-select" id="country"';
$submit         = array('name' => 'send', 'value' => 'Submit', 'type' => 'submit', 'class' => 'button');
$reset          = array('name' => 'reset', 'value' => 'Reset', 'type' => 'submit', 'class' => 'button');

echo form_open(base_url().'index.php'.$from, $formopen);
?>
<fieldset class="bluegradiant">
    <div class="logo"><img longdesc="MI Technologies" title="Mi Tech" alt="43" src="<?=base_url()?>/images/header/mitechnologies.png"></div>
    <div class="form-data">
    <center>
        <table>
            <tbody>
                <tr>
                    <td><?=form_label('Search: ')?></td>
                    <td><?=form_input($inputsearch)?></td>
                </tr>
                <tr>
                    <td><?=form_label('Channel: ')?></td>
                    <? '&nbsp;' ?><td><?=form_dropdown('selectMenu', $channel, '0', $selectChannel);
?>
</td>
                    <td><?=form_label('Category: ')?></td>
                    <? '&nbsp;' ?><td><?=form_dropdown('selectMenu', $categoria, '0', $selectCategory);
?></td>
                    <td><?=form_label('CountryCode: ')?></td>
                    <? '&nbsp;' ?><td><?=form_dropdown('selectMenu', $country, 'US', $selectCountry);
?></td>
                </tr>
                <tr>
                    <td colspan="6"><center><?=form_input($submit).'&nbsp;'.form_input($reset);
?></center></td>
                </tr>
            </tbody>
        </table>
    <center>
    </div>
</fieldset>
<?php
echo form_close();
?>

<div class="clear"></div>

<div style="background-color: #B2FFFF">
    <button id="amazon"> Add_New_Product </button>
</div>

<section id="grid_container">
    <table id="list"></table>
    <div id="pager"></div>
</section>


<form method="post" action="<?=base_url().'index.php'.$from.'csvExport'?>">
    <input type="hidden" name="csvBuffer" id="csvBuffer" value="" />
</form>

<div id="dialog-modal" title="Details" style="display: none;"></div>

<script type="text/javascript">

    $(function() {
        var ChannelName = $( "#ChannelName" ),
        Asin = $( "#Asin" ),
        CountryCode = $( "#CountryCode" ),
        IsActive = $( "#IsActive" ),
        Title = $( "#Title" ),
        Category = $( "#Category" ),
        manufacturerpn = $( "#manufacturerpn" ),
        manufacturer = $( "#manufacturer" ),
        BrandMentioned = $( "#BrandMentioned" ),
        BrandSell = $( "#BrandSell" ),
        LampType = $( "#LampType" ),
        ProductCatalogId = $( "#ProductCatalogId" ),
        Comments = $( "#Comments" ),

        allFields = $( [] ).add( ChannelName )
        .add( Asin )
        .add( CountryCode )
        .add( IsActive )
        .add( Title )
        .add( Category )
        .add( manufacturerpn )
        .add( manufacturer )
        .add( BrandMentioned )
        .add( BrandSell )
        .add( LampType )
        .add( ProductCatalogId )
        .add( Comments ),

        tips = $( ".validateTips" );

        function updateTips( t ) {
            tips
            .text( t )
            .addClass( "ui-state-highlight" );
            setTimeout(function() {
                tips.removeClass( "ui-state-highlight", 1500 );
            }, 1000 );
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

        function completehtml(myurl,mydata,fieldtofill){
            var req = $.ajax({
                url:myurl,
                type: "POST",
                data: {q: mydata}
            });
            req.done(function(ret) {
                $(fieldtofill).html(ret);

            });
        }

        function fillValue(myurl,mydata,fieldtofill){
            var req = $.ajax({
                url:myurl,
                type: "POST",
                data: {q: mydata}
            });
            req.done(function(ret) {
                $(fieldtofill).val(ret);

            });
        }

        function fillValue2(myurl,mydata1,mydata2,fieldtofill){
            var req = $.ajax({

                url:myurl,
                type: "POST",
                data: {q: mydata1, r: mydata2}
            });
            req.done(function(ret) {
                $(fieldtofill).val(ret);

            });
        }

        $('#LampType').click(function(){
            if($(this).is(':checked'))
                { $(this).val(1); }
            else
                { $(this).val(0); }
        });

        $('#IsActive').click(function(){
            if($(this).is(':checked'))
                { $(this).val(1); }
            else
                { $(this).val(0); }
        });

        inits();
        initialize();

        var channel = $("#ChannelName option:selected" ).val();

        $('#Asin').blur(function(){
            var Asinval= $('#Asin').val();

            var  request = $.ajax({
                url:"<?=base_url()?>index.php/tools/datamining/validateAsin",
                type: "POST",
                data:{av: Asinval}
            });
            request.done(function(ret) {
                if(ret != 0){
                    alert('Duplicated Asin')
                    initialize();
                }else{
                    var bValid = true;
                    if ($('#ChannelName').val() == "Buy"){
                        bValid = bValid && checkLength( Asin, "Asin", 9, 10 );
                    }
                    if ($('#ChannelName').val() == "Amazon"){
                        bValid = bValid && checkLength( Asin, "Asin", 10, 10 );
                    }

                    if ( !bValid ) {
                        $("#Asin").removeClass( "ui-state-error" );
                        initialize();
                        $( "#Asin" ).focus();
                    }else{
                        $( "#IsActive" ).removeAttr("disabled");
                        $( "#CountryCode" ).removeAttr("disabled");
                        $( "#Title" ).removeAttr("disabled");
                        $( "#Category" ).removeAttr("disabled");
                        $( "#manufacturerpn" ).removeAttr("disabled");
                        $( "#manufacturer" ).removeAttr("disabled");
                        $( "#BrandMentioned" ).removeAttr("disabled");
                        $( "#BrandSell" ).removeAttr("disabled");
                        $( "#LampType" ).removeAttr("disabled");
                        $( "#ProductCatalogId" ).removeAttr("disabled");
                        $( "#Comments" ).removeAttr("disabled");
                        $( "#Title" ).focus();
                    }
                }
            });

        });

        $( "#manufacturerpn" ).autocomplete({
            source: "<?=base_url()?>index.php/tools/datamining/fillManufacturerPN"
        });

        $( "#manufacturer" ).focus(function(){
            fillValue("<?=base_url()?>index.php/tools/datamining/getManufacturer",$("#manufacturerpn").val(), "#manufacturer" );
            $('#ModelNumber').focus();
        });

        $( "#BrandSell" ).autocomplete({
            source: "<?=base_url()?>index.php/tools/datamining/fillBrandSell"
        });

        $( "#ProductCatalogId" ).focus(function(){
            fillValue2("<?=base_url()?>index.php/tools/datamining/getMITSKU",$("#manufacturerpn").val(), $("#BrandSell").val(), "#ProductCatalogId" );
        });

        $( "#BrandMentioned" ).autocomplete({
            source: "<?=base_url()?>index.php/tools/datamining/getBrandMentioned"
        });

        $( "#dialog-form" ).dialog({
            autoOpen: false,
            height: 575,
            width: 550,
            modal: true,
            buttons: {
                "Create and Add": function() {
                    var bValid = true;
                    allFields.removeClass( "ui-state-error" );

                    if ($('#ChannelName').val() ==  "Buy"){
                        bValid = bValid && checkLength( Asin, "Asin", 9, 10 );
                    }
                    if ($('#ChannelName').val() ==  "Amazon"){
                        bValid = bValid && checkLength( Asin, "Asin", 10, 10 );
                    }

                    bValid = bValid && checkLength( Asin, "manufacturerpn", 1, 255 );
                    bValid = bValid && checkLength( manufacturerpn, "manufacturerpn", 1, 255 );
                    bValid = bValid && checkLength( manufacturer, "manufacturer", 1, 255 );
                    bValid = bValid && checkLength( BrandMentioned, "BrandMentioned", 1, 255 );

                    if ( bValid ) {

                        var request = $.ajax({
                            url:"<?=base_url()?>index.php/tools/datamining/saveRecord",
                            type: "POST",
                            data:allFields
                        });

                        request.done(function() {
                            alert('Record Added')

                        });
                        inits();
                        initialize();
                        $( "#Asin" ).focus();
                    }
                },
                Cancel: function() {
                    $( "#Asin" ).val('');
                    $( "#CountryCode" ).attr('disabled', 'disabled');
                    $( "#IsActive" ).attr('disabled', true);
                    $( "#Title" ).val('');
                    $( "#Title" ).attr('disabled', true);
                    $( "#ProductCatalogId" ).val('');
                    $( "#ProductCatalogId" ).attr('disabled', true);
                    $( "#manufacturerpn" ).val('');
                    $( "#manufacturerpn" ).attr('disabled', true).end().val('');
                    $( "#manufacturer" ).val('');
                    $( "#manufacturer" ).attr('disabled', true).end().val('');
                    $( "#LampType" ).attr('disabled', true);
                    $( "#BrandMentioned" ).val('');
                    $( "#BrandMentioned" ).attr('disabled', true).end().val('');
                    $( "#BrandSell" ).attr('disabled', true);
                    $( "#BrandSell" ).val('');
                    $( "#LampType").attr('disabled', true);
                    $( "#Comments" ).val('');
                    $( "#Comments" ).attr('disabled', true).end().val('');
                    $( this ).dialog( "close" );

                }
            },
            close: function() {
                $( "#Asin" ).val('');
                $( "#CountryCode" ).attr('disabled', 'disabled');
                $( "#IsActive" ).attr('disabled', true);
                $( "#Title" ).val('');
                $( "#Title" ).attr('disabled', true);
                $( "#ProductCatalogId" ).val('');
                $( "#ProductCatalogId" ).attr('disabled', true);
                $( "#manufacturerpn" ).val('');
                $( "#manufacturerpn" ).attr('disabled', true).end().val('');
                $( "#manufacturer" ).val('');
                $( "#manufacturer" ).attr('disabled', true).end().val('');
                $( "#LampType" ).attr('disabled', true);
                $( "#BrandMentioned" ).val('');
                $( "#BrandMentioned" ).attr('disabled', true).end().val('');
                $( "#BrandSell" ).attr('disabled', true);
                $( "#BrandSell" ).val('');
                $( "#LampType").attr('disabled', true);
                $( "#Comments" ).val('');
                $( "#Comments" ).attr('disabled', true).end().val('');
                $( this ).dialog( "close" );
            }
        });

        $( "#amazon" )
        .button()
        .click(function() {
            $( "#dialog-form" ).dialog( "open" );
        });

    });

    function inits()
    { $( "#Asin").val(''); }

    function initialize(){
        $( "#Asin" ).val('');
        $( "#CountryCode" ).attr('disabled', 'disabled');
        $( "#IsActive" ).attr('disabled', true);
        $( "#Title" ).val('');
        $( "#Title" ).attr('disabled', true);
        $( "#ProductCatalogId" ).val('');
        $( "#ProductCatalogId" ).attr('disabled', true);
        $( "#manufacturerpn" ).val('');
        $( "#manufacturerpn" ).attr('disabled', true).end().val('');
        $( "#manufacturer" ).val('');
        $( "#manufacturer" ).attr('disabled', true).end().val('');
        $( "#LampType" ).attr('disabled', true);
        $( "#BrandMentioned" ).val('');
        $( "#BrandMentioned" ).attr('disabled', true).end().val('');
        $( "#BrandSell" ).attr('disabled', true);
        $( "#BrandSell" ).val('');
        $( "#LampType").attr('disabled', true);
        $( "#Comments" ).val('');
        $( "#Comments" ).attr('disabled', true).end().val('');
    }

    function resize_the_grid()
    { $("#list").fluidGrid({base:'#grid_wrapper', offset:-20}); }

    $(document).ready(function(){

        var myGrid = $("#list");

        myGrid.jqGrid({
            url:'getData',
            postData: {
                ds:   function() { return jQuery("#search").val(); },
                ch:   function() { return jQuery("#channel").val(); },
                ca:   function() { return jQuery("#categoria option:selected").val(); },
                co:   function() { return jQuery("#country option:selected").val(); },
            },
            datatype: "json",
            colNames:<?=$colNames?>,
            colModel:<?=$colModel?>,
            autowidth: true,
            pager: jQuery('#pager'),
            rowNum: 50,
            sortname: 'id',
            sortorder: "asc",
            rowList:[50,300,500,1000],
            loadComplete: function() { $("option[value=100000000]").text('ALL');},
            rownumbers: true,
            viewrecords: true,
            caption: "<?=$caption?>",
            height: 600,
            multiselect: true,
            toppager:true,
            loadonce:false,
            cellEdit: false,
            editurl:"saveDefault",
            shrinkToFit: false,
        });

        jQuery("#list").jqGrid('navGrid','#pager',{cloneToTop:true,edit:true,add:false,del:false,search:false,refresh:true},{
            beforeShowForm: function(form) { $('#tr_Adjustment_Id', form).hide(); },
              reloadAfterSubmit:false,
              closeAfterEdit: true,
              closeOnEscape:true,
              recreateForm: true,
              width:500,

        });

        myGrid.jqGrid('navButtonAdd', '#' + myGrid[0].id + '_toppager_left',{ // "#list_toppager_left"
            caption: "Excel",
            buttonicon: 'myicon',
            onClickButton: function(){
                exportExcel(myGrid);
            }
        });

        myGrid.jqGrid('navButtonAdd', '#' + myGrid[0].id + '_toppager_left', {  // "#list_toppager_left"
            id: "Order_"+ myGrid[0].id +"_top",
            title:"Add Order",
            caption: "Add Order",
            buttonicon: 'order',
            onClickButton: function()
            {
                var order = jQuery("#list").jqGrid('getGridParam','selarrrow');
                if( order !== null )
                {
                    $( "#dialog4-confirm" )
                    .dialog( "open" );
                }
                else alert("Please Select Row");
            }
        });

        myGrid.jqGrid('navButtonAdd', '#' + myGrid[0].id + '_toppager_left', {  // "#list_toppager_left"
            id: "Del_"+ myGrid[0].id +"_top",
            title:"Del Asin",
            caption: "Del Asin",
            buttonicon: 'del',
            onClickButton: function()
            {
                var order = jQuery("#list").jqGrid('getGridParam','selarrrow');
                if( order !== null )
                {
                    $( "#dialog5-confirm" )
                    .dialog( "open" );
                }
                else alert("Please Select Row");
            }
        });

         myGrid.jqGrid('setFrozenColumns');

        // var totalRows= jQuery('#list').jqGrid('getGridParam','records');
        // $("#totals").append('<h1>'+totalRows+'<h1>');
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
            $( "#channel" ).on('change',function(){
               myReload();
            });

            //Recargar el grid en el evento onChange del select search del formulario
            $( "#categoria" ).on('change',function(){
               myReload();
            });

            $( "#country" ).on('change',function(){
               myReload();
            });
    });

    $(function()
    {
        $( "#dialog4-confirm" ).dialog({
            autoOpen: false,
            resizable: false,
            height:160,
            modal: true,
            buttons:
            {
                "Add Order": function()
                {
                    var order = jQuery("#list").jqGrid('getGridParam','selarrrow');

                    var skus = [];
                    var asins = [];
                    var qtys = [];
                    for (var i=0, il=order.length; i < il; i++)
                    {
                        var sku = jQuery("#list").jqGrid('getCell', order[i], 'DartFBASKU');
                        skus.push(sku);

                        var asin = jQuery("#list").jqGrid('getCell', order[i], 'ASIN');
                        asins.push(asin);

                        var qty = jQuery("#list").jqGrid('getCell', order[i], 'NeedToSend');
                        qtys.push(qty);

                        var request = $.ajax({
                            url:"saveOrder",
                            type: "POST",
                            data:{  oams: JSON.stringify(skus[i]),
                                    oaasin: JSON.stringify(asins[i]),
                                    oqty: JSON.stringify(qtys[i])},
                            dataType: "json"
                            });
                    }

                    request.done(function() {
                        alert('Orders Added')
                        jQuery("#list").trigger("reloadGrid");
                    });

                    $( this ).dialog( "close" );
                },
                Cancel: function()
                { $( this ).dialog( "close" ); }
            }
        });
    });

    $(function()
    {
        $( "#dialog5-confirm" ).dialog({
            autoOpen: false,
            resizable: false,
            height:160,
            modal: true,
            buttons:
            {
                "Delete Asin": function()
                {
                    var order = jQuery("#list").jqGrid('getGridParam','selarrrow');

                    var asins = [];

                    for (var i=0, il=order.length; i < il; i++)
                    {
                        var asin = jQuery("#list").jqGrid('getCell', order[i], 'ASIN');
                        asins.push(asin);

                        var request = $.ajax({
                            url:"deleteAsin",
                            type: "POST",
                            data:{  oaasin: JSON.stringify(asins[i])},
                            dataType: "json"
                            });
                    }

                    request.done(function() {
                        alert('Asin Deleted')
                        jQuery("#list").trigger("reloadGrid");
                    });

                    $( this ).dialog( "close" );
                },
                Cancel: function()
                { $( this ).dialog( "close" ); }
            }
        });
    });

    $(window).resize(resize_the_grid);

    function yesno(cellvalue, options,rowObject)
    {
        if (cellvalue==0)
            { return 'No'; }
        else
            { return 'Yes'; }
    }

</script>

<div id="dialog-form" title="AddASIN">
    <fieldset class="bluegradiant">
        <ul >
            <li>
                <div style=" float:left;margin-right: 20px;">
                        <label>Select Channel:    </label>
                        <div>
                            <select id="ChannelName" name="ChannelName">
                                <option value="Amazon" selected="selected" >Amazon</option>
                                <option value="Buy" >Buy</option>
                                <option value="NewEgg" >NewEgg</option>
                                <option value="Sears" >Sears</option>
                                <option value="Barnes & Nobel" >Barnes & Nobel</option>
                            </select>
                        </div>
                    </div>
                    <div style=" float:left;margin-right: 20px;">
                        <label>Category:</label>
                        <div>
                            <select id="Category" name="Category">
                                <option value="FP LAMP" selected="selected">FP LAMP</option>
                                <option value="RPTV LAMP">RPTV LAMP</option>
                                <option value="TOYS">TOYS</option>
                                <option value="REMOTE CONTROLS">REMOTE CONTROLS</option>
                                <option value="TV ACCESSORIES">TV ACCESSORIES</option>
				<option value="STAGE LAMPS">STAGE LAMPS</option>
				<option value="DJ LAMPS">DJ LAMPS</option>
 				<option value="THEATER LAMPS">THEATER LAMPS</option>
 				<option value="FILM LAMPS">FILM LAMPS</option>
                                <option value="ARCHITAINMENT LAMPS">ARCHITAINMENT LAMPS</option>
                                <option value="DISINFECTION LAMPS">DISINFECTION LAMPS</option>
                                <option value="PROJECTION LAMPS">PROJECTION LAMPS</option>
				<option value="REPROGRAPHY LAMPS">REPROGRAPHY LAMPS</option>
                                <option value="INSECT TRAP LAMPS">INSECT TRAP LAMPS</option>
                            </select>
                        </div>
                    </div>
            </li>

            <br><br><br>

            <li>
                <div>
                    <div style=" float:left;margin-right: 20px;">
                        <label>Channel Unique ID (ASIN/SKU):    </label>
                        <div>
                            <input id="Asin" name="Asin" type="text" size="25" maxlength="255" value=""/>
                        </div>
                    </div>
                    <div style=" float:left;margin-right: 20px;">
                        <label>Country:</label>
                        <div>
                           <select id="CountryCode" name="CountryCode">
                                <option value="US" selected="selected">US</option>
                                <option value="CA">CA</option>
                                <option value="DE">DE</option>
                                <option value="ES">ES</option>
                                <option value="FR">FR</option>
                                <option value="ID">ID</option>
                                <option value="IT">IT</option>
                                <option value="SP">SP</option>
                                <option value="UK">UK</option>
				<option value="MX">MX</option>
                            </select>
                        </div>
                    </div>
                    <div style=" float:left;margin-right: 20px;">
                        <label>Active:</label>
                        <div>
                            <input id="IsActive" name="IsActive"  type="checkbox" value="1" checked/>
                            <label> Is Active?</label>
                        </div>
                    </div>
                </div>
            </li>

            <br><br><br><br>

            <li>
                <label>Listing Title:</label>
                <div>
                    <textarea id="Title" name="Title" rows="2" cols="60" ></textarea>
                </div>
            </li>

            <br>

            <li>
                <label>Manufacture Part Number: </label>
                <div>
                    <input id="manufacturerpn" name="manufacturerpn"  type="text" maxlength="255" value=""/>
                </div>
            </li>

             <br>

            <li>
                <label>Manufacturer:</label>
                <div>
                    <input id="manufacturer" name="manufacturer"  type="text"  maxlength="255" value=""/>
                </div>
            </li>

            <br>

            <li>
                <label>Brand Mentioned:</label>
                <div>
                    <input id="BrandMentioned" name="BrandMentioned"  type="text"  maxlength="255" value=""/>
                </div>
            </li>

            <br>

            <li>
                <label>Brand Sell:</label>
                <div>
                    <select id="BrandSell" name="BrandSell">
                        <option value="PHILIPS" >PHILIPS</option>
                        <option value="OSRAM" >OSRAM</option>
                        <option value="PHOENIX" >PHOENIX</option>
                        <option value="OEM" >OEM</option>
                        <option value="NEOLUX">NEOLUX</option>
                        <option value="USHIO">USHIO</option>
                        <option value="COMPATIBLE">COMPATIBLE</option>
                        <option value="LUTEMA">LUTEMA</option>
                        <option value="AURABEAM">AURABEAM</option>
                    </select>
                </div>
            </li>

            <br>

            <li>
                <label>Enclosure:</label>
                <div>
                    <input id="LampType" name="LampType" type="checkbox" value="1" checked />
                    <label>Includes Enclosure?</label>
                </div>
            </li>

            <br>

            <li>
                <label>MITSKU:</label>
                <div>
                    <input id="ProductCatalogId" name="ProductCatalogId"  type="text" maxlength="255" value=""/>
                </div>
            </li>

            <br>

            <li>
                <label>Comments:</label>
                <div>
                    <textarea id="Comments" name="Comments" rows="2" cols="60" ></textarea>
                </div>
            </li>
        </ul>

    </fieldset>
</div>

<div id="dialog4-confirm" title="Add New Order?">
    <p><span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 20px 0;"></span>This will add a new order. Are you sure?</p>
</div>

<div id="dialog5-confirm" title="Delete ASIN?">
    <p><span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 20px 0;"></span>This ASIN will be permanentry delete and cannot be recovered. Are you sure?</p>
</div>
