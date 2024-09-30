<?php
$total = 0;
foreach ($result as $value) {
  $total += $value['OrderCount'];
}
?>

<div class="graphicContainter">
    <center>
          <?php
        $formopen = array('id' => "salesbyspform" , 'class'=>'myform');
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
    <div id="salesbystore" style="width: 520px; height: 320px; float:left;margin-left:3px;"></div>
</div><!-- End demo -->


<script type="text/javascript">


    var chart;
    $(document).ready(function() {
        chart = new Highcharts.Chart({
            chart: {
                renderTo: 'salesbystore',
                plotBorderWidth: null,
                plotShadow: false,
                borderColor:'#cccccc',
                borderWidth:'1'
            },
            title: {
                text:'Sales By SalesPerson'
            },
            subtitle: {
                    text: '<?php echo "From ".$dateFrom." To ".$dateTo ?>',
                     style: {
			               font: 'normal 11px Arial, sans-serif'
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
                                    $data = "['" . $value['EnteredBy'] . "'," . $value['OrderCount'] . '],';
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
