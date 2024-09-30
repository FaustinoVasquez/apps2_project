

<div class="graphicContainter">
    <center>

        <?php
        $formopen = array('id' => "form" , 'class'=>'myform');
        echo form_open(base_url() . 'index.php' . $from, $formopen);
        echo form_label(' Year:', 'tyear');
        $Tyeardropdown = 'id="selectedYear" class="form-select" onChange="this.form.submit()"';
        echo form_dropdown('selectedYear', $TyearOptions, isset($lineselect->selectedYear) ? $lineselect->selectedYear : $this->input->post('selectedYear'), $Tyeardropdown);
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
                text: 'DropShip Orders'
            },
            subtitle: {
                text: 'Comparation By Years',
                style: {
                    font: 'normal 9px Arial, sans-serif'
                }
            },
            xAxis: [{
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
                    'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
            }],
            yAxis: [{ // Primary yAxis
                min: 0,
                labels: {
                    formatter: function() {
                          if (this.value >999){
                            return this.value/1000+'k';
                        }else{
                            return this.value;
                        }
                    },
                    style: {
                        color: '#89A54E'
                    }
                },
                
                title: {
                    text: 'Total Sold($)',
                    style: {
                        color: '#89A54E'
                    }
                },
                opposite: true
            },{ // Secondary yAxis
                gridLineWidth: 0,
                min: 0,
                title: {
                    text: 'Quantity',
                    style: {
                        color: '#4572A7'
                    }
                },
                labels: {
                    formatter: function() {
                        return this.value;
                    },
                    style: {
                        color: '#4572A7'
                    }
                }
            }],
            tooltip: {
                 shared: true    
            },    
            
            credits: {
                enabled: false
            },

            series: [{
                    name: 'Total',
                    type: 'spline',
                    dashStyle: 'shortdot',
                     color: '#89A54E',
                    data: [<?php
                                foreach ($result as $value) {
                                    $data = $value['total'] . ',';

                                    echo $data;
                                }
                                ?>]},
                      {
                      name: 'Quantity',
                      
                     yAxis: 1,
                      type: 'spline',
                      color: '#4572A7',
                            data: [<?php
                                foreach ($result as $value) {
                                    $data = $value['quantity'] . ',';

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
