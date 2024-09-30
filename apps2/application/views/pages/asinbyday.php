
<div class="graphicContainter" style="margin-top: 5px;">
    <center>
        <?php
        $formopen = array('id' => "dateNowform" , 'class'=>'myform', 'style' =>'margin-top:3px;');
        echo form_open(base_url() . 'index.php' . $from, $formopen);
        echo form_label('From: ', 'tdate');
        $inputfrom = array('id' => 'dateFrom', 'name' => 'dateFrom', 'class' => 'date-pick', 'value' => $dateFrom, 'size' => '10', 'onchange' => "this.form.submit()", 'style' => 'width:80px;text-align:center');
	echo form_input($inputfrom);
	echo '&nbsp;&nbsp'.form_label('To: ', 'tdate');
	$inputto = array('id' => 'dateTo', 'name' => 'dateTo', 'class' => 'date-pick', 'value' => $dateTo, 'size' => '10', 'onchange' => "this.form.submit()", 'style' => 'width:80px;text-align:center');
	echo form_input($inputto);
	
        $submit = array('name' => 'send', 'value' => 'Go', 'type' => 'submit', 'class' => 'button');
        echo '&nbsp;' . form_input($submit);
        echo form_close();
        ?>
    </center>
    <div id="asinsbyday" style="width: 520px; height: 320px; float:left;margin-left:3px;">
    </div><!-- End demo -->
</div>



<script type="text/javascript">

    $(document).ready(function() {

        Highcharts.setOptions({
            global: {
                useUTC: false
            }
        });
        chart1 = new Highcharts.Chart({
            chart: {
                borderColor:'#cccccc',
                borderWidth:'1',
                renderTo: 'asinsbyday',
                defaultSeriesType: 'line',
                zoomType: 'x',
                spacingRight: 10
            },
            title: {
                text: 'Captured ASINs'
            },
            subtitle: {
                text: 'Period Time',
                style: {
                    font: 'normal 9px Arial, sans-serif'
                }
            },
            xAxis: {
                type: 'datetime',
                maxZoom: 14 * 24 * 3600000, // fourteen days
                title: {
                    text: null
                }
                
            },
            yAxis: {
                title: {
                    text: 'Qty ASINs'
                },
                min: 0.6,
                startOnTick: false,
                showFirstLabel: false
            },
            tooltip: {
                shared: true               
            },
            legend: {
                enabled: false
            },
            plotOptions: {
                area: {
                    fillColor: {
                        linearGradient: [0, 0, 0, 300],
                        stops: [
                            [0, Highcharts.getOptions().colors[0]],
                            [1, 'rgba(2,0,0,0)']
                        ]
                    },
                    lineWidth: 1,
                    marker: {
                        enabled: false,
                        states: {
                            hover: {
                                enabled: true,
                                radius: 5
                            }
                        }
                    },
                    shadow: false,
                    states: {
                        hover: {
                            lineWidth: 1                  
                        }
                    }
                }
            },
            credits: {
                enabled: false
            },
            series: [{
                    type: 'area',
                    name: 'Asin',
		    pointStart: Date.UTC(<?= $year ?>,<?= $month -1 ?>,<?= $day +1 ?>),
                    pointInterval: 24 * 3600 * 1000,
                    
                   
                    data: [<?php foreach ($result as $value) {
			    $data = $value . ',';
			    echo $data;} 
			   ?>
			  ]},
            ]});
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

