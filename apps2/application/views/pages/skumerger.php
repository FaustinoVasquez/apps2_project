<?php
    $formopen = array('id'  => 'target', 'class' => 'myform');
    $inputRetain = array('name' => 'retain','id'=>'retain', 'value' =>'', 'autocomplete' => 'on', 'maxlength' => '250', 'size' => '53px','type'=>'text','style'=>'background-color:#287E26;color:White;font-weight:bold');
    $inputDelete = array('name' => 'deletesku','id'=>'deletesku', 'value' =>'', 'autocomplete' => 'on', 'maxlength' => '250', 'size' => '53px','type'=>'text','style'=>'background-color:#A03434;color:White;font-weight:bold');
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
                            <td><?=form_label('Delete SKU: ')?>&nbsp;</td><td><?=form_input($inputDelete)?></td>
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


 $( 'form' ).on( 'submit', function( e ){
            e.preventDefault();
            var retainSku = $("#retain").val();
            var deleteSku = $("#deletesku").val();

            if ((retainSku) && (deleteSku))
            {
                var r = prompt("Please Insert Password", "");
                var p = "dLp173Vb"

                if(r == p)
                {
                    var request = $.ajax({
                        url: 'MergerSKU',
                        type: "POST",
                        data: { rs:retainSku, dk:deleteSku },
                        async: true,
                    });
                    if(request == true)
                        alert("The Operation Was Successful !")
                    else
                        alert("Please Check the Information")
                }
                else
                { alert("Wrong Password!") }                
            }
            else
            { alert("Please fill Retain SKU and/or Delete SKU") }

            $("#retain").val('').focus();
            $("#deletesku").val('').focus();
});

});
    
</script>