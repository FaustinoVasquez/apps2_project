
<?php
    $formopen = array('id'  => 'target', 'id' => "omc_form", 'class' => 'myform');
    $submit =   array('name' => 'send', 'value' => 'UnLock', 'type' => 'submit', 'class' => 'button');
    echo form_open(base_url(), $formopen);
?>
    <fieldset class="bluegradiant">
        <div class="logo"><img longdesc="MI Technologies" title="Mi Tech" alt="43" src="<?= base_url() ?>/images/header/mitechnologies.png"></div>
        <div class="form-data"> 
            <?php
                echo '<input type="hidden" name="id" value="" id="id">';
                echo "Press button to unLock Database Process :" ;
                echo "&nbsp;&nbsp;";
                echo form_input($submit);
            ?>
        </div>
    </fieldset>
<br>
<?php echo form_close(); ?> 
	     
    
<div class="clear"></div>
    
    
    
<script type="text/javascript">

    $(function(){

        $( 'form' ).on( 'submit', function( e ){
            e.preventDefault();
            var postData = $(this).serializeArray();

            alert("This action kill all processes that have more than 5 minutes of running on the database, are you sure?")

            var request = $.ajax({
                url: "Data",
                type: "POST",
                data: postData,
            });
            
        });
    });

    
</script>


