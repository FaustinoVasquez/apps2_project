<style type="text/css" media="screen">

</style>

<div class="clear"></div>

<section id="grid_container">
    <table id="list"></table>
    <div id="pager"></div> 
</section>

<form method="post" action="<?= base_url() . 'index.php' . $from . 'csvExport' ?>">
    <input type="hidden" name="csvBuffer" id="csvBuffer" value="" />
</form>  

<div id="dialog-modal" title="Details" style="display: none;"></div>


<script type="text/javascript">
    
    function resize_the_grid()
    {
        $("#list").fluidGrid({base:'#grid_wrapper', offset:-20});
    }
        
    $(document).ready(function(){

        var myGrid = $("#list");

        myGrid.jqGrid({
            url:'getData',
            datatype: "json",
            postData:{ sku: <?= $sku ?> },
            colNames:<?= $colNames ?>,
            colModel:<?= $colModel ?>,
            pager: jQuery('#pager'),
            rowNum: 50,
            sortname: 'asc',
            rowList:[300,500,100000000],
            loadComplete: function() { $("option[value=100000000]").text('ALL');},
            rownumbers: true,
            viewrecords: true,
            caption: "<?= $caption ?>",
            height: 600, 
            toppager:true,      
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

    });
    
    $(window).resize(resize_the_grid);

</script>

