

<div class="graphicContainter">
    <center>
           <?php
        $formopen = array('id' => "itemsordersform");
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
    </div><!-- End demo -->
</div>



<script type="text/javascript">

 
        var chart1;
        $(document).ready(function() {
     
            chart1 = new Highcharts.Chart({
                chart: {
                    borderColor:'#cccccc',
                    borderWidth:'1',
                    renderTo: 'OrdersItems',
                     zoomType: 'xy'
                },
                title: {
                    text: 'Dropship orders by top 10. Most Popular SKUs'
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
			      rotation: -35,
			      align: 'right',
			      style: {
			               font: '11px normal Helvetica,Arial,sans-serif'
				     }
			    }

                },
                yAxis: [{ // Primary yAxis
                labels: {
                    min: 0,
                   
                    style: {
                        color: '#4572A7'
                    }
                },
                title: {
                    text: 'Quantity',
                    style: {
                        color: '#4572A7'
                    }
                },
                opposite: true
    
            }, { // Secondary yAxis
                gridLineWidth: 0,
                min: 0,
                title: {
                    text: 'Total Sold',
                    style: {
                        color: '#89A54E'
                    }
                },
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
                }
    
            }, { // Tertiary yAxis
                gridLineWidth: 0,
                title: {
                    text: 'Avg Sales Price ($)',
                    style: {
                        color: '#AA4643'
                    }
                },
                labels: {
                    formatter: function() {
                          if (this.value >999){
                            return this.value/1000+'k';
                        }else{
                            return this.value;
                        }
                    },
                    style: {
                        color: '#AA4643'
                    }
                },
                opposite: true
            }],


                tooltip: {
                     shared: true
                },
                
               
                credits: {
                    enabled: false
                },
                series: [{
                        name: 'SoldTotal',
                        yAxis: 1,
                        type: 'column',
                        color: '#89A54E',
                        data: [<?php
                                foreach ($result as $value) {
                                    $data = $value['SoldTotal'] . ',';
                                    echo $data;
                                }
                                ?>],
                       
                            dashStyle: 'shortdot',
                        tooltip: {
                            valueSuffix: ' US'
                            }

                        },{
                         name: 'AVG Sales Price',
                         color: '#AA4643',
                         type: 'spline',
                          yAxis: 2,
                        data: [<?php
                                foreach ($result as $value) {
                                    $data = $value['avgprice'] . ',';
                                    echo $data;
                                }
                                ?>
                        ],
                         tooltip: {
                            valueSuffix: ' US'
                            }
                     
                    },{
                         name: 'Quantity',
                         color: '#4572A7',
                         type: 'spline',
                        data: [<?php
                                foreach ($result as $value) {
                                    $data = $value['QtyOrdered'] . ',';
                                    echo $data;
                                }
                                ?>
                        ],
                     
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




