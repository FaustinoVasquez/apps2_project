<html>
    <head>
        <title></title>
        <link rel="stylesheet" type="text/css" href="<?= base_url() ?>css/form.css"> 
        <link rel="stylesheet" type="text/css" href="<?= base_url() ?>css/label.css"> 
        <link rel="stylesheet" type="text/css" href="<?= base_url() ?>css/label_print.css" media="print"> 
        <script src="<?= base_url() ?>js/libs/jquery-1.7.2.min.js" type="text/javascript"></script>
        <script src="<?= base_url() ?>js/libs/jquery.printPage.js" type="text/javascript"></script>
        <script src="<?= base_url() ?>js/libs/jquery-barcode-2.0.2.min.js" type="text/javascript"></script>
        <script src="<?= base_url() ?>js/libs/jquery.jqprint-0.3.js" type="text/javascript"></script>
        <script>  
            $(document).ready(function() {
                $("#barcode").click();
                $("#PrintTicket").click(function (){
                    $("#ticket").jqprint();
                })
            });
            
        </script>
    </head>
    <body>
        <div id="ticket" class="layer">
            <p>
                <b class="hide">Build SKU:</b> <span id="bulidsku" class="hide"><?= $buildsku ?></span></br>
            <hr class="hide">
            <b>Amazon ASIN:</b> <?= $asin ?></br>
            <div id="bcTarget"></div></br> 
            <?= $description ?></br>
        </p>
    </div>

<center><input class="button" id="PrintTicket" value="Print ticket" type="button"/></center>
<input id="barcode" type="hidden" onclick='$("#bcTarget").barcode("<?= $snsku ?>", "code128",{barWidth:2, barHeight:30});' value="Test"/>

</body>
</html>