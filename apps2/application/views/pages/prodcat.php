<?php
$formopen = array('id' => "prodcat_form",'class'=>'myform');
echo form_open(base_url() . 'index.php/' . $from, $formopen);
?>
<fieldset class="bluegradiant">
    <div class="logo"><img longdesc='MI Technologies' title='Mi Tech' alt='43' src='<?= base_url() ?>/images/header/mitechnologies.png'>
    </div>
    <div class="form-data"> 
        <?php
        $inputtext = array('name' => 'search', 'value' => $search, 'autocomplete' => 'on', 'maxlength' => '250', 'size' => '35px', 'onChange'=>"javascript:document.forms['editAgendaItem19'].submit()");
        echo "Search:" . form_input($inputtext);
        $dropdown = 'class="form-select" onChange="this.form.submit()"';
        echo form_dropdown('categories', $categoriesOptions, isset($lineselect->categories) ? $lineselect->categories : $this->input->post('categories'), $dropdown);
        echo'<br>';
        echo'<label>';
        $qtyOrderedCheckbox = array('name' => 'qtyOrdered', 'id' => 'qtyOrdered', 'value' => 'TRUE', 'checked' => $qtyOrdered, 'onClick' => "this.form.submit()", 'class' => 'checkbox');
        $doirChekbox = array('name' => 'doir', 'id' => 'doir', 'value' => 'TRUE', 'checked' => $doir, 'onClick' => "this.form.submit()", 'class' => 'checkbox');
        echo form_checkbox($qtyOrderedCheckbox);
        echo' Quantity ordered&nbsp;&nbsp;</label>';
        echo'<label>' . form_checkbox($doirChekbox) . ' DOIR</label>';
        if ($doir) {
            $historyInputText = array('name' => 'historyDays', 'value' => $historyDays, 'autocomplete' => 'on', 'maxlength' => '4', 'size' => '1', 'style' => 'text-align:center;');
            echo form_input($historyInputText);
        }
        echo "&nbsp;&nbsp;";
        $submit = array('name' => 'send', 'value' => 'Submit', 'type' => 'submit', 'class' => 'button');
        echo form_input($submit);
        $reset = array('name' => 'reset', 'value' => 'Reset', 'type' => 'submit', 'class' => 'button');
        echo form_input($reset);
        ?>
    </div>
</fieldset>
<br>

<?php
echo form_close();

$this->load->view('grids/gridprodcat');
?>


<script>
    function formatLink(cellvalue, options,rowData) {
        if (cellvalue!=null){  
            return "<a href=<?= base_url()  . 'index.php/Reports/purchaseorderlist/'?>"+'?q='+ rowData[0]+'&p='+ 0 +'&t='+ 1 +">" + cellvalue + "</a>";
        }  
    }
</script>


<div id="dialog" title="Purcharse Orders"></div>