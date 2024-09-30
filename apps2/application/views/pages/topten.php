

<div class="graphicContainter" >
    <center>
        
          <?php
        $formopen = array('id' => "toptenform" , 'class'=>'myform');
        echo form_open(base_url() . 'index.php' . $from, $formopen);
        $label = array('style'=>'font-size:10px');
        echo form_label(' From:', 'from', $label);
        $inputto = array('id' => 'dateFrom', 'name' => 'dateFrom', 'class' => 'date-pick', 'value' => $dateFrom, 'size' => '10', 'onchange' => "this.form.submit()", 'style' => 'width:60px;text-align:center;font-size:10px;');
        echo form_input($inputto);

        echo form_label(' To:', 'to' , $label);
        $inputto = array('id' => 'dateTo', 'name' => 'dateTo', 'class' => 'date-pick', 'value' => $dateTo, 'size' => '10', 'onchange' => "this.form.submit()", 'style' => 'width:60px;text-align:center;font-size:10px;');
        echo form_input($inputto);
        $dropdown = 'class="form-select" onChange="this.form.submit()"';
        echo ' '.form_dropdown('productLines', $productLineOptions, isset($lineselect->productLines) ? $lineselect->productLines : $this->input->post('productLines'), $dropdown);

        $submit = array('name' => 'send', 'value' => 'Go', 'type' => 'submit', 'class' => 'button');
        echo '&nbsp;' . form_input($submit);
        echo form_close();
        ?>
    </center>
    <div id="topten" style="width: 520px; height: 320px; float:left;margin-left:3px;">
    </div><!-- End demo -->
</div>



<script type="text/javascript">


        var chart2;
        $(document).ready(function() {

            chart1 = new Highcharts.Chart({
                chart: {
                    borderColor:'#cccccc',
                    borderWidth:'1',
                    renderTo: 'topten',
                    defaultSeriesType: 'bar'
                },
                title: {
                    text: 'TopTen SKU '
                },
                subtitle: {
                    text: '<?php echo "From ".$dateFrom." To ".$dateTo ?>',
                     style: {
			               font: 'normal 11px Arial, sans-serif'
				     }
                },
                xAxis: {
                    categories: [
                                    <?php
                                    foreach ($result as $value) {
                                        $data = "['" . $value['sku'] . "'" . '],';
                                        echo $data;
                                    }
                                    ?>
                    ],
                    labels: {
			     // rotation: -45,
			      align: 'right',
			      style: {
			               font: 'bold 12px Arial, sans-serif'
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
                    x: -10,
                    y: 200,
                    floating: true,
                    shadow: true

                },
                credits: {
                    enabled: false
                },
                series: [{
                        name: 'Qty',
                        data: [<?php
                                foreach ($result as $value) {
                                    $data = $value['qty'] . ',';
                                    echo $data;
                                }
                                ?>],
                            dataLabels: {
                                enabled: true
                            
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






