<html>
    <head>
        <title></title>
        <link rel="stylesheet" type="text/css" href="<?= base_url() ?>css/form.css"> 
        <link rel="stylesheet" type="text/css" href="<?= base_url() ?>css/label.css"> 
 <!--        <link rel="stylesheet" type="text/css" href="<?= base_url() ?>css/labelprint.css" media="print">  -->
        <script src="<?= base_url() ?>js/libs/jquery-1.7.2.min.js" type="text/javascript"></script>
        <script src="<?= base_url() ?>js/libs/jquery.printPage.js" type="text/javascript"></script>
        <script src="<?= base_url() ?>js/libs/jquery-barcode-2.0.2.min.js" type="text/javascript"></script>
        <script src="<?= base_url() ?>js/libs/jquery.jqprint-0.3.js" type="text/javascript"></script>
        <script>  
            $(document).ready(function() {
               $("#barcode").click();
                $("#PrintTicket").click(function (){
                    $("#ticket").jqprint();
                    return false;
                })
            });
            
        </script>
    </head>
    <body>
        <input class="button" id="PrintTicket" value="Print Label" type="button"/>
        <br><br>
        <div id="ticket" class="ticket">
            <div id="bcTarget"></div>
        </div>
        <input id="barcode" type="hidden" onclick='$("#bcTarget").barcode("<?= $fnsku ?>", "code128",{barWidth:1, barHeight:20});' value="Test"/>

</body>
</html>