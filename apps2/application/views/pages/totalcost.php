<?php
$formopen = array('id' => 'costbyproductline' , 'class'=>'myform');
echo form_open(base_url() . 'index.php/' . $from, $formopen);
?>
<fieldset class="bluegradiant">

    <div class="form-data"> 
        <?php
        $inputto = array('id' => 'to', 'name' => 'dateto', 'class' => 'date-pick', 'value' => $dateto, 'size' => '10', 'onchange' => "this.form.submit()", 'style' => 'width:80px;text-align:center');
        echo "Cutof Date Inventory:" . form_input($inputto);

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





<div class="clear"></div>

<section id="grid_container">
    <table id="list30"></table>
    <div id="pager30"></div>
</section>

<script type="text/javascript">

    function resize_the_grid(){
        $("#list30").fluidGrid({base:'#grid_wrapper', offset:-20});
    }


    $(document).ready(function(){
    
        var myGrid = $("#list30");

        myGrid.jqGrid({
 
            url:'<?= base_url() . 'index.php' . $from . 'gridDataTotalcost?dateto=' . $dateto ?>',
           
            datatype: "json",
            height: 350,
            colNames:<?= $headers ?>,
            colModel:<?= $body ?>,
            pager: '#pager30',
            rowNum: 50,
            rowList: [50,300,500,1000],
            rownumbers: true,
            sortname: 'productlineid',
            sortorder: 'asc',
            viewrecords: true,
            caption: "<?= $caption ?>",
            toppager:true,
            footerrow : true,
            userDataOnFooter : true
        });  
        jQuery("#list30").jqGrid('navGrid','#pager30',{cloneToTop:true,view:true,del:false,add:false,edit:false},{},{},{},{multipleSearch:true})
    
    
        //Llamamos a resize si el navegador ha sido modificado en su tama;o
        resize_the_grid(myGrid);  
    
    });

    $(window).resize(resize_the_grid);
    
    
    $(function() {
        $( ".date-pick" ).datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat:"mm/dd/yy"
        });
    });
    
</script>


