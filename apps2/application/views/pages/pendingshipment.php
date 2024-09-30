<style type="text/css" media="screen">
.ui-icon.myicon {
        width: 16px;
        height: 16px;
        background-image:url('<?= base_url() ?>images/toolbar/Excel-icon2.png')
    }

    .blink {
      animation: blink 1s steps(5, start) infinite;
      -webkit-animation: blink 1s steps(5, start) infinite;
    }
    @keyframes blink {
      to {
        visibility: hidden;
      }
    }
    @-webkit-keyframes blink {
      to {
        visibility: hidden;
      }
    }
</style>

<?php
$formopen = array('id' => 'target', 'class' => 'myform');
$inputsearch = array('id'=>'search','name' => 'search', 'value' => '', 'autocomplete' => 'on', 'maxlength' => '250', 'size' => '20');
$selectCartName = 'class="form-select" id="cartname"';
$selectShippingMethod = 'class="form-select" id="shipping"';
$submit = array('name' => 'send', 'value' => 'Submit', 'type' => 'submit', 'class' => 'button');
$reset = array('name' => 'reset', 'value' => 'Reset', 'type' => 'submit', 'class' => 'button');


echo form_open(base_url() . 'index.php' . $from, $formopen);
?>
<fieldset class="bluegradiant">
    <div class="logo"><img longdesc="MI Technologies"  title="Mi Tech" alt="43" src="<?= base_url() ?>/images/header/mitechnologies.png"></div>
    <div class="form-data"> 
    <center>
           
    <?php echo form_label('Search: ');
          echo form_input($inputsearch);
          echo '&nbsp;';
          echo form_label('Cart: ');
          echo form_dropdown('selectMenu', $cartname,'0', $selectCartName);
          echo '<br>';
          echo form_label('Shipping Method: ');
          echo form_dropdown('selectMenu', $shipping,'0', $selectShippingMethod);
          echo '&nbsp;&nbsp;&nbsp;&nbsp;';
          echo form_input($submit).'&nbsp;'.form_input($reset);
    ?>
        <center>
    </div>
</fieldset>
    <div id="totals" style="background-color:green; width:100%;height:60px;color:white;font-size:40px;line-height:60px;text-align:center"></div>
<br>
<?php
echo form_close();
?>

<div class="clear"></div>





<section id="grid_container">

 <table id="list1"></table>
    <div id="pager1"></div> 
<hr>
    <table id="list"></table>
    <div id="pager"></div> 
<hr>
    <table id="list2"></table>
    <div id="pager2"></div>
</section>

<form method="post" action="<?= base_url() . 'index.php' . $from . 'csvExport' ?>">
    <input type="hidden" name="csvBuffer" id="csvBuffer" value="" />
</form>  

<div id="dialog-modal" title="Details" style="display: none;"></div>


<script type="text/javascript">
    
    function resize_the_grid()
    {
        $("#list1").fluidGrid({base:'#grid_wrapper', offset:-20});
        $("#list").fluidGrid({base:'#grid_wrapper', offset:-20});
        $("#list2").fluidGrid({base:'#grid_wrapper', offset:-20});
    }
        
    $(document).ready(function(){

        var myGrid1 = $("#list1"),
            myGrid = $("#list"),
            myGrid2 = $("#list2"),
            intervalId = setInterval(function() {
            myGrid1.trigger("reloadGrid",[{current:true}]);
            myGrid.trigger("reloadGrid",[{current:true}]);
            myGrid2.trigger("reloadGrid",[{current:true}]);
        },3000000); // 5 mins


        myGrid1.jqGrid({
            url:'getData1',
            datatype: "json",
            colNames:<?= $colNames1 ?>,
            colModel:<?= $colModel1 ?>,
            autowidth: true,
            pager: jQuery('#pager1'),
            rowNum: 50,
            sortname: 'asc',
            rowList:[50,500,100000000],
            rownumbers: true,
            viewrecords: true,
            caption: "Pending Orders by Shipping Method",
            height: 150, 
        });
        jQuery("#list1").jqGrid('navGrid','#pager1',{cloneToTop:false,edit:false,add:false,del:false,search:false,refresh:true});


        myGrid.jqGrid({
            url:'getData',
            postData: {
                ds:   function() { return jQuery("#search").val(); },
                car:  function() { return jQuery("#cartname option:selected").val(); },
                shi:  function() { return jQuery("#shipping option:selected").val(); },
            },
            datatype: "json",
            colNames:<?= $colNames ?>,
            colModel:<?= $colModel ?>,
            autowidth: true,
            pager: jQuery('#pager'),
            rowNum: 50,
            sortname: 'asc',
            rowList:[50,300,500,100000000],
            loadComplete: function() { $("option[value=100000000]").text('ALL');},
            rownumbers: true,
            viewrecords: true,
            caption: "<?= $caption ?>",
            height: 410, 
            toppager:true,
            cellEdit: true,
            cellurl:"SaveInfo" ,
            gridComplete: function () {
                $('#totals').empty();
                $('#totals').append('Pending: <span class="blink">'+$('#list').getGridParam('records')+'</span> Orders');
            }

        });

        jQuery("#list").jqGrid('navGrid','#pager',{cloneToTop:true,edit:false,add:false,del:false,search:false,refresh:true});

        myGrid.jqGrid('navButtonAdd', '#' + myGrid[0].id + '_toppager_left', { // "#list_toppager_left"
            caption: "Excel",
            buttonicon: 'myicon',
            onClickButton: function(){
                exportExcel(myGrid,'<?= $export ?>'); 
            }
        });

        myGrid2.jqGrid({
            url:'getData2',
            datatype: "json",
            colNames:<?= $colNames2 ?>,
            colModel:<?= $colModel2 ?>,
            autowidth: true,
            pager: jQuery('#pager2'),
            rowNum: 50,
            sortname: 'asc',
            rowList:[50,500,100000000],
            rownumbers: true,
            toppager:true,
            viewrecords: true,
            caption: "Pending Items",
            height: 410, 
        });
        jQuery("#list2").jqGrid('navGrid','#pager2',{cloneToTop:true,edit:false,add:false,del:false,search:false,refresh:true});


        myGrid2.jqGrid('navButtonAdd', '#' + myGrid2[0].id + '_toppager_left', { // "#list_toppager_left"
            caption: "Excel",
            buttonicon: 'myicon',
            onClickButton: function(){
                exportExcel(myGrid2,'<?= $export ?>'); 
            }
        });

        // var totalRows= jQuery('#list').jqGrid('getGridParam','records');
        // $("#totals").append('<h1>'+totalRows+'<h1>');
        resize_the_grid();

            var myReload = function() { 
                 myGrid.setGridParam({ page: 1, datatype: "json" }).trigger('reloadGrid');
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

            //Recargar el grid en el evento onChange del select search del formulario
            $( "#cartname" ).on('change',function(){
               myReload();
            });

            //Recargar el grid en el evento onChange del select search del formulario
            $( "#shipping" ).on('change',function(){
               myReload();
            });

    });
    
    $(window).resize(resize_the_grid);


    function formatLink(cellvalue, options,rowData) {
        if (cellvalue!=null){    
            return "<a href=<?= base_url()  . 'index.php' .$formatLink?>"+'&on='+ cellvalue +'&csid='+ rowData[7] +'&from=0'+">" + cellvalue + "</a>";
        }
        
    }

</script>

