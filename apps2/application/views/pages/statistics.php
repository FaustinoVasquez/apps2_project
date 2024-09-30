
<div class="demo1"  style="width: 524px; height: 344px; float:left;">
    <div class="styled-select">
        <select id="ComboBox">
            <option value="1">Stock Movements</option>
            <option value="2">Average Pricing By Month</option>
            <option value="3">Max Min Price</option>
        </select>
    </div>

    

    <?php
    if ($ytd != '') {

        $TotalMax = $TotalSellMax['quantity'] * $MaxMin['Expensive'];
        $TotalMin = $TotalSellMin['quantity'] * $MaxMin['chip'];


        $AvgLast3months = ceil($AvgLast3months);
        if ($lastMonth == '') {
            $lastMonth = 0;
        }
        if ($thisMonth == '') {
            $thisMonth = 0;
        }
        if ($AvgLast3months == '') {
            $AvgLast3months = 0;
        }




        echo '<div id="Stats' . $search . '"  style="width: 520px; height: 320px; float:left;margin-left:3px;">';
        echo '</div><!-- End demo -->';
    } else {
        echo '<label>This SKU don\'t have Data</lablel>';
    }
    ?>


    <div id="Price<?= $search ?>"  style="width: 520px; height: 320px; float:left;margin-left:3px;">
    </div>

    <div id="MaxMin<?= $search ?>"  style="width: 520px; height: 320px; float:left;margin-left:3px;">
    </div>


</div>



<script>
    
    function displayVals() {
        var singleValues = $("#ComboBox").val();
     
    
        if (singleValues == 1){   
            $("#Stats<?= $search ?>").css("display","block");
            $("#Price<?= $search ?>").css("display","none");
            $("#MaxMin<?= $search ?>").css("display","none");
        }
    
        if (singleValues == 2){
            $("#Stats<?= $search ?>").css("display","none");
            $("#Price<?= $search ?>").css("display","block");
            $("#MaxMin<?= $search ?>").css("display","none");
        }
            
        if (singleValues == 3){
            $("#Stats<?= $search ?>").css("display","none");
            $("#Price<?= $search ?>").css("display","none");
            $("#MaxMin<?= $search ?>").css("display","block");
        }
    
    }
    

    $("#ComboBox").change(displayVals);
    displayVals();
    
</script>




<script type="text/javascript">


    $(document).ready(function() {
        if (typeof _dataChart != "undefined" && typeof _dataChart.destroy == "function") _dataChart.destroy();
        _dataChart = new Highcharts.Chart({
           
            chart: {
                borderColor:'#cccccc',
                borderWidth:'1',
                renderTo: 'Stats<?= $search ?>',
                defaultSeriesType: 'bar'
            },
            title: {
                text: 'Stock Movements'
            },
            subtitle: {
                text: 'SKU: <?= $search ?>',
                style: {
                    font: 'normal 9px Arial, sans-serif'
                }
            },
            xAxis: {
                categories: ['Last 12 Months','<?php echo date('F Y', strtotime('-1 month'));?>','Last 30 Days','Monthly Avg - based<br> on last 90 Days'
                ],
                labels: {
                    rotation: 0,
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
                    align: 'high',
                    showLastLabel: false

                },
                labels: {
                formatter: function() {
                    if (this.value >= 1000){
                       return this.value/1000 +'k'; 
                    }else{
                        return this.value;
                    }
                    
                }
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
                    data: [<?= $ytd . ',' . $lastMonth . ',' . $thisMonth . ',' . $AvgLast3months ?>],
                    dataLabels: {
                        enabled: true
                            
                    }

                }]
        });


    });

</script>




<script type="text/javascript">

    $(document).ready(function() {

        chart1 = new Highcharts.Chart({
            chart: {
                borderColor:'#cccccc',
                borderWidth:'1',
                renderTo: 'Price<?= $search ?>',
                defaultSeriesType: 'line'
            },
            title: {
                text: 'Average Pricing By Month'
            },
            subtitle: {
                text: 'SKU: <?= $search ?>',
                style: {
                    font: 'normal 9px Arial, sans-serif'
                }
            },
            xAxis: {
                categories: [ <?php
                                $i = 0;
                                foreach ($Months as $value) {
                                    $data = "'" . $Months[$i] . "',";
                                    $i++;
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
                        text: 'Quantity',
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
                        name: 'Price',
                        data: [<?php
                                    $i = 1;
                                    foreach ($AvgPricing as $value) {
                                        $data = $AvgPricing[$i] . ',';
                                        $i++;
                                        echo $data;
                                    }
                                    ?>]
                        }]
                });
            });
</script>







<script type="text/javascript">
        var chart;
        $(document).ready(function() {
   
            var colors = Highcharts.getOptions().colors,
            categories = ['Max Price','Min Price','Average sales price'],
            name = 'Max Price - Min Price',
            data = [{ 
                    y: <?= $MaxMin['Expensive'] ?>,
                    color: colors[0]
            
                }, {
                    y: <?= $MaxMin['chip'] ?>,
                    color: colors[1]

                }, {
                    y: <?= number_format($avgPricingTreeMonths, 2, '.', ' ') ?>,
                    color: colors[2]

                }];
   
            function setChart(name, categories, data, color) {
                chart.xAxis[0].setCategories(categories);
                chart.series[0].remove();
                chart.addSeries({
                    name: name,
                    data: data,
                    color: color || 'white'
                });
            }
   
            chart = new Highcharts.Chart({
                chart: {
                    borderColor:'#cccccc',
                    borderWidth:'1',
                    renderTo: 'MaxMin<?= $search ?>', 
                    type: 'column'
                },
                title: {
                    text: 'Max Min Price Last 3 months'
                },
                subtitle: {
                    text: 'SKU: <?= $search ?>'
                },
                xAxis: {
                    categories: categories                     
                },
                yAxis: {
                    title: {
                        text: 'Total Price'
                    }
                },
                plotOptions: {
                    column: {
                        cursor: 'pointer',
                        point: {
                            events: {
                                click: function() {
                                    var drilldown = this.drilldown;
                                    if (drilldown) { // drill down
                                        setChart(drilldown.name, drilldown.categories, drilldown.data, drilldown.color);
                                    } else { // restore
                                        setChart(name, categories, data);
                                    }
                                }
                            }
                        },
                        dataLabels: {
                            enabled: true,
                            color: colors[0],
                            style: {
                                fontWeight: 'bold'
                            },
                            formatter: function() {
                                return this.y +' USD';
                            }
                        }               
                    }
                },
                tooltip: {
                    formatter: function() {
                        var point = this.point,
                        s = this.x +':<b>'+ this.y + ' USD</b><br/>';

                        if (point.category =='Max Price') {
                            s += 'Sold: <b><?= $TotalSellMax['quantity'] ?></b> Total:<b><?= $TotalMax ?> USD</b>';
                        }
                        if (point.category =='Min Price'){
                            s += 'Sold: <b><?= $TotalSellMin['quantity'] ?></b> Total:<b><?= $TotalMin ?> USD</b>';
                        }
                        if (point.category =='Average sales price'){
                            s += '<br />based on last 3 months';
                        }
                        
               
                        return s;
                    }
                },
                credits: {
                    enabled: false
                },
                series: [{
                        name: name,
                        data: data,
                        color: 'white'
                    }],
                exporting: {
                    enabled: false
                }
            });
   
   
        });

</script>
