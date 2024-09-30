<?php
    $formopen = array('id'  => 'target', 'class' => 'myform');
    $inputRetain = array('name' => 'emails','id'=>'retain', 'value' =>'', 'autocomplete' => 'on', 'maxlength' => '250', 'size' => '53px','type'=>'text');
    $inputDelete = array('name' => 'emails','id'=>'deletesku', 'value' =>'', 'autocomplete' => 'on', 'maxlength' => '250', 'size' => '53px','type'=>'text');
    $submit =   array('name' => 'send', 'value' => 'Submit', 'type' => 'submit', 'class' => 'button');
    $reset = array('name' => 'reset', 'value' => 'Reset', 'type' => 'reset', 'class' => 'button');
    echo form_open(base_url(), $formopen);
?>
    <fieldset class="bluegradiant">
        <div class="logo"><img longdesc="MI Technologies" title="Mi Tech" alt="43" src="<?= base_url() ?>/images/header/mitechnologies.png"></div>
        <div class="form-data"> 
            <center>
                <table>
                    <caption><h3>Merge SKU</h3></caption>
                    <tbody>
                        <tr>
                            <td><?=form_label('Retain SKU: ')?></td><td><?=form_input($inputRetain)?></td>
                        </tr>
                        <tr>
                            <td><?=form_label('Delete SKU: ')?>&nbsp;</td><td><?=form_input($inputDetail)?></td>
                        </tr>
                         <tr>
                            <td colspan="2"><center><?=form_input($submit).'&nbsp;'.form_input($reset);?></center></td>
                        </tr>
                    </tbody>
                </table>
            <center>
            <br>
        </div>
    </fieldset>
<br>
<?php echo form_close(); ?> 
<div class="clear"></div>
    
    
<script type="text/javascript">
$(function(){

 $("#target").validate({
    errorElement:'div',
    rules: {
        emails: {
            required: true,
            multiemail:true
        }
    },
    messages: 
    {
        emails: {
            required:"Please enter SKU."
        }
    }
 });

 $( 'form' ).on( 'submit', function( e ){
            e.preventDefault();
            var retain = $("#retain").val();
            var deletesku = $("#deletesku").val();
            var r = prompt("Please Insert Password", "");
            var p = "dLp173Vb"

            if ((retain) && (deletesku))
            {
                if(r == p)
                {
                    var request = $.ajax({
                        url: 'MergerSKU',
                        type: "POST",
                        data: { re:retain, ds:deletesku  },
                        async: false,
                    });
                    alert("The data will be sent to "+email)
                }
                else
                { alert("Wrong Password!") }                
            }
            else
            { alert("Please fill Retaun SKU and Delete SKU") }
});

});
 
       
</script>