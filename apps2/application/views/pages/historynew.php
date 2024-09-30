<style type="text/css" media="screen">
    .ui-icon.excel {
        width: 16px;
        height: 16px;
        background-image:url('<?= base_url() ?>images/toolbar/Excel-icon2.png');
    }
</style>



<div class="clear"></div> 
<section id="grid_container">
    <form action='<?= base_url() . 'index.php' . $from .'gridData' ?>' method='POST' style="margin-top: 5px;margin-bottom: 5px;">
        <?php
        $flowAttrib = 'style="font-size:12px" id="flow"';
        $qtyAttrib = 'style="font-size:12px" id="qty"';
        $inputfrom = array('id'=>'from','name' => 'dateFrom', 'class' => 'date-pick', 'value' => $dateFrom, 'size' => '10','style'=>'width:80px;text-align:center');
        $inputto = array('id'=>'to','name' => 'dateTo', 'class' => 'date-pick', 'value'=>$dateTo,'size'=>'10','style'=>'width:80px;text-align:center');
        $inputUser = array('id'=>'user','name'=>'user','value' =>'','autocomplete'=>'on','maxlength'=>'250','size'=>'15px');
        $flowOptions = array('0'=>'All','1'=>'Added','2'=>'Removed','3'=>'Transferred');
        $qtyOptions = array('0'=>'All','2'=>'>=2','5'=>'>=5','10'=>'>=10','20'=>'>=20','50'=>'>=50','100'=>'>=100','500'=>'>=500','1000'=>'>=1000');
        $inputComment = array('id'=>'comments','name'=>'comments', 'value' => '', 'autocomplete' => 'on', 'maxlength' => '250', 'size' => '20px');
        $inputBin = array('id'=>'bin','name'=>'bin','value' => '', 'autocomplete' => 'on', 'maxlength' =>'250','size'=>'15px');
        $inputTransaction = array('id'=>'transaction','name'=>'transaction','value'=>'','autocomplete'=>'on','maxlength'=>'250','size'=>'15px');

        echo '<label>From:</label>'.form_input($inputfrom);
        echo '&nbsp;&nbsp;';
        echo '<label>To:</label>'.form_input($inputto);
        echo '&nbsp;&nbsp;';
        echo '<label>User:</label>'.form_input($inputUser);
        echo '&nbsp;&nbsp;';
        echo '<label>Movement:</label>'.form_dropdown('flow', $flowOptions, '0',$flowAttrib);
        echo '&nbsp;&nbsp;';
    	echo '<label>Qty:</label>'.form_dropdown('qty', $qtyOptions, '99999',$qtyAttrib);
        echo '&nbsp;&nbsp;';
        echo '<label>Comments:</label>'.form_input($inputComment);
        echo '&nbsp;&nbsp;';
        echo '<label>BinID:</label>'.form_input($inputBin);
        echo '&nbsp;&nbsp;';
        echo '<label>Transaction:</label>'.form_input($inputTransaction);
    	?>   
    	<input type='submit' class='button' value='Submit!'>
    </form>

    <table id="list"></table>
    <div id="pager"></div>
</section>


<script type="text/javascript">
    
    function resize_the_grid()
    {
        $("#list").fluidGrid({base:'#grid_wrapper', offset:-20});
    }
     
     $(function(){
         var myGrid= $("#list"),
            pagerSelector = "#pager", 
            myAddButton = function(options) {
                myGrid.jqGrid('navButtonAdd',pagerSelector,options);
                myGrid.jqGrid('navButtonAdd','#'+myGrid[0].id+"_toppager",options);
            };
      
        myGrid.jqGrid({
            url:'gridData',
            datatype: "json",
            postData: {
            sku: <?=$search?>,
            from:   function() { return jQuery("#from").val(); },
            to:   function() { return jQuery("#to").val(); },
            user:   function() { return jQuery("#user").val(); },
            flow:  function() { return jQuery("#flow option:selected").val(); },
            qty:  function() { return jQuery("#qty option:selected").val(); },
            comments: function() { return jQuery("#comments").val(); },
            bin:   function() { return jQuery("#bin").val(); },
            transaction:   function() { return jQuery("#transaction").val(); },
            },
            colNames:<?= $headers ?>,
            colModel:<?= $body ?>,
            height:250,
            autowidth: true,
            pager: '#pager',
            rowNum: 1000,
            rowList: [1000,1500,2000],
            rownumbers: true,
            sortname: 'Adjustment_Id',
            sortorder: 'desc',
            viewrecords: true,
            caption: "<?= $caption ?>",
	        toppager:true,
        });
        jQuery("#list").jqGrid('navGrid','#pager',{cloneToTop:true,edit:false,add:false,del:false,search:false,refresh:true});
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
    $( "#from" ).on('change',function(){
       myReload();
    });


     //Recargar el grid en el evento onChange del select search del formulario
    $( "#to" ).on('change',function(){
       myReload();
    });

    //Recargar el grid en el evento onChange del select search del formulario
    $( "#user" ).on('change',function(){
       myReload();
    });

    //Recargar el grid en el evento onChange del select categories del formulario
    $( "#flow" ).on('change',function(){
        myReload();
    });

     //Recargar el grid en el evento onChange del select categories del formulario
    $( "#qty" ).on('change',function(){
        myReload();
    });

    //Recargar el grid en el evento onChange del select search del formulario
    $( "#comments" ).on('change',function(){
       myReload();
    });

      //Recargar el grid en el evento onChange del select search del formulario
    $( "#bin" ).on('change',function(){
       myReload();
    });

      //Recargar el grid en el evento onChange del select search del formulario
    $( "#transaction" ).on('change',function(){
       myReload();
    });

    });
    
    $(window).resize(resize_the_grid);
        
</script>

<script type="text/javascript">   
    $(function() {
        $( ".date-pick" ).datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat:"mm/dd/yy"
        });
    });   
         
</script>
