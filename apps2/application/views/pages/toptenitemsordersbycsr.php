
<div class="graphicContainter">
    <center>
        <?php
        $formopen = array('id' => "itorbsform" , 'class'=>'myform');
        echo form_open(base_url() . 'index.php' . $from, $formopen);

        echo form_label(' From:', 'from');
        $inputto = array('id' => 'dateFrom', 'name' => 'dateFrom', 'class' => 'date-pick', 'value' => $dateFrom, 'size' => '10', 'onchange' => "this.form.submit()", 'style' => 'width:80px;text-align:center');
        echo form_input($inputto);

        echo form_label(' To:', 'to');
        $inputto = array('id' => 'dateTo', 'name' => 'dateTo', 'class' => 'date-pick', 'value' => $dateTo, 'size' => '10', 'onchange' => "this.form.submit()", 'style' => 'width:80px;text-align:center');
        echo form_input($inputto);

        $submit = array('name' => 'send', 'value' => 'Go', 'type' => 'submit', 'class' => 'button');
        echo '&nbsp;' . form_input($submit);
        echo form_close();
        ?>

    </center>
    <div id="OrdersItems" style="width: 520px; height: 320px; float:left;margin-left:3px;">
    </div>
</div>



<script type="text/javascript">


    var chart1;
    $(document).ready(function() {

        chart1 = new Highcharts.Chart({
            chart: {
                borderColor:'#cccccc',
                borderWidth:'1',
                renderTo: 'OrdersItems',
                defaultSeriesType: 'column'
            },
            title: {
                text: 'Items & Orders by CSR-MX'
            },
            subtitle: {
                text: '<?php echo "From " . $dateFrom . " To " . $dateTo ?>',
                style: {
                    font: 'normal 11px Arial, sans-serif'
                }
            },
            xAxis: {
                categories: [
                    <?php
                    foreach ($result as $value) {
                        $data = "['" . $value['EnteredBy'] . "'" . '],';
                        echo $data;
                    }
                    ?>
                    ],
                    labels: {
                        rotation: -45,
                        align: 'right',
                        style: {
                            font: 'normal 9px Arial, sans-serif'
                        }
                    }

                },
                yAxis: {
                    min: 0,
                    title: {
                        text: 'Quantity',
                        align: 'high'
                    }
                },


                tooltip: {
                    formatter: function() {
                        return ''+
                            this.series.name +': '+ this.y ;
                    }
                },
                plotOptions: {
                    bar: {
                        dataLabels: {
                            enabled: true
                        }
                    }
                },
                legend: {
                    layout: 'vertical',
                    backgroundColor: '#FFFFFF',
                    align: 'right',
                    verticalAlign: 'top',
                    x: 0,
                    y: 70,
                    floating: true,
                    shadow: true

                },
                credits: {
                    enabled: false
                },
                series: [{
                        name: 'Orders',
                        data: [<?php
                                foreach ($result as $value) {
                                    $data = $value['OrderCount'] . ',';
                                    echo $data;
                                }
                                ?>
                                ],
                    dataLabels: {
                        enabled: true,
                        rotation: -90,
                        color: 'blue',
                        align: 'right',
                        x: -0,
                        y: -20,
                        formatter: function() {
                            return this.y;
                        },
                        style: {
                            font: 'normal 8px Verdana, sans-serif'
                        }
                    }

                }, {
                    name: 'Items',
                    data: [<?php
                            foreach ($items as $value) {
                                $data = $value['items'] . ',';
                                echo $data;
                            }
                            ?>],
                    dataLabels: {
                        enabled: true,
                        rotation: -90,
                        color: 'red',
                        align: 'right',
                        x: -0,
                        y: -20,
                        formatter: function() {
                            return this.y;
                        },
                        style: {
                            font: 'normal 8px Verdana, sans-serif'
                        }
                    }

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


