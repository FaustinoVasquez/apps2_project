<style type="text/css" media="screen">
    .ui-icon.myicon {
        width: 16px;
        height: 16px;
        background-image:url('<?= base_url() ?>images/toolbar/Excel-icon2.png')
    }

</style>


 <?php
            $form =         array('id'=>'target', 'class' => 'myform');
            $inputsearch =  array('id'=>'search','name' => 'search', 'value' => '', 'autocomplete' => 'on', 'maxlength' => '250', 'size' => '20');
            $submit =       array('id'=>'submit','name' => 'send', 'value' => 'Submit', 'type' => 'submit', 'class' => 'button');
            $reset =        array('id'=>'reset','name' => 'reset', 'value' => 'Reset', 'type' => 'submit', 'class' => 'button');

            $form1 =        array('id'=>'target1', 'class' => 'myform');
            $inputsearch1 = array('id'=>'search1','name' => 'search1', 'value' => '', 'autocomplete' => 'on', 'maxlength' => '250', 'size' => '20');
            $submit1 =      array('id'=>'submit1','name' => 'send1', 'value' => 'Submit', 'type' => 'submit', 'class' => 'button');
            $reset1 =       array('id'=>'reset1','name' => 'reset1', 'value' => 'Reset', 'type' => 'submit', 'class' => 'button');

            $form2 =        array('id'=> 'target2', 'class' => 'myform');
            $inputsearch2 = array('id'=>'search2','name' => 'search2', 'value' => '', 'autocomplete' => 'on', 'maxlength' => '250', 'size' => '20');
            $submit2 =      array('id'=>'submit2','name' => 'send2', 'value' => 'Submit', 'type' => 'submit', 'class' => 'button');
            $reset2 =       array('id'=>'reset2','name' => 'reset2', 'value' => 'Reset', 'type' => 'submit', 'class' => 'button');

            $form3 =        array('id'=>'target3', 'class' => 'myform');
            $inputsearch3 = array('id'=>'search3','name' => 'search3', 'value' => '', 'autocomplete' => 'on', 'maxlength' => '250', 'size' => '20');
            $submit3 =      array('id'=>'submit3','name' => 'send3', 'value' => 'Submit', 'type' => 'submit', 'class' => 'button');
            $reset3 =       array('id'=>'reset3','name' => 'reset3', 'value' => 'Reset', 'type' => 'submit', 'class' => 'button');

  ?>

    
<div class="clear"></div>
<section id="grid_container">

    <div class="tabs">
        <ul>
            <li><a href="#tabs-1">Request UnMerge</a></li>
            <li><a href="#tabs-2">Add Missing ASIN to Amazon Table</a></li>
            <li><a href="#tabs-3">eMapping Mismatch Need Fixing</a></li>
            <li><a href="#tabs-4">OK Mergers</a></li>
        </ul>
        <div id="tabs-1">
            <?php
            echo form_open(base_url() . 'index.php' . $from, $form);
            ?>
            <fieldset class="bluegradiant">
                <div class="logo"><img longdesc="MI Technologies"  title="Mi Tech" alt="43" src="<?= base_url() ?>/images/header/mitechnologies.png"></div>
                <div class="form-data ">
                    <center>
                        <table>
                            <tbody>
                            <tr>
                                <td><?=form_label('Search: ')?></td>
                                <td><?=form_input($inputsearch)?></td>
                                <td colspan="4"><?=form_input($submit).'&nbsp;'.form_input($reset);?></td>
                            </tr>
                            </tbody>
                        </table>
                    </center>
                </div>
            </fieldset>
            <br>
            <?php
            echo form_close();
            ?>
    
            <div class="clear"></div>

            <center>
               <table id="list"></table>
               <div id="pager"></div>
            </center>
        </div>
        <div id="tabs-2">
            <?php
                echo form_open(base_url() . 'index.php' . $from, $form1);
            ?>
                <fieldset class="bluegradiant">
                    <div class="logo"><img longdesc="MI Technologies"  title="Mi Tech" alt="43" src="<?= base_url() ?>/images/header/mitechnologies.png"></div>
                    <div class="form-data ">
                        <center>
                            <table>
                                <tbody>
                                <tr>
                                    <td><?=form_label('Search: ')?></td>
                                    <td><?=form_input($inputsearch1)?></td>
                                    <td colspan="4"><?=form_input($submit1).'&nbsp;'.form_input($reset1);?></td>
                                </tr>
                                </tbody>
                            </table>
                        </center>
                    </div>
                </fieldset>
                <br>
                <?php
                echo form_close();
                ?>
        
                <div class="clear"></div>
            <center>
               <table id="list1"></table>
               <div id="pager1"></div>
            </center>
        </div>
        <div id="tabs-3">
                <?php
                echo form_open(base_url() . 'index.php' . $from, $form2);
                ?>
                <fieldset class="bluegradiant">
                    <div class="logo"><img longdesc="MI Technologies"  title="Mi Tech" alt="43" src="<?= base_url() ?>/images/header/mitechnologies.png"></div>
                    <div class="form-data ">
                        <center>
                            <table>
                                <tbody>
                                <tr>
                                    <td><?=form_label('Search: ')?></td>
                                    <td><?=form_input($inputsearch2)?></td>
                                    <td colspan="4"><?=form_input($submit2).'&nbsp;'.form_input($reset2);?></td>
                                </tr>
                                </tbody>
                            </table>
                        </center>
                    </div>
                </fieldset>
                <br>
                <?php
                echo form_close();
                ?>
        
                <div class="clear"></div>
             <center>
                <table id="list2"></table>
                <div id="pager2"></div>
            </center>
        </div>
        <div id="tabs-4">
                <?php
                echo form_open(base_url() . 'index.php' . $from, $form3);
                ?>
                <fieldset class="bluegradiant">
                    <div class="logo"><img longdesc="MI Technologies"  title="Mi Tech" alt="43" src="<?= base_url() ?>/images/header/mitechnologies.png"></div>
                    <div class="form-data ">
                        <center>
                            <table>
                                <tbody>
                                <tr>
                                    <td><?=form_label('Search: ')?></td>
                                    <td><?=form_input($inputsearch3)?></td>
                                    <td colspan="4"><?=form_input($submit3).'&nbsp;'.form_input($reset3);?></td>
                                </tr>
                                </tbody>
                            </table>
                        </center>
                    </div>
                </fieldset>
                <br>
                <?php
                echo form_close();
                ?>
                 <div class="clear"></div>
             <center>
               <table id="list3"></table>
               <div id="pager3"></div>
           </center>
        </div>
    </div>
</section>

<form method="post" action="<?= base_url() . 'index.php' . $from . 'csvExport' ?>">
    <input type="hidden" name="csvBuffer" id="csvBuffer" value="" />
</form>


<script type="text/javascript">

    function resize_the_grid(grid)
    {
        $(grid).fluidGrid({base:'#grid_wrapper', offset:-20});
    }


    $(function(){
        $( ".tabs" ).tabs({
            show: function (event, ui) {
              var active = ui.index;

               if(active == 1){
                resize_the_grid('#list1');
               }

               if(active == 2){
                resize_the_grid('#list2');
               }
                if(active == 3){
                resize_the_grid('#list3');
               }


            }   
        });

        var grid        =["#list", "#list1", "#list2", "#list3"];
        var pager       =["#pager", "#pager1", "#pager2", "#pager3"];
        var caption     =['Request UnMerge','Add Missing ASIN to Amazon Table','Mapping Mismatch Need Fixing','OK Mergers'];
        var myurl       =['getData','getData1','getData2','getData3'];
        var search      =["#search", "#search1", "#search2", "#search3"];
        var form        =["#target", "#target1", "#target2", "#target3"];
        var submit      =["#submit", "#submit1", "#submit2", "#submit3"]

        
        $.each(grid, function(index, value){
            creategrid(value,pager[index],caption[index],myurl[index],search[index],form[index],submit[index])
        });

    });



    function creategrid(grid,pager,caption,myurl,search,form){


        var myGrid = $(grid);

        myGrid.jqGrid({
            url:myurl,
            postData: {
                ds:   function() { return jQuery(search).val(); },
            },
            datatype: "json",
            colNames:<?= $colNames ?>,
            colModel:<?= $colModel ?>,
            autowidth: true,
            pager: jQuery(pager),
            rowNum: 50,
            sortname: '[AAM].[Date]',
            sortorder: 'asc',
            rowList:[50,300,500,100000000],
            loadComplete: function() { $("option[value=100000000]").text('ALL');},
            rownumbers: true,
            viewrecords: true,
            caption: caption,
            height: 600,
            toppager:true,

        });

        myGrid.jqGrid('navGrid',pager,{cloneToTop:true,edit:false,add:false,del:false,search:false,refresh:true});


        myGrid.jqGrid('navButtonAdd', '#' + myGrid[0].id + '_toppager_left', { // "#list_toppager_left"
            caption: "Excel",
            buttonicon: 'myicon',
            onClickButton: function(){
                exportExcel4(myGrid,'<?= $export ?>');
            }
        });

        resize_the_grid(grid);


        var myReload = function() {
            myGrid.setGridParam({ page: 1, datatype: "json" }).trigger('reloadGrid');
        };

        // Recargar el grid en el evento submit del formulario
        $( form ).on( 'submit', function( e ){
            e.preventDefault();
           
            myReload();
            

        });

        //Recargar el grid en el evento onChange del select search del formulario
        $( search).on('change',function(){
            myReload();
        });



    };

    $(window).resize(resize_the_grid);


    function formatLink(cellvalue, options,rowData) {
        if (cellvalue!=null){

        	return('<a href="http://www.amazon.com/gp/offer-listing/'+cellvalue+'/ref=dp_olp_new?ie=UTF8&condition=new" target="_blank">' + cellvalue + '</a>');
        }
        
    }

</script>


