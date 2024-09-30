

<div class="graphicContainter">
    <center>

        <?php
        $formopen = array('id' => "form" , 'class'=>'myform');
        echo form_open(base_url() . 'index.php' . $from, $formopen);
        echo form_label(' Year:', 'tyear');
        $Tyeardropdown = 'id="selectedTyear" class="form-select" onChange="this.form.submit()"';
        echo form_dropdown('selectedTyear', $TyearOptions, isset($lineselect->selectedTyear) ? $lineselect->selectedTyear : $this->input->post('selectedTyear'), $Tyeardropdown);
        echo form_label(' Year:', 'fyear');
        $Fyeardropdown = 'id="selectedFyear" class="form-select" onChange="this.form.submit()"';
        echo form_dropdown('selectedFyear', $FyearOptions, $selectedFyear, $Fyeardropdown);
        echo form_label(' Cart:', 'cart');
        $cartdropdown = 'id="selectedcart" class="form-select" onChange="this.form.submit()"';
        echo form_dropdown('selectedcart', $cartOptions, isset($lineselect->selectedcart) ? $lineselect->selectedcart : $this->input->post('selectedcart'), $cartdropdown);

        $submit = array('name' => 'send', 'value' => 'Go', 'type' => 'submit', 'class' => 'button');
        echo '&nbsp;' . form_input($submit);

        form_hidden('graph', '2');

        echo form_close();
        ?>
    </center>
    <div id="salesbymonth" style="width: 520px; height: 320px; float:left;margin-left:3px;">
    </div>
</div>



<script type="text/javascript">

    $(document).ready(function() {

        chart1 = new Highcharts.Chart({
            chart: {
                borderColor:'#cccccc',
                borderWidth:'1',
                renderTo: 'salesbymonth',
                defaultSeriesType: 'line'
            },
            title: {
                text: 'Sales By Month '+'<?= $selectedTyear ?>'+' & '+'<?= $selectedFyear ?>'
            },
            subtitle: {
                text: 'Comparation By Years',
                style: {
                    font: 'normal 9px Arial, sans-serif'
                }
            },
            xAxis: {
                categories: [
                    <?php
                    foreach ($result as $value) {
                        $data = "['" . $value['mymonth'] . "'" . '],';
                        echo $data;
                    }
                    ?>
                ],
                labels: {
                    rotation: 0,
                    align: 'right',
                    style: {
                        font: 'normal 10px Arial, sans-serif'
                    }
                }

            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Sales ($)',
                    align: 'high'
                }
            },


            tooltip: {
                formatter: function() {   
                    return '<b>'+ this.series.name +'</b>: '+ Highcharts.numberFormat(this.y, 2, '.')+' USD';

                }
                    
            },
                
                
            plotOptions: {
                bar: {
                    dataLabels: {
                        enabled: true
                    }
                }
            },
                
            credits: {
                enabled: false
            },
            series: [{
                    name: '<?= $selectedTyear ?>',
                    data: [<?php
                                foreach ($result as $value) {
                                    $data = $value['total'] . ',';

                                    echo $data;
                                }
                                ?>]},
                        {
                            name: '<?= $selectedFyear ?>',
                            data: [<?php
                                foreach ($result1 as $value) {
                                    $data = $value['total'] . ',';

                                    echo $data;
                                }
                                ?>]
                            }]
                    });


                });



                //  jQuery('.date-pick').datepicker({dateFormat:"yy-mm-dd"});
                $(function() {
                    $( ".date-pick" ).datepicker({
                        changeMonth: true,
                        changeYear: true,
                        dateFormat:"mm/dd/yy"
                    });
                });





</script>
