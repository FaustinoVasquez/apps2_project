
<?php
    $formopen = array('id' => "myform", 'class' => 'myform');
    $upload = array('name' => 'userfile', 'value' => 'Choose File', 'type' => 'file');
    $submit = array('name' => 'send', 'value' => 'Upload', 'type' => 'submit', 'class' => 'button');
    echo form_open_multipart($from.'do_upload',$formopen);
?>
    <fieldset class="bluegradiant">
        <div class="logo"><img longdesc="MI Technologies" title="Mi Tech" alt="43" src="<?= base_url() ?>/images/header/mitechnologies.png"></div>
        <div class="form-data"> 
            <?php
                echo "<label>Upload File:</label>" ;
                echo form_input($upload).'&nbsp;&nbsp;';
                echo form_input($submit);
            ?>
        </div>
    </fieldset>
<br>
<?php echo form_close(); ?> 
	     
    
<div class="clear"></div>

<div id='log'></div>
     
    
 <script type="text/javascript">
    $(function(){
        $( '#myform' ).on( 'submit', function( e ){
            e.preventDefault();
            var request = $.ajax({
                url: "do_upload",
                type: "POST",
                data:  new FormData(this),
                processData: false,
                contentType: false
            });

            request.done(function( msg ) {
            alert(msg);
           // $( "#log" ).html( msg );
            });
            
        });
    });

    
</script>
