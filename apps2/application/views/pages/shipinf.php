<style>
    form  { display: table;      }
    p     { display: table-row;  }
    label { display: table-cell; }
    input { display: table-cell; }
    .right { text-align: right; }

    .mcentered {
        position: relative;
        left: 50%;
        margin-left: -100px;
        margin-top: 30px;
    }
    .info, .success, .warning, .error, .validation {
        border: 1px solid;
        margin: 10px 0px;
        padding:15px 10px 15px 50px;
        background-repeat: no-repeat;
        background-position: 10px center;
        text-align: center;
    }
    .info {
        color: #00529B;
        background-color: #BDE5F8;
    }
    .success {
        color: #4F8A10;
        background-color: #DFF2BF;
    }
    .warning {
        color: #9F6000;
        background-color: #FEEFB3;
    }
    .error {
        color: #D8000C;
        background-color: #FFBABA;
    }
</style>
<div class="message"></div>
<div class="mcentered">
    <?php $attributes = array('id' => 'myForm'); ?>
    <?=form_open('login/terminal',$attributes);?>
    <p>
        <?=form_hidden('Id',$result['Id'])?>
    </p>
    <p>
    <?=form_label('CustomsName:', 'customsname')?>
    <?=form_input(array('id'=>'customsname','name'=>'CustomsName','value'=>$result['CustomsName']))?><br />
    </p>
    <p>
    <?=form_label('CustomsValue:', 'customsvalue')?>
    <?=form_input(array('id'=>'customsvalue','name'=>'CustomsValue','value'=>$result['CustomsValue'], 'class'=>'right'))?><br />
    </p>
    <p>
    <?=form_label('BoxSKU:', 'boxSKU')?>
    <?=form_input(array('id'=>'boxSKU','name'=>'BoxSKU','value'=>$result['BoxSKU'], 'class'=>'right'))?><br />
    </p>
    <p>
    <?=form_label('BoxLength:', 'boxLength')?>
    <?=form_input(array('id'=>'boxLength','name'=>'BoxLength','value'=>$result['BoxLength'], 'class'=>'right'))?><br />
     </p>
    <p>
    <?=form_label('BoxWidth:', 'boxWidth')?>
    <?=form_input(array('id'=>'boxWidth','name'=>'BoxWidth','value'=>$result['BoxWidth'], 'class'=>'right'))?><br />
 </p>
    <p>
    <?=form_label('BoxHeight:', 'boxHeight')?>
    <?=form_input(array('id'=>'boxHeight','name'=>'BoxHeight','value'=>$result['BoxHeight'], 'class'=>'right'))?><br />
 </p>
    <p>
    <?=form_label('BoxWeightOz:', 'boxWeightOz')?>
    <?=form_input(array('id'=>'boxWeightOz','name'=>'BoxWeightOz','value'=>$result['BoxWeightOz'], 'class'=>'right'))?><br />
    </p>
    <p>
    <?=form_label('FlatRateOptions:', 'flatRateOptions')?>
    <?=form_input(array('id'=>'flatRateOptions','name'=>'FlatRateOptions','value'=>$result['FlatRateOptions'], 'class'=>'right'))?><br />
    </p>
    <?=form_submit(array('name'=>'passwordsubmit','value'=>'Submit', 'class'=>'right'))?><br />
    <?=form_close();?>
</div>

<script>
    $("#myForm").submit(function(e) {

    var url = "saveData"; // the script where you handle the form input.

    $.ajax({
    type: "POST",
    url: url,
    datatype: 'json',
    data: $("#myForm").serialize(), // serializes the form's elements.
    success: function(data)
    {
        var objData = jQuery.parseJSON(data);
        $('.message').addClass('success');
        $('.message').html(objData.status +':' + objData.message);
        $(".message").show().delay(2000).queue(function(n) {
            $(this).hide(); n();
        });
    }
    });

    e.preventDefault(); // avoid to execute the actual submit of the form.
    });

</script>
