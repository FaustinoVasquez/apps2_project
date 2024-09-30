
<?php
$formopen = array('id' => 'target', 'class' => 'myform');
$inputsearch = array('name' => 'search', 'value' => '', 'autocomplete' => 'on', 'maxlength' => '250', 'size' => '20', 'onclick' => "this.value=''");
$submit = array('name' => 'send', 'value' => 'Submit', 'type' => 'submit', 'class' => 'button');
$reset = array('name' => 'reset', 'value' => 'Reset', 'type' => 'submit', 'class' => 'button');


echo form_open(base_url(), $formopen);
?>
<fieldset class="bluegradiant">
    <div class="logo"><img longdesc="MI Technologies"
                           title="Mi Tech" alt="43" src="<?= base_url() ?>/images/header/mitechnologies.png">
    </div>
    <div class="form-data"> 
        <?php
        echo "Search:" . form_input($inputsearch);
        echo "&nbsp;&nbsp;";
        echo form_input($submit);
        echo form_input($reset);
        ?>
    </div>
</fieldset>
<br>
<?php
        echo form_close();
?> 
	    

<script type="text/javascript">
    
$(function() {
    $('#target').submit(function(e) {
        e.preventDefault(); 

        var form = $(this);
        $.ajax({
            type:'POST',
            url: 'Data',
            data: form.serialize()
        }).done(function() {
            alert('listo')
        }).fail(function() {
            alert ('error');
        });
});
   });
</script>


