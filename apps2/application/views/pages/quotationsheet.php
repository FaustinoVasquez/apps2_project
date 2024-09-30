
<?php
    $formopen = array('id'  => 'target', 'class' => 'myform');
    $selectCategories = 'class="form-select" id="category" style="width: 400px"';
    $selectSuppliers = 'class="form-select" id="supplier" style="width: 400px"';
    $inputEmail = array('name' => 'emails','id'=>'email', 'value' =>'', 'autocomplete' => 'on', 'maxlength' => '250', 'size' => '53px','type'=>'text');
    $submit =   array('name' => 'send', 'value' => 'Submit', 'type' => 'submit', 'class' => 'button');
    $reset = array('name' => 'reset', 'value' => 'Reset', 'type' => 'reset', 'class' => 'button');
    echo form_open(base_url(), $formopen);
?>
<fieldset class="bluegradiant">
        <div class="logo"><img longdesc="MI Technologies" title="Mi Tech" alt="43" src="<?= base_url() ?>/images/header/mitechnologies.png"></div>
        <div class="form-data"> 
            <center>
                <table>
                    <caption><h3>Send Email Quotation Sheet</h3></caption>
                    <tbody>
                        <tr>
                            <td><?=form_label('Category:')?></td><td><?=form_dropdown('categories', $categories,'0', $selectCategories)?></td>
                        </tr>
                        <tr>
                            <td><?=form_label('Supplier:')?></td><td><?=form_dropdown('suppliers', $suppliers,'0', $selectSuppliers)?></td>
                        </tr>
                        <tr>
                            <td><?=form_label('Email List:')?>&nbsp;</td><td><?=form_input($inputEmail)?></td>
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

<?php echo form_close(); ?> 
	     
    
<div class="clear"></div>
    
    

<script type="text/javascript">
$(function(){
  jQuery.validator.addMethod("multiemail", function (value, element) {
        if (this.optional(element)) {
            return true;
        }
        var emails = value.split(';'),
            valid = true;
        for (var i = 0, limit = emails.length; i < limit; i++) {
            value = emails[i];
            valid = valid && jQuery.validator.methods.email.call(this, value, element);
        }
        return valid;
    }, "<h3>Please separate email addresses with a semicolon (;) and do not use spaces.</h3>");


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
            required:"Please enter email address."
        }
    }
 });

 $( 'form' ).on( 'submit', function( e ){
            e.preventDefault();
            var category = $("#category option:selected").val();
            var supplier = $("#supplier option:selected").val();
            var email = $("#email").val();
            var r = prompt("Please Insert Password", "");
            var p = "dLp173Vb"

            if ((category) && (email))
            {
                if(r == p)
                {
                    var request = $.ajax({
                        url: 'SendEmail',
                        type: "POST",
                        data: { cat:category, supl:supplier, em:email  },
                        async: false,
                    });
                    alert("The data will be sent to "+email)
                }
                else
                { alert("Wrong Password!") }
            }
            else
            { alert("Please fill Category and Supplier") }
});

});
</script>


