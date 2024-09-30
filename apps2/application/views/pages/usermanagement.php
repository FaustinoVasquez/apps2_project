<style type="text/css" media="screen">
    
    .ui-icon.myicon {
        width: 16px;
        height: 16px;
        background-image:url('<?= base_url() ?>images/toolbar/Excel-icon2.png')
    }

</style>

<!-- Defunimos los atributos para los elementos del formulario -->
<?php 

$formopen = array('id' => "myform", 'class'=>'myform');
$selectFullname ='class="form-select" id="fullname"';
$selectCategories = 'class="form-select" id="categories"';
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
        echo form_dropdown('fullName', $fullNameOptions, NULL, $selectFullname);
        echo form_dropdown('categories', $categoriesOptions, NULL, $selectCategories);
        echo "<br><label>Search:</label>" . form_input($inputsearch) .'&nbsp;&nbsp;';
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
var lastsel;
$(function(){
    var myGrid= $("#grid"),
        pagerSelector = "#pager", 
        myAddButton = function(options) {
            myGrid.jqGrid('navButtonAdd',pagerSelector,options);
            myGrid.jqGrid('navButtonAdd','#'+myGrid[0].id+"_toppager",options);
        };

   myGrid.jqGrid({
        url:'<?= base_url() . 'index.php'.$from.'GridData' ?>',
        postData: {
            ds:function() { return jQuery("#search").val(); },
            cuid: function() { return jQuery("#fullname option:selected").val(); },
            cat: function() { return jQuery("#categories option:selected").val(); },
        },
        datatype: "json",
        rowNum:50,
        rowList:[50,300,100000000],
        loadComplete: function() { $("option[value=100000000]").text('ALL');},
        colNames:<?= $colNames ?>,
        colModel:<?= $colModel ?>,
        pager: jQuery('#pager'),
        viewrecords: true,
        rownumbers: true,
        sortname: "SKU",
        sortorder: "asc",
        caption: '<?= $caption ?>',
        height: 600, 
        toppager:true,
        cellEdit: true,
        cellurl:"editSKU" ,
        onSelectRow: function(sku){ 
            if(sku && sku!==lastsel){ 
                jQuery('#grid').jqGrid('restoreRow',lastsel); 
                jQuery('#grid').jqGrid('editRow',sku,true); 
                lastsel=sku; 
            }else{
                jQuery('#grid').jqGrid('saveRow',lastsel);
                lastsel = -1;
            }
        },
        editurl:'<?= base_url() . 'index.php' . $from . '/EditData' ?>',
    });
        myGrid.jqGrid('navGrid',pagerSelector,{cloneToTop:true,edit:false,add:false,del:false,search:true});

	myGrid.jqGrid('navButtonAdd', '#' + jQuery(grid)[0].id + '_toppager_left', { // "#list_toppager_left"
        caption: "Excel",
        buttonicon: 'myicon',
        onClickButton: function(){
            exportExcel(myGrid,'datos');
            }
        }); 


        myGrid.jqGrid('navButtonAdd', '#' + jQuery(grid)[0].id + '_toppager_left', { // "#list_toppager_left"
        caption: "AutoPrice",
        buttonicon: 'ui-icon-check',
        onClickButton: function(){
            autoprice(1);
            }
        }); 

        myGrid.jqGrid('navButtonAdd', '#' + jQuery(grid)[0].id + '_toppager_left', { // "#list_toppager_left"
        caption: "AutoPrice",
        buttonicon: 'ui-icon-close',
        onClickButton: function(){
            autoprice(0);
            }
        }); 


    //Funcion para recargar el grid
    var myReload = function() {
        myGrid.trigger('reloadGrid');
    };
    
    //  $('#search').prop('disabled',true);
    // if ($("#fullname option:selected").val() != 0 ){
    //     $('#search').prop('disabled',false);
    // }

    //Recargar el grid en el evento submit del formulario
    $( 'form' ).on( 'submit', function( e ){
         e.preventDefault();
         myReload();
     });


    //Recargar el grid en el evento onChange del select categories del formulario
    $( "#fullname" ).on('change',function(){
        myReload();
    })

    //Recargar el grid en el evento onChange del select categories del formulario
    $( "#categories" ).on('change',function(){
        myReload();
    });



    resizeTheGrid(); 
});

// function checkBox(e) {
//   e = e||event;/* get IE event ( not passed ) */
//   e.stopPropagation? e.stopPropagation() : e.cancelBubble = true;

//  var request = $.ajax({
//                 url: "autoPrice",
//                 type: "POST",
//                 data: {
//                         cuid: function() { return jQuery("#fullname option:selected").val(); },
//                         cat: function() { return jQuery("#categories option:selected").val(); }
//                        },
//                 });

//      request.done(function( msg ) {
//            jQuery("#grid").trigger('reloadGrid');
//         });
// }

function autoprice(mtype){

    var request = $.ajax({
        url: "autoPrice",
        type: "POST",
        data: {
                cuid: function() { return jQuery("#fullname option:selected").val(); },
                cat: function() { return jQuery("#categories option:selected").val(); },
                mtype: mtype
               },
        });

     request.done(function( msg ) {
           jQuery("#grid").trigger('reloadGrid');
        });

}



var resizeTheGrid = function() {
    $("#grid").fluidGrid({base:'#grid_wrapper', offset:-20});
};

var validateSku = function(sku){
    var customerID = myCustomerID;
    var validate = myajax(sku, '<?= base_url() . 'index.php' . $from.'validateSku?sku=' ?>'+sku+'&cuid='+myVariable);

    return validate;
};

$(window).resize(resizeTheGrid);


function ajaxSave(rowid, curCheckbox,grid) {
     var field = curCheckbox.name;
     var value = curCheckbox.checked;
     var request = $.ajax({
                url: "editSKU",
                type: "POST",
                data: { id : rowid,
                        checkbox: field,
                        value: value
                       },
                });

     request.done(function( msg ) {
           jQuery("#"+grid).trigger('reloadGrid');
        });
     }

    function checkboxFormatter(cellvalue, options, rowObject) {
        cellvalue = cellvalue + "";
        cellvalue = cellvalue.toLowerCase();
        var bchk = cellvalue.search(/(false|0|no|off|n)/i) < 0 ? " checked=\"checked\"" : "";
        return "<input type='checkbox' onclick=\"ajaxSave('" + options.rowId + "', this,'myGrid');\" " + bchk + " value='" + cellvalue + "' name='"+options.colModel.name+"' id='"+options.colModel.name+"' offval='0' />";
    }


    
    
</script>

<!-- <div id="dialog" title="Select SKU"></div> -->



   
    

