
<?php

$total = 0;
foreach ($result as $value) {
  $total += $value['total'];
}

?>
<div class="graphicContainter" >
    <br>
    <div id="salesbyyear" class="graphic"></div>
</div>


<script type="text/javascript">


    var chart;
    $(document).ready(function() {
        chart = new Highcharts.Chart({
            chart: {
                renderTo: 'salesbyyear',
                plotBorderWidth: null,
                plotShadow: false,
                borderColor:'#cccccc',
                borderWidth:'1'
            },
            title: {
                text:'Total Sales By Year'
            },
            subtitle: {
                    text: '<?php echo "From ".$baseyear." To ".$Fyear ?>',
                     style: {
			               font: 'normal 9px Arial, sans-serif'
				     }
                },
            tooltip: {
                formatter: function() {
                   this.y = (this.y * 100)/<?= $total ?>;
                    return '<b>'+ this.point.name +'</b>: '+ Highcharts.numberFormat(this.y, 0, ',')+' %';

                }
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: false
                    },
                    showInLegend: true
                }

            },
            legend: {
                layout: 'vertical',
                align: 'right',
                verticalAlign: 'middle',
                x: -0,
                y: 10,
                // floating: true,
                borderWidth: 1,
                backgroundColor: '#FFFFFF',
                shadow: true,
                labelFormatter: function() {
                    return ''+ this.name+': '+ Highcharts.numberFormat(this.y, 2, '.')+' USD';
                },
                itemStyle: {
                    cursor: 'pointer',
                    color: '#3E576F'

                },
                 style: {
			               font: 'normal 9px Arial, sans-serif'
				     }

            },
            credits: {
                enabled: false
            },
            series: [{
                    type: 'pie',
                    name: 'Browser share',
                    data: [<?php
                                foreach ($result as $value) {
                                    $data = "['" . $value['year1'] . "'," . $value['total'] . '],';
                                    echo $data;
                                }
                                ?>]
                    }]
            });
        });


</script>
