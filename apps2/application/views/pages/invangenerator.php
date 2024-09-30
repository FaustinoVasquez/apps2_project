

<?php
    $formopen = array('id' => "myform", 'class' => 'myform');
    $selectCategories = 'class="form-select" id="category" style="width: 400px"';
    $search  = array('name' => 'search', 'id'=>'search','value' => '', 'autocomplete' => 'on', 'maxlength' => '250', 'size' => '50');
    $upload = array('name' => 'userfile', 'value' => 'Choose File', 'type' => 'file');
    $submit = array('name' => 'send', 'value' => 'Submit', 'type' => 'submit', 'class' => 'button');
    
    $selectCategoriesModal = 'class="form-select" id="categorymodal" style="width: 200px"';
    $targetModal  = array('name' => 'target', 'id'=>'target','value' => '', 'autocomplete' => 'on', 'maxlength' => '3', 'size' => '5');
    $submitModal = array('name' => 'send1', 'value' => 'Generate report', 'type' => 'submit', 'class' => 'button');
?>


<?php
echo form_open(base_url() . 'index.php' . $from, $formopen);
?>
<fieldset class="bluegradiant">
        <div class="logo"><img longdesc="MI Technologies" title="Mi Tech" alt="43" src="<?= base_url() ?>/images/header/mitechnologies.png"></div>
        <div class="form-data"> 
            <?php
                echo "<label>Search : </label>" ;
                echo form_input($search).'&nbsp;&nbsp;';
                echo form_dropdown('categories', $categories,'0', $selectCategories).'<br>';
                echo form_input($submit);
            ?>
        </div>
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

    //Creamos un objeto


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
                    url: "editData",
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

    $(function(){
        var pass='';

        var myGrid = $("#grid");

        myGrid.jqGrid({
            url:'getData',
            postData: {
                ds:   function() { return jQuery("#search").val(); },
                cat:   function() { return jQuery("#category option:selected").val(); },
                td:   function() { return jQuery("#target").val(); },
            },
            datatype: "json",
            colNames:<?= $colNames ?>,
            colModel:<?= $colModel ?>,
            pager: jQuery('#pager'),
            rowNum: 50,
            sortname: 'IAR.[SKU]',
            sortorder: "asc",
            rowList:[50,300,500,100000000],
            loadComplete: function() { $("option[value=100000000]").text('ALL');},
            rownumbers: true,
            viewrecords: true,
            caption: "<?=$caption?>",
            height: 600,
            toppager:true,
            shrinkToFit: false,
        });

        myGrid.jqGrid('navGrid','#pager',{cloneToTop:true,edit:false,add:false,del:false,search:false,refresh:false});

        jQuery("#grid").jqGrid('setFrozenColumns');

        add_top_bar(myGrid);
        resize_the_grid();


        var myReload = function() {
            myGrid.setGridParam({ page: 1, datatype: "json" }).trigger('reloadGrid');
        };

         // Recargar el grid en el evento submit del formulario
        $( '#myform' ).on( 'submit', function( e ){
             e.preventDefault();
              myReload();
         });


        //Recargar el grid en el evento onChange del select search del formulario
        $( "#search" ).on('change',function(){
            myReload();
        });

          //Recargar el grid en el evento onChange del select search del formulario
        // $( "#category" ).on('change',function(){
        //     myReload();
        // });

        //    //Recargar el grid en el evento onChange del select search del formulario
        // $( "#category" ).on('change',function(){
        //     myReload();
        // });

    });


    $(window).resize(resize_the_grid);
    function add_top_bar(grid){

        jQuery(grid).jqGrid('navButtonAdd', '#' + jQuery(grid)[0].id + '_toppager_left', { // "#list_toppager_left"
            caption: "Excel",
            buttonicon: 'ui-icon-document',
            onClickButton: function(){
                exportExcel(grid,'Inventory_Analytics_Generator');
            }
        });

        jQuery(grid).jqGrid('navButtonAdd', '#' + jQuery(grid)[0].id + '_toppager_left', { // "#list_toppager_left"
        caption: "Generate Report",
        buttonicon: 'ui-icon-print',
        onClickButton: function(){
            $( "#dialog-form"  ).dialog( "open" ); 
            }
        }); 

        jQuery(grid).jqGrid('navButtonAdd', '#' + jQuery(grid)[0].id + '_toppager_left', { // "#list_toppager_left"
        caption: "Configure Automation",
        buttonicon: 'ui-icon-wrench',
        onClickButton: function(){
            $( "#dialog-configure"  ).dialog( "open" ); 
            }
        }); 
    }


    $(function() {
   
       var dialog;
       var tips = $( ".validateTips" );

       function updateTips( t ) {
          tips
            .text( t )
            .addClass( "ui-state-highlight" );
          setTimeout(function() {
            tips.removeClass( "ui-state-highlight", 1500 );
          }, 500 );
        }

       function validateform(category,target){
            // var category = $( "#categorymodal").val();
            // var target = $( "#target").val();

            if ( (category ==null) || (category ==0) ){
                updateTips('Please, select a category');
                return false;
            }

             if ( (target == null) || (target <= 0) ){
                updateTips('Please, fill target days');
                return false;
            }

            return true;

       }

        dialog = $( "#dialog-form" ).dialog({
        autoOpen: false,
        height: 190,
        width: 550,
        modal: true,
        open: function() {
                           
                     $( this ).find( "[type=submit]" ).hide();
                        },
                 buttons: [
                   {
                        text: "Generate Report",
                        click: $.noop,
                        type: "submit",
                        form: "myForm1"
                    },
                    {
                        text: "Close",
                        click: function() {
                          $( this ).dialog( "close" );
                        }
                    }

                ],
                close: function() {
                    dialog.dialog( "close" );
                }
        });

        dialog.find( "form" ).on( "submit", function( event ) {
          event.preventDefault();
           var catId = $( "#categorymodal").val();
           var targetDays = $( "#target").val();

          if (validateform(catId,targetDays) == true){
                $( "#dialog-password").data("catId",catId).data("targetDays",targetDays).data("dialog",'dialog-form').dialog( "open" ); 
            }
        });
    });





    $(function() {
   
       var dialogc;
       var tips = $( ".validateTips" );
    
        function updateMTips( t ) {
          tips
            .text( t )
            .addClass( "ui-state-highlight" );
          setTimeout(function() {
            tips.removeClass( "ui-state-highlight", 1500 );
          }, 500 );
        }

        dialogc = $( "#dialog-configure" ).dialog({
        autoOpen: false,
        height: 410,
        width: 1060,
        modal: true,
        open: function() {

                           
                    jQuery("#gridConf").jqGrid({
                                url:'getDataConf',
                                datatype: "json",
                                colNames:<?= $colNamesConf ?>,
                                colModel:<?= $colModelConf ?>,
                                pager: jQuery('#pagerConf'),
                                rowNum: 150,
                                sortname: 'ID',
                                sortorder: "asc",
                                rownumbers: true,
                                viewrecords: true,
                                caption: "<?=$captionConf?>",
                                height: 200,
                                cellEdit : true,
                                cellurl:'editData',
                            });

                        },
                 buttons: [
                   {
                        text: "Generate Report",
                        click: function(){
                              //  $( "#dialog-password"  ).dialog( "open" ); 

                            var selRowId = jQuery('#gridConf').jqGrid ('getGridParam', 'selrow');
                            var targetDays = jQuery('#gridConf').jqGrid ('getCell', selRowId, 4);
                            var isActive = jQuery('#gridConf').jqGrid ('getCell', selRowId, 3);
                            if ( isActive ==0)
                            {
                                updateMTips('That report is not available');
                            }
                            else
                            {
                                $( "#dialog-password").data("catId",selRowId).data("targetDays",targetDays).data("dialog",'dialog-configure').dialog( "open" ); 
                                   
                            }
                        }
                    },
                    {
                        text: "Close",
                        click: function() {
                          $( this ).dialog( "close" );
                        }
                    }

                ],
                close: function() {
                    $( this ).dialog( "close" );
                }
        });

    });


    $(function() {
   
       var dialogp;
        var tips = $( ".validatePassw" );
    
        function updateMTips( t ) {
          tips
            .text( t )
            .addClass( "ui-state-highlight" );
          setTimeout(function() {
            tips.removeClass( "ui-state-highlight", 1500 );
          }, 500 );
        }


       function validatePassword(password){
            if (password != 'pLa13t1B')
            {
                updateMTips('Wrong Password');
                return false
            }
            else
            { 
                return true
            }
       }
      

        dialogp = $( "#dialog-password" ).dialog({
        autoOpen: false,
        height: 150,
        width: 210,
        modal: true,
        open: function() {
                      
                        $( this ).find( "[type=submit]" ).hide();
                        $( this ).find( "[type=password]" ).val('');
                        },
                 buttons: [
                   {
                        text: "Submit",
                        click: $.noop,
                        type: "submit",
                        form: "myForm2"

                    },
                    {
                        text: "Close",
                        click: function() {
                          $( this ).dialog( "close" );
                        }
                    }

                ],
                close: function() {
                    $( this ).dialog( "close" );
                } 
        });


        dialogp.find( "form" ).on( "submit", function( event ) {
          event.preventDefault();

           var cartID = dialogp.data('catId');
           var targetDays= dialogp.data('targetDays');
           var dialog= dialogp.data('dialog');
           var password = dialogp.find( "[type=password]" ).val();


          if (validatePassword(password) == true){

                $.ajax({
                    type: "POST",
                    url: 'generateReport',
                    data: {cId:cartID,td:targetDays},
                    async: true,
                });

                dialogp.dialog( "close" );
                $( '#'+dialog ).dialog( "close" );
            }
        });

    });

</script>


<div id="dialog-form" title="Generate Report">

    <p class="validateTips">All form fields are required and the values must be appropiated.</p>
   <br>
    <form  id="myForm1" action="javascript:;" method="Post">
    <?php
        echo form_label("Categories: ");
        echo form_dropdown('categories', $categories,'0', $selectCategoriesModal).'&nbsp;&nbsp;&nbsp;&nbsp;';
        echo form_label("Target Days: ");
        echo form_input($targetModal);
    ?>
     <input type="submit" value="Submit">
    </form>
    <br>
    <p>The report will take some time to be generated, when completed, an email will be send to you!</p>
</div>

<div id="dialog-configure" title="Configure Automation">
    <p class="validateTips">Select Grid Row and press Generate Report!.</p>
    <table id="gridConf"></table>
    <div id="pagerConf"></div>
</div>


<div id="dialog-password" title="Type Password">
<p class="validatePassw">Type your password!.</p>
<p>
    <form  id="myForm2" action="javascript:;" method="Post">
    <input type="password" id="password" name="psw" class="ui-widget-content ui-corner-all" style="width: 170px">
     <input type="submit" value="Submit">
    </form>
</p>
</div>
