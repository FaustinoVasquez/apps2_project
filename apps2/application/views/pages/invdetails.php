<br>
<div class="clear"></div>


<section id="grid_container">
    <table id="list"></table>
    <div id="pager"></div>
</section>


<script type="text/javascript">
    
      function resize_the_grid()
        {
            $("#list").fluidGrid({base:'#grid_wrapper', offset:-20});
        }
        
    $(document).ready(function(){

        function currencyFmatter (cellvalue, options, rowObject)
        {
            // do something here
            return "<b>"+cellvalue+"</b>";
        }

        var myGrid = $("#list");

        myGrid.jqGrid({
            url:'<?= base_url() . 'index.php' . $from . 'gridDataInvDetails?q=' . $search ?>',
            datatype: "json",
            colNames:<?= $headers ?>,
            colModel:<?= $body ?>,
            height:250,
            autowidth: true,
            pager: '#pager',
            rowNum: 50,
            rowList: [50,100,250],
            rownumbers: true,
            viewrecords: true,
           
            caption: "<?= $caption ?>"
        });
 
        jQuery("#list").jqGrid('navGrid','#pager',{edit:false,add:false,del:false,search:false,refresh:true});
         resize_the_grid();
    });
    
    $(window).resize(resize_the_grid);


</script>
