<style type="text/css" media="screen">
    .ui-jqgrid tr.jqgrow td {
        white-space: normal !important;
        height:auto;
        vertical-align:text-top;
        padding-top:3px;
    }
    .ui-jqgrid .ui-jqgrid-htable th div {
        height:auto;
        overflow:hidden;
        padding-right:4px;
        padding-top:3px;
        position:relative;
        vertical-align:text-top;
        white-space:normal !important;
    }
</style>
<div style="height: 355px;width: 810px;margin-left: auto ;  margin-right: auto ;margin-top: 20px">

    <span style ="float:left">
        <table id="list10"></table>
        <div id="pager10"></div>
    </span>
    <span style ="float:right">
        <table id="list10_d"></table>
        <div id="pager10_d"></div>
    </span>
    <div style ="float:left; margin-top:5px;">
        <table id="list11_d"></table>
        <div id="pager11_d"></div>
    </div>
    <div style ="float:right; margin-top:5px;">
        <table id="list12_d"></table>
        <div id="pager12_d"></div>
    </div>
</div>


<script type="text/javascript">

    $(document).ready(function(){

        var list10 = $("#list10");
        var list10_d = $("#list10_d");
        var list11_d = $("#list11_d");
        var list12_d = $("#list12_d");

        list10.jqGrid({
            url:'<?= base_url() . 'index.php/' . $from . 'CompatData?q=' . $search ?>',
            datatype: "json",
            height: 100,
            width: 400,
            datatype: "json",
            colNames:<?= $headers ?>,
            colModel:<?= $body ?>,
            pager: '#pager10',
            rowNum: 150,
            rownumbers: true,
            sortname: 'id',
            sortorder: 'desc',
            multiselect: false,
            viewrecords: true,
            caption: "<?= $caption . ' SKU ' . $search ?>",
            onSelectRow: function(ids) {
                if(ids != null) {
                    var ret = jQuery(list10).jqGrid('getRowData', ids);
                    jQuery(list10_d).jqGrid('setGridParam',{url:"<?= base_url() . 'index.php/' . $from . '/compatibilityDetails?q=1&id=' ?>"+ids+"&mf="+ret.Manufacturer+"&pn="+ret.PartNumber+"&page="+1});
                    jQuery(list10_d).jqGrid('setCaption',"Compatibility Details: "+ret.PartNumber).trigger('reloadGrid');

                    jQuery(list11_d).jqGrid('setGridParam',{url:"<?= base_url() . 'index.php/' . $from . '/compatibilityDetails?q=2&id=' ?>"+ids+"&mf="+ret.Manufacturer+"&pn="+ret.PartNumber+"&page="+1});
                    jQuery(list11_d).jqGrid('setCaption',"Alternative PN: "+ret.PartNumber).trigger('reloadGrid');

                    jQuery(list12_d).jqGrid('setGridParam',{url:'<?= base_url() . 'index.php/' . $from . '/compatibilityDetails?q=' ?>3&id=0'});
                    jQuery(list12_d).jqGrid('setCaption',"Comp Alternative Model: ").trigger('reloadGrid');
                }
            }
        });
    
        jQuery(list10).jqGrid('navGrid','#pager10',{edit:false,add:false,del:false,search:false,view:false});
        jQuery(list10).jqGrid("navButtonAdd","#pager10",{caption:'C-F',title:'My View Title', onClickButton:function (event, data) {rowview();}})
    
  
   


        function rowview(event, data){
            var gr = jQuery(list10).getGridParam("selrow");
            if (gr != null){
                $(list10).jqGrid('viewGridRow', gr, {jaModal: true,
                    caption:'Custom Fields<?= ' SKU ' . $search ?>'
                });
            }
            else
                alert("Select row to view");
        }


        /*
         *
         *
         * Grid CompatibilityDetails    CASE ## 1 ##
         *
         */
     

        
        jQuery(list10_d).jqGrid({
            url:'<?= base_url() . 'index.php/' . $from . 'compatibilityDetails?q=' ?>1&id=0',
            datatype: "json",
            colNames:['id','Model', 'comments','OriginalManufacturer','ApprovedBy'],
            colModel:[ {name:'id',index:'id', width:40, align:"center",hidden:true},
                {name:'Model',index:'Model', width:90, align:"left"},
                {name:'Comments',index:'Comments', width:80, align:"left",hidden:true, editrules : {edithidden:true}},
                {name:'OriginalManufacturer',index:'OriginalManufacturer', width:80, hidden:true},
                {name:'ApprovedBy',index:'ApprovedBy', width:80,align:"left", sortable:false, search:false}
            ],
            pager: '#pager10_d',
            rowNum: 150,
            sortname: 'id',
            sortorder: 'asc',
            viewrecords: true,
            rownumbers: true,
            height: "100",
            width: 400,
            caption: "Compatibility Details: ",
            onSelectRow: function(ids) {
                if(ids == null) {
                    ids=0;
                    if(jQuery(list10_d).jqGrid('getGridParam','records') >0 ) {
                        jQuery(list11_d).jqGrid('setGridParam',{url:"<?= base_url() . 'index.php/' . $from . 'compatibilityDetails?q=3&id=' ?>"+ids,page:1});
                        jQuery(list11_d).jqGrid('setCaption',"Invoice Detail: "+ids) .trigger('reloadGrid');
                    }
                } else {
                    var ret = jQuery(list10_d).jqGrid('getRowData', ids);
                    jQuery(list12_d).jqGrid('setGridParam',{url:"<?= base_url() . 'index.php/' . $from . 'compatibilityDetails?q=3&id=' ?>"+ids+"&oma="+ret.OriginalManufacturer+"&mo="+ret.Model+"&page="+1});
                    jQuery(list12_d).jqGrid('setCaption',"Comp Alternative Model: "+ret.Model).trigger('reloadGrid');
                }
            }
        });
    

        /*
         *
         *
         * Grid CompatibilityAlternativePN CASE ## 2 ##
         *
         */


        jQuery(list11_d).jqGrid({
            url:'<?= base_url() . 'index.php/' . $from . '/compatibilityDetails?q=' ?>2&id=0',
            datatype: "json",
            colNames:['id','AlternativePN', 'comments','AddedBy', 'ApprovedBy'],
            colModel:[ {name:'id',index:'id', width:40, align:"center",hidden:true},
                {name:'AlternativePN',index:'AlternativePN', width:90, align:"left"},
                {name:'Comments',index:'Comments', width:150, align:"center",hidden:true, editrules : {edithidden:true}},
                {name:'AddedBy',index:'AddedBy', width:80,align:"left", sortable:false, search:false},
                {name:'ApprovedBy',index:'ApprovedBy', width:80,align:"left", sortable:false, search:false,hidden:true}
            ],
            pager: '#pager11_d',
            rowNum: 150,
            sortname: 'id',
            sortorder: 'asc',
            viewrecords: true,
            rownumbers: true,
            height: "100",
            width: 400,
            caption: "Alternative PN"
        });
  


        /*
         *
         *
         * Grid CompatibilityAlternativeModels CASE ## 3 ##
         *
         */

        jQuery(list12_d).jqGrid({
            url:'<?= base_url() . 'index.php/' . $from . '/compatibilityDetails?q=' ?>3id=0',
            datatype: "json",
            colNames:['id','AlternativeModel', 'Comments','ApprovedBy'],
            colModel:[ {name:'id',index:'id', width:40, align:"center",hidden:true},
                {name:'AlternativeModel',index:'AlternativeModel', width:90, align:"left"},
                {name:'Comments',index:'Comments', width:150, align:"center",hidden:true, editrules : {edithidden:true}},
                {name:'ApprovedBy',index:'ApprovedBy', width:80,align:"left", sortable:false, search:false}
            ],
            pager: '#pager12_d',
            rowNum: 150,
            sortname: 'id',
            sortorder: 'asc',
            viewrecords: true,
            rownumbers: true,
            height: "100",
            width: 400,
            caption: "Comp Alternative Model"
        });
        
        
        
    });


 

</script>
