<?php  

$formopen = array('id' => "form", 'class'=>'myform');
$inputsearch = array('name' => 'search','id'=>'search','value'=>'','autocomplete'=>'on','maxlength'=>'250','size'=>'20');
$submit = array('name' => 'send', 'id'=>'btnSubmit', 'value' => 'Submit', 'type' => 'submit', 'class' => 'button');
$reset = array('name' => 'reset', 'value' => 'Reset', 'type' => 'submit', 'class' => 'button');
?>


<!-- Creamos el formulario -->


<?php echo form_open(base_url() . 'index.php' . $from, $formopen); ?>
<fieldset class="bluegradiant">
    <div class="logo"><img longdesc="MI Technologies" title="Mi Tech" alt="43" src="<?= base_url() ?>/images/header/mitechnologies.png"></div>
    <div class="form-data"> 
        <?php
        echo "<label>Search:</label>" . form_input($inputsearch) .'&nbsp;';
        echo form_input($submit);
        echo form_input($reset);
        ?>
    </div>
</fieldset>
<br>
<?php echo form_close(); ?>


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


    function autocomplete_element(value, options) {
        // create input element
        var $ac = $('<input type="text" />');
        // setting value to the one passed from jqGrid
        $ac.val(value);
        // creating autocomplete
        $ac.autocomplete({source: "autocompletesku"});
 
        return $ac.get(0); 

    }
 
    function autocomplete_value(elem, op, v) {
        if (op == 'set') {
            $(elem).val(v);
        }
      return $(elem).val();
    }
     
     $(function(){

         var myGrid= $("#grid"),
            pagerSelector = "#pager", 
            myAddButton = function(options) {
                myGrid.jqGrid('navButtonAdd',pagerSelector,options);
                myGrid.jqGrid('navButtonAdd','#'+myGrid[0].id+"_toppager",options);
            };
      
        myGrid.jqGrid({
            url:'getData',
            datatype: "json",
            postData: {
            ds:      function() { return jQuery("#search").val(); },
            },
            colNames:<?= $headers ?>,
            colModel:<?= $body ?>,
            height:600,
            autowidth: true,
            pager: '#pager',
            rowNum: 50,
            rowList: [50,100,200],
            rownumbers: true,
            sortname: 'ProductCatalogID',
            sortorder: 'asc',
            viewrecords: true,
            caption: "<?= $caption ?>",
            toppager:true,
            editurl:'crudOperation',
        });
        jQuery("#grid").jqGrid('navGrid','#pager',{cloneToTop: true
                                        ,edit: true
                                        ,add: true
                                        ,del: true
                                        ,search:false
                                        ,reload:false
                                      },
            {//Edit Options
            recreateForm:true,
            jqModal:false,
            reloadAfterSubmit:true,
            closeOnEscape:true,
            closeAfterEdit:true,
            zIndex:1000,
            width: 450,        

            beforeInitData: function(formid) {
                $("#grid").jqGrid('setColProp','Id',{editable:true});
            },
            afterShowForm: function (formid) {
                $("#grid").jqGrid('setColProp','Id',{editable:false});
            },
        },
        {   //addOptions
            recreateForm:true,
            jqModal:false,
            reloadAfterSubmit:true,
            closeOnEscape:true,
            closeAfterEdit:true,
            zIndex:1000,
            width: 450,
        });


        // myGrid.jqGrid('navButtonAdd', '#' + myGrid[0].id + '_toppager_left', { // "#grid_toppager_left"
        //     caption: "Excel",
        //     buttonicon: 'myicon',
        //     onClickButton: function(){
        //         exportExcel();
        //     }
        // });


       resize_the_grid();

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


// function autocomplete_element(value, options) {
//   // creating input element
//   var $ac = $('<input type="text"/>');
//   // setting value to the one passed from jqGrid
//   $ac.val(value);
//   // creating autocomplete
//   $ac.autocomplete({source: "autocompletesku"});
//   // returning element back to jqGrid
//   return $ac;
// }

// function autocomplete_value(elem, op, value) {
//   if (op == "set") {
//     $(elem).val(value);
//   }
//   return $(elem).val();
// }


    
    $(window).resize(resize_the_grid);

    

</script>




