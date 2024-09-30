<?php
$formopen = array('id' => 'mercadolibre' , 'class'=>'myform');
echo form_open(base_url() . 'index.php/' . $from, $formopen);
?>
<fieldset class="bluegradiant">
    <div class="logo"><img longdesc="MI Technologies" title="Mi Tech" alt="43" src="<?= base_url() ?>/images/header/mitechnologies.png"> </div>

    <div class="form-data"> 
        <?php
        $inputsearch = array('name' => 'Search', 'id'=>'search','value' => '', 'autocomplete' => 'on', 'maxlength' => '250', 'size' => '20');
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


<div class="clear"></div>

<section id="grid_container">
    <table id="list"></table>
    <div id="pager"></div>
</section>

<script type="text/javascript">

    function resize_the_grid(){
        $("#list").fluidGrid({base:'#grid_wrapper', offset:-20});
    }


    $(document).ready(function(){
    
        var myGrid = $("#list");

        myGrid.jqGrid({
            url:'mercadolibre/getData',
             postData: {
                search: function() { return jQuery("#search").val(); },
            },
            datatype: "json",
            height: 600,
            colNames:<?= $colNames ?>,
            colModel:<?= $colModel ?>,
            pager: '#pager',
            rowNum: 50,
            rowList: [50,300,500,1000],
            rownumbers: true,
            sortname: 'ProductCatalogID',
            sortorder: 'asc',
            viewrecords: true,
            caption: "<?= $caption ?>",
            toppager:true,
            shrinkToFit: false,
        });  
        jQuery("#list").jqGrid('navGrid','#pager',{cloneToTop:true,view:true,del:false,add:false,edit:false},{},{},{},{multipleSearch:true})

        jQuery("#list").jqGrid('setFrozenColumns');
    
        //Llamamos a resize si el navegador ha sido modificado en su tama;o
        resize_the_grid(myGrid);  

         var myReload = function() {
            myGrid.trigger('reloadGrid');
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
    
    });

    $(window).resize(resize_the_grid);
    
    
    $(function() {
        $( ".date-pick" ).datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat:"mm/dd/yy"
        });
    });

     function formatLink1(cellvalue, options,rowObject) {
        if (cellvalue!=null){   
        return  '<input type="button" value="Image1" onclick="window.open(\''+cellvalue+'\')" style="background-color:#CEECF5; color:#blue;">';
        }
    }
        
     function formatLink2(cellvalue, options,rowObject) {
        if (cellvalue!=null){   
        return  '<input type="button" value="Image2" onclick="window.open(\''+cellvalue+'\')" style="background-color:#CEE3F6; color:#blue;">';
        }
    }
     function formatLink3(cellvalue, options,rowObject) {
        if (cellvalue!=null){   
        return  '<input type="button" value="Image3" onclick="window.open(\''+cellvalue+'\')" style="background-color:#CED8F6; color:#blue;">';
        }
    }
     function formatLink4(cellvalue, options,rowObject) {
        if (cellvalue!=null){   
        return  '<input type="button" value="Image4" onclick="window.open(\''+cellvalue+'\')" style="background-color:#CECEF6; color:#blue;">';
        }
    }
    function formatLink5(cellvalue, options,rowObject) {
        if (cellvalue!=null){   
        return  '<input type="button" value="Image5" onclick="window.open(\''+cellvalue+'\')" style="background-color:#D8CEF6; color:#blue;">';
        }
    }
    
</script>

