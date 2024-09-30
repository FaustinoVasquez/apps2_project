<style type="text/css">
    .ui-autocomplete-loading { background: white url('images/ui-anim_basic_16x16.gif') right center no-repeat; }
    .ui-icon.myicon {
        width: 16px;
        height: 16px;
        background-image:url('http://apps2.mitechnologiesinc.com/images/toolbar/Excel-icon2.png');
    }

</style>

<?php
$formopen = array('id' => "configurator_form" , 'class'=>'myform');
echo form_open(base_url() . 'index.php' . $from, $formopen);
?>
<fieldset class="bluegradiant">
    <div class="logo"><img longdesc='MI Technologies' title='Mi Tech' alt='43' src='<?= base_url() ?>/images/header/mitechnologies.png'>
    </div>
    <div class="form-data"> 
        <?php
        
        $inputtext = array('name' => 'search', 'value' => $search, 'autocomplete' => 'on', 'maxlength' => '250','style'=> 'width:57%;height:20px; margin-top:25px;font-size:16px;');
        echo "Search:" . form_input($inputtext);
      
      
        echo "&nbsp;&nbsp;";
        $submit = array('name' => 'send', 'value' => 'Submit', 'type' => 'submit', 'class' => 'button','style'=> 'height:25px;');
        echo form_input($submit);
        $reset = array('name' => 'reset', 'value' => 'Reset', 'type' => 'submit', 'class' => 'button','style'=> 'height:25px;');
        echo form_input($reset);
        ?>
    </div>
</fieldset>
<br>
<?php
echo form_close();

?>


<div class="clear"></div>

<section id="grid_container">
<table id="list11"></table>
<div id="pager11" ></div>
</section>


<form method="post" action="<?= base_url() . 'index.php/admin' . $from . '/csvExport' ?>">
    <input type="hidden" name="csvBuffer" id="csvBuffer" value="" />
</form>


<script type="text/javascript">
    
    
  function resize_the_grid()
    {
        $("#list11").fluidGrid({base:'#grid_wrapper', offset:-20});
    }

  var mydata= <?= $mydata ?>;

    
      $(document).ready(function(){

        var myGrid = $("#list11");

        myGrid.jqGrid({
 
      //  url:'<?= base_url() . 'index.php' . $from . 'gridDataConfigurator/' . $search ?>',
        data: mydata,
        datatype: "local",
        height: 600, 
        colNames:<?= $headers ?>,
        colModel:<?= $body ?>,
        autowidth: true, 
        pager: '#pager11',
        rowNum: 300,
        rowList: [300,500,1000],
        rownumbers: true,
        sortname: '',
        sortorder: 'asc',
        viewrecords: true,
        caption: "<?= $caption ?>",
        toppager:true,
        subGrid: true,
        subGrisubGridOptions: { "plusicon" : "ui-icon-triangle-1-e", "minusicon" : "ui-icon-triangle-1-s", "openicon" : "ui-icon-arrowreturn-1-e" },
            subGridRowExpanded: function(subgrid_id, row_id) {
                
                var data=$("#list11").getRowData(row_id); 
                
                $.ajax({
                    url: '<?= base_url() . "index.php" . $showskudata  ?>'+data.SKU,
                    type: "GET",
                    success: function(html) {
                        $("#" + subgrid_id).append(html);
                    }
                });
            }
        
    });

    jQuery("#list11").jqGrid('navGrid','#pager11',{cloneToTop:true,view:true,del:false,add:false,edit:false},{},{},{},{multipleSearch:true})
    jQuery("#list11").jqGrid('navButtonAdd', '#' + jQuery("#list11")[0].id + '_toppager_left', { // "#list_toppager_left"
        caption: "Excel",
        buttonicon: 'myicon',
        onClickButton: function(){
            exportExcel();
        }
    });
    
         resize_the_grid(myGrid);  
    
});





    var topPagerDiv = $('#' + jQuery("#list11")[0].id + '_toppager')[0];         // "#list_toppager"
    $("#edit_" + jQuery("#list11")[0].id + "_top", topPagerDiv).remove();        // "#edit_list_top"
    $("#del_" + jQuery("#list11")[0].id + "_top", topPagerDiv).remove();         // "#del_list_top"
    $("#search_" + jQuery("#list11")[0].id + "_top", topPagerDiv).remove();      // "#search_list_top"
    
    var bottomPagerDiv = $("div#pager14")[0];
    $("#add_" +jQuery("#list11")[0].id, bottomPagerDiv).remove();               // "#add_list"




    function exportExcel()
    {
        var mya=new Array();
        mya=$("#list11").getDataIDs();  // Get All IDs
        var data=$("#list11").getRowData(mya[0]);     // Get First row to get the labels
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
            data=$("#list11").getRowData(mya[i]); // get each row
            for(j=0;j<colNames.length;j++)
            {
                if(j==11){
                    data[colNames[j]]=data[colNames[j]].replace("<DIV style=\"BACKGROUND-COLOR: red; COLOR: white; FONT-WEIGHT: bold\" class=colorBlock>","");
                    data[colNames[j]]=data[colNames[j]].replace("<div class=\"colorBlock\" style=\"background-color: red; color: white; font-weight: bold;\">","");
                    data[colNames[j]]=data[colNames[j]].replace("<DIV style=\"BACKGROUND-COLOR: green; COLOR: white; FONT-WEIGHT: bold\" class=colorBlock>","");
                    data[colNames[j]]=data[colNames[j]].replace("<div class=\"colorBlock\" style=\"background-color: green; color: white; font-weight: bold;\">","");
                    data[colNames[j]]=data[colNames[j]].replace("<DIV style=\"BACKGROUND-COLOR: blue; COLOR: white; FONT-WEIGHT: bold\" class=colorBlock>","");
                    data[colNames[j]]=data[colNames[j]].replace("<div class=\"colorBlock\" style=\"background-color: blue; color: white; font-weight: bold;\">","");
                    data[colNames[j]]=data[colNames[j]].replace("</DIV>","");
                    data[colNames[j]]=data[colNames[j]].replace("</div>",""); 
                    
                }
                

                html=html+data[colNames[j]]+"\t"; // output each Row as tab delimited
            }
            html=html+"\n";  // output each row with end of line

        }

        html=html+"\n";  // end of line at the end
        document.forms[1].csvBuffer.value=html;
        document.forms[1].method='POST';
        document.forms[1].action='<?= base_url() . 'index.php' . $from . 'csvExport' ?>';
        document.forms[1].target='_blank';
        document.forms[1].submit();
    }


    $(window).resize(resize_the_grid);


   function formatCurrency(num) {
	num = num.toString().replace(/\$|\,/g,'');
	if(isNaN(num))
	    num = "0";
	sign = (num == (num = Math.abs(num)));
	num = Math.floor(num*100+0.50000000001);
	cents = num%100;
	num = Math.floor(num/100).toString();
	if(cents<10)
	    cents = "0" + cents;
	for (var i = 0; i < Math.floor((num.length-(1+i))/3); i++)
	num = num.substring(0,num.length-(4*i+3))+','+
	num.substring(num.length-(4*i+3));
	return (((sign)?'':'-') + '' + num + '.' + cents);
    }

    function fvirtualstock(cellvalue, options,rowData) {
	
	var value = formatCurrency(cellvalue);
	
        if ((cellvalue > rowData.InventoryMin) && (cellvalue < rowData.InventoryMax)) {
            return '<div class="colorBlock" style="background-color: green; color: white; font-weight: bold;">'+ value+'</div>';
        }
        if (cellvalue <= rowData.InventoryMin) {
            return '<div class="colorBlock" style="background-color: red; color: white; font-weight: bold;">'+ value +'</div>';
        }
        else{
            return '<div class="colorBlock" style="background-color: blue; color: white; font-weight: bold;">'+ value +'</div>';
        }
    }

</script>
<br>
<br>
<br>
<br>

