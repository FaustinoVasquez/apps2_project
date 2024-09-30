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
        sortname: "rowid",
        sortorder: "asc",
        caption: '<?= $caption ?>',
        height: 600, 
        toppager:true,
        cellEdit: true,
        cellurl:"editSKU" ,
        editurl:'<?= base_url() . 'index.php' . $from . 'editData' ?>',
    });
        myGrid.jqGrid('navGrid',pagerSelector,{cloneToTop:true,edit:false,add:false,del:true,search:true});

	myGrid.jqGrid('navButtonAdd', '#' + jQuery(grid)[0].id + '_toppager_left', { // "#list_toppager_left"
        caption: "Excel",
        buttonicon: 'myicon',
        onClickButton: function(){
            exportExcel(myGrid,'datos');
            }
        }); 


    //Funcion para recargar el grid
    var myReload = function() {
        myGrid.trigger('reloadGrid');
    };

    //Recargar el grid en el evento submit del formulario
    $( 'form' ).on( 'submit', function( e ){
         e.preventDefault();
         myReload();
     });


    resizeTheGrid(); 
});



var resizeTheGrid = function() {
    $("#grid").fluidGrid({base:'#grid_wrapper', offset:-20});
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

    
</script>

<!-- <div id="dialog" title="Select SKU"></div> -->



   
    

