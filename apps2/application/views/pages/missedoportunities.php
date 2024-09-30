

<?php
    $formopen = array('id' => "myform", 'class' => 'myform');
    $search  = array('name' => 'search', 'id'=>'search','value' => '', 'autocomplete' => 'on', 'maxlength' => '250', 'size' => '20', 'onclick' => "this.value=''");
    $upload = array('name' => 'userfile', 'value' => 'Choose File', 'type' => 'file');
    $submit = array('name' => 'send', 'value' => 'Upload', 'type' => 'submit', 'class' => 'button');
?>


<?php
echo form_open_multipart($from.'do_upload',$formopen);
?>
<fieldset class="bluegradiant">
        <div class="logo"><img longdesc="MI Technologies" title="Mi Tech" alt="43" src="<?= base_url() ?>/images/header/mitechnologies.png"></div>
        <div class="form-data"> 
            <?php
                echo "<label>Search : </label>" ;
                echo form_input($search).'<br>';
                echo "<label>Upload File: </label>" ;
                echo form_input($upload).'&nbsp;&nbsp;';
                echo form_input($submit);
            ?>
        </div>
       <div>
       <a href="http://192.168.0.221/mnt/systemimports/templates/MissedOpportunitesTemplate.csv">MissedOportunities Template CSV (click)</a>
       <a href="http://192.168.0.221/mnt/systemimports/templates/MissedOpportunitesTemplate.xlsx">MissedOportunities Template XLSX (click)</a>
    </fieldset>
<br>
<?php
echo form_close();
?>


<div class="clear"></div>

<section id="grid_container">
    <table id="grid"></table>
    <div id="pager"></div>
</section>

<form method="post" action="<?= base_url() . 'index.php' . $from . 'csvExport' ?>">
    <input type="hidden" name="csvBuffer" id="csvBuffer" value="" />
</form>

<script type="text/javascript">

    function resize_the_grid()
    {
        $("#grid").fluidGrid({base:'#grid_wrapper', offset:-20});
    }
    $(function(){

        var myGrid = $("#grid");

        myGrid.jqGrid({
            url:'getData',
            postData: {
                ds:   function() { return jQuery("#search").val(); },
            },
            datatype: "json",
            colNames:<?= $colNames ?>,
            colModel:<?= $colModel ?>,
            pager: jQuery('#pager'),
            rowNum: 50,
            sortname: 'SKU',
            sortorder: "asc",
            rowList:[50,300,500,100000000],
            loadComplete: function() { $("option[value=100000000]").text('ALL');},
            rownumbers: true,
            viewrecords: true,
            caption: "<?=$caption?>",
            height: 600,
            toppager:true,
        });

        myGrid.jqGrid('navGrid','#pager',{cloneToTop:true,edit:false,add:false,del:false,search:false,refresh:false});
        add_top_bar(myGrid);
        resize_the_grid();


        var myReload = function() {
            myGrid.setGridParam({ page: 1, datatype: "json" }).trigger('reloadGrid');
        };
        // Recargar el grid en el evento submit del formulario
        $( 'form' ).on( 'submit', function( e ){
            e.preventDefault();
            var request = $.ajax({
                url: "do_upload",
                type: "POST",
                data:  new FormData(this),
                processData: false,
                contentType: false
            });

            request.done(function( msg ) {
            $('#myform')[0].reset();
             myReload();
            });
            

        });

        //Recargar el grid en el evento onChange del select search del formulario
        $( "#search" ).on('change',function(){
            myReload();
        });

    });


    $(window).resize(resize_the_grid);
    function add_top_bar(grid){

        jQuery(grid).jqGrid('navButtonAdd', '#' + jQuery(grid)[0].id + '_toppager_left', { // "#list_toppager_left"
            caption: "Excel",
            buttonicon: 'ui-icon-document',
            onClickButton: function(){
                exportExcel(grid,'MRP_Report');
            }
        });
    }


    function linksku(cellvalue,options,rowData){
        if (cellvalue!=null){
            return '<a id="t'+cellvalue+'" href="#"  onclick="showDialog('+cellvalue+',\'<?=base_url()?>\');">' + cellvalue + '</a>';
        }else{
            return '';
        }
    }



    function checkboxFormatter(cellvalue, options, rowObject) {
        cellvalue = cellvalue + "";
        cellvalue = cellvalue.toLowerCase();
        var bchk = cellvalue.search(/(false|0|no|off|n)/i) < 0 ? " checked=\"checked\"" : "";
        return "<input type='checkbox' onclick=\"ajaxSave('" + options.rowId + "', this,'"+grid+"');\" " + bchk + " value='" + cellvalue + "' name='"+options.colModel.name+"' id='"+options.colModel.name+options.rowId+"' offval='no' />";
    }

    function ajaxSave(rowid, curCheckbox) {
        var field = curCheckbox.name;
        var value = curCheckbox.checked;
        var request = $.ajax({
            url: "editHousing",
            type: "POST",
            data: { id : rowid,
                checkbox: field,
                value: value
            },
        });

        request.done(function( msg ) {
            jQuery("#grid").trigger('reloadGrid');
        });
    }


</script>

