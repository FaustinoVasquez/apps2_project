
<style type = "text/css" media = "screen">


    .ui-icon.myicon {
        width: 16px;
        height: 16px;
        background-image:url('<?= base_url() ?>images/toolbar/Excel-icon2.png')
    }


</style>
<?php
$formopen = array('id' => 'target', 'id' => "omc_form", 'class' => 'myform');
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
	    
	    
<style type="text/css" media="screen">
    .ui-icon.excel {
        width: 16px;
        height: 16px;
        background-image:url('<?= base_url() ?>images/toolbar/Excel-icon2.png');
    }
</style>
    
    
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
    


    var subgrid = function (id,grid,pager,field1,field2,field3,field4,gridname,ID,saveurl,gridurl){


        switch(id) {
        case 1:
            var colnames = ['ID','DM_Youtube','DM_Vimeo','DM_Facebook'];
            var colmodel = [{name:field1,index:field1, width:80, align:'left', hidden:true},
                            {name:field2,index:field2, width:250, align:'left', editable:true},
                            {name:field3,index:field3, width:250, align:'center', editable:true},
                            {name:field4,index:field4, width:250, align:'left', editable:true}];

        break;

        case 2:
                var colnames = ['ID','DTVL_Youtube','DTVL_Vimeo','DTVL_Facebook'];
                var colmodel = [{name:field1,index:field1, width:80, align:'left', hidden:true},
                                {name:field2,index:field2, width:250, align:'left', editable:true},
                                {name:field3,index:field3, width:250, align:'center', editable:true},
                                {name:field4,index:field4, width:250, align:'left', editable:true}];
        break;

        case 3:
            var colnames = ['ID','PriceEncSKU','PriceGeneric','PricePremium'];
            var colmodel = [{name:field1,index:field1, width:80, align:'left', hidden:true},
                            {name:field2,index:field2, width:150, align:'left', editable:true,editrules:{number: true}},
                            {name:field3,index:field3, width:150, align:'left', editable:true,editrules:{number: true}},
                            {name:field4,index:field4, width:150, align:'left', editable:true,editrules:{number: true}}];
        break;

        case 4:
            var colnames = ['ID','UPCEconomy','UPCPremium','UPCPhilips'];
            var colmodel = [{name:field1,index:field1, width:80, align:'left', hidden:true},
                            {name:field2,index:field2, width:150, align:'left', editable:true,editrules:{number: true}},
                            {name:field3,index:field3, width:150, align:'left', editable:true,editrules:{number: true}},
                            {name:field4,index:field4, width:150, align:'left', editable:true,editrules:{number: true}}];
        break;

        case 5:
            var colnames = ['ID','Watt','OriginalLampType'];
            var colmodel = [{name:field1,index:field1, width:80, align:'left', hidden:true},
                            {name:field2,index:field2, width:250, align:'center', editable:true},
                            {name:field3,index:field3, width:250, align:'center', editable:true}];
        break;

        case 6:
            var colnames = ['ID','Notes','CompatibiltiesNotes'];
            var colmodel = [{name:field1,index:field1, width:80, align:'left', hidden:true},
                            {name:field2,index:field2, width:250, align:'center', editable:true},
                            {name:field3,index:field3, width:250, align:'center', editable:true}];
        break;

        } 

                 jQuery("#"+grid).jqGrid({
                        url:gridurl,
                        postData: { ds: ID},
                        datatype: "json",
                        colNames:colnames,
                        colModel:colmodel,
                        pager: "#"+pager,
                        rownumbers: true,
                        sortname: "ID",
                        viewrecords: true,
                        sortorder: "asc",
                        caption: gridname,
                        mtype: "GET",
                        cellEdit: true,
                        cellurl:saveurl,
                    });

                    jQuery('#'+grid).navGrid('#'+pager, {edit: false, add: false, del: false});
                    jQuery('#'+grid).trigger('reloadGrid');
    };

    $(document).ready(function(){
	
	
	
        var myGrid = $("#list");
	
        myGrid.jqGrid({
            url:'<?= base_url() . 'index.php' . $from . $gridSearch ?>',
            datatype: "json",
            colNames:<?= $colNames ?>,
            colModel:<?= $colModel ?>,
            autowidth: true,
            pager: jQuery('#pager'),
            rowNum: 50,
            sortname: 'ID',
            rowList:[50,300,100000000],
            loadComplete: function() { $("option[value=100000000]").text('ALL');},
            rownumbers: true,
            viewrecords: true,
            caption: "<?= $caption ?>",
            height: 600, 
            toppager:true,
            cellEdit: true,
	        cellurl:'saveMainData',
            subGrid: true,
            editurl: 'saveData',
            subGridOptions: { "expandOnLoad": false, "reloadOnExpand" : false },
            subGridRowExpanded: function(subgrid_id, row_id) {    
                var rData = jQuery("#list").getRowData(row_id);
                var colData = rData['ID'];

                var request = $.ajax({
                url: "getTabs",
                type: "GET",
                data: { id : colData },
                dataType: "html"
                });
                request.done(function( msg ) {
                    $("#"+subgrid_id).html("<div>"+msg+"</div>"); 
                    $( ".tabs" ).tabs()
               
                subgrid(1,'grid1'+colData,'pager1'+colData,'ID','DM_Youtube','DM_Vimeo','DM_Facebook','DM',colData,'saveTab/1','tab/1');
                subgrid(2,'grid2'+colData,'pager2'+colData,'ID','DTVL_Youtube','DTVL_Vimeo','DTVL_Facebook','DTVL',colData,'saveTab/2','tab/2');
                subgrid(3,'grid3'+colData,'pager3'+colData,'ID','PriceEncSKU','PriceGeneric','PricePremium','Pricing',colData,'saveTab/3','tab/3');
                subgrid(4,'grid4'+colData,'pager4'+colData,'ID','UPCEconomy','UPCPremium','UPCPhilips','UPC',colData,'saveTab/4','tab/4');
                subgrid(5,'grid5'+colData,'pager5'+colData,'ID','Watt','OriginalLampType','','Specs',colData,'saveTab/5','tab/5');
                subgrid(6,'grid6'+colData,'pager6'+colData,'ID','Notes','CompatibiltiesNotes','','Notes',colData,'saveTab/6','tab/6');
    
                });

            },
        });


	resize_the_grid();
        add_top_bar(myGrid);
    });
    
    $(window).resize(resize_the_grid);
    
    
    function add_top_bar(grid){
        jQuery(grid).jqGrid('navGrid','#projectordataPager',{cloneToTop:true, edit: false, add: true, del:true, search: false, refresh:false}, 
            {},
            {
                closeAfterAdd: true,
                recreateForm: true,
                width:700,
            },
            {
                url: 'DeleteRow'
            });

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


