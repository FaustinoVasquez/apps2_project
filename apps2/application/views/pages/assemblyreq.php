<br/>
<div class="clear"></div>

<section id="grid_container">
    <table id="list20"></table>
    <div id="pager20"></div>
</section>




<script type="text/javascript">

    function resize_the_grid()
    {
        $("#list20").fluidGrid({base:'#grid_wrapper', offset:-20});
    }

    $(document).ready(function(){

        function yesnoFmatter (cellvalue, options, rowObject)
        {
            // do something here
            if (cellvalue==1)
                return "Yes";
            else
                return "No";
                
        }


        var myGrid = $("#list20");

        myGrid.jqGrid({
            url:'<?= base_url() . 'index.php' . $from . 'gridAssembleRequirements?q=' . $search ?>',
            datatype: "json",
            height:250,
            colNames:<?= $headers ?>,
            colModel:<?= $body ?>,
            pager: '#pager20',
            rowNum: 50,
            rowList: [50,100,250],
            rownumbers: true,
            sortorder: 'desc',
            viewrecords: true,
            caption: "<?= $caption ?>"
        });
        jQuery("#list20").jqGrid('navGrid','#pager20',{edit:false,add:false,del:false,search:false,refresh:true});
        
        resize_the_grid();
    });

    function popup(cellvalue, options,rowData){
        if (cellvalue!=null){
            var a = rowData[0];
            return "<A HREF=\"javascript:popUp("+a+")\"><span class=\"ui-icon ui-icon-newwin\" style=\"display:inline-block;\"></span></A>";
        }else{
            return '';
        }
    }

    function popUp(URL) {
        var day = new Date();
        var id = day.getTime();
        eval("page" + id + " = window.open('<?= base_url() .'index.php/Tabs/costinfo/'?>"+URL+"', '" + id + "', 'toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=0,width=1000,height=400,left = 200,top = 325');");
    }


    $(window).resize(resize_the_grid);

</script>

