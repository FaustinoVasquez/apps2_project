

<form id="consoleform" class="myform">
    <fieldset class="bluegradiant">
	<div class="logo"><img longdesc="MI Technologies" title="Mi Tech" alt="43" src="<?= base_url() ?>/images/header/mitechnologies.png">
	</div>
	<div class="form-data"> 
	    <?php
	    $inputsearch = array('id' =>'mysearch', 'name' => 'search', 'value' => '', 'rows' => '5', 'cols' => '90');
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
        $("#list").fluidGrid({base:'#grid_wrapper', offset:-20});
    }
    
    $(document).ready(function(){
		
	$("#consoleform").submit(function(e){
	    e.preventDefault();
            
            if ($('#grid_container').text().length > 0) {
                    $('#grid_container').empty();
                } 
                
            $('#grid_container').append("<table id='list'></table><div id='pager'></div>");
    
	    var dataString = $("#consoleform").serialize();
	       
            var query = $("#mysearch").val();
                                    
	    if (((query.search(/insert/i)) != 0) && (query.search(/delete/i) != 0) && (query.search(/alter/i) != 0) 
		&& (query.search(/update/i) != 0) && (query.search(/drop/i) != 0)){
		  
		$.ajax({
                        type: "POST",
                        dataType: "json",
                        url: "<?= base_url() . 'index.php/Console/myconsole/createGrid' ?>",
                        data: dataString
                       })
                        .done(function(data) {  $('#grid').append(data.grid);  })
                        .fail(function() { alert("There is something wrong with your query"); });
	   
	    }else{
		alert('Some actions defined in the query are not allowed\r\nInsert,Delete,Update,Drop');
		dataString ='';
                query ='';
	    }
	   
	});
        
         resize_the_grid("#list");  
         $(window).resize(resize_the_grid);
      
    });

</script>


<div id="grid"></div>

<section id='grid_container'></section>



