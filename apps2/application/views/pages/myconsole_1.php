

<form id="consoleform" class="myform">
    <fieldset class="bluegradiant">
	<div class="logo"><img longdesc="MI Technologies" title="Mi Tech" alt="43" src="<?= base_url() ?>/images/header/mitechnologies.png">
	</div>
	<div class="form-data"> 
	    <?php
	    $inputsearch = array('name' => 'search', 'value' => '', 'rows' => '5', 'cols' => '90');
	    echo "Search:" . form_textarea($inputsearch);
	    echo "&nbsp;&nbsp;";
	    $submit = array('name' => 'send', 'value' => 'Submit', 'type' => 'submit', 'class' => 'button');
	    echo form_input($submit);
	    $reset = array('name' => 'reset', 'value' => 'Reset', 'type' => 'submit', 'class' => 'button');
	    echo form_input($reset);
	    ?>
	</div>
    </fieldset>
    <br>
</form>
<div class="clear"></div>

<script>

    function resize_the_grid()
    {
        $("<?= '#' . $nameGrid ?>").fluidGrid({base:'#grid_wrapper', offset:-20});
    }
    
    $(document).ready(function(){
	
	
	
	
	$("#consoleform").submit(function(e){
	    e.preventDefault();
       
	    var dataString = $("#consoleform").serialize();
	       
	    if ((dataString.search(/insert/i) !== 0) || (dataString.search(/delete/i) !== 0) || (dataString.search(/delete/i) !== 0) 
		|| (dataString.search(/update/i) !== 0) || (dataString.search(/drop/i) !== 0)){
		  
		$.ajax({
		    type: "POST",
		    dataType: "json",
		    url: "<?= base_url() . 'index.php/Console/myconsole/createGrid' ?>",
		    data: dataString
		}).done(function(data) { $('#grid').append(data.grid); })
		.fail(function() { alert("Error"); });
	   
	    }else{
		Alert('No se permiten Inserciones');
		dataString =""
	    }
	   
	});

    });

</script>



<!--select ID,manufacturer from productcatalog where ID=101001;-->
<div id="grid"></div>

<section id='grid_container'>

    <table id='list'></table>
    <div id='pager'></div>

</section>



