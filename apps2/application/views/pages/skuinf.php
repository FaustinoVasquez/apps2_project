<!doctype html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

        <?php /* codeigniter-boilerplate: Page Title ****************************** */ ?>
        <?php if ($title): ?>
            <?php /* Page title: used if the page object has a title */ ?>
            <title><?php echo $title ?></title>
        <? else: ?>
            <?php /* Generic site title: used if the page object has not a title */ ?>
            <title>__YOUR SITE TITLE HERE__</title>
        <?php endif; ?>

        <?php foreach ($css as $c): ?>
            <link rel="stylesheet" href="<?php echo base_url() ?>css/<?php echo $c ?>">
        <?php endforeach; ?>  
        <script>window.jQuery || document.write('<script src="<?php echo base_url() ?>js/libs/jquery-1.7.2.min.js"><\/script>')</script>            
        <?php foreach ($javascript as $js): ?>
            <script defer src="<?php echo base_url() ?>js/<?php echo $js ?>"></script>
        <?php endforeach; ?>


        <script type="text/javascript">
            $(function() {
                $("#<?= $row_id ?>").tabs();
            });
        </script>
        <style>
            #tabs{font-size: 13px;}
        </style>
    </head>
    <body>

        <div class="headersku"><h1 class="h1title" style="line-height:50px;">SKU: <?php echo $sku?></h1></div>
       
        <div>
            <section id="tabs">
                <div id="<?= $row_id ?>" style="height:800px;">
                    <ul>
                        <li><a href="#<?= $row_id ?>-1">SKU Data</a></li>
                        <li><a href="#<?= $row_id ?>-2">Custom Fields</a></li>
                        <li><a href="#<?= $row_id ?>-3">Compatibilities</a></li>
                        <li><a href="#<?= $row_id ?>-4">Images</a></li>
                        <li><a href="#<?= $row_id ?>-5">Attachments</a></li>
                        <li><a href="#<?= $row_id ?>-6">History</a></li>
                        <li><a href="#<?= $row_id ?>-7">Inventory Details</a></li>
                        <li><a href="#<?= $row_id ?>-8">Assembly Requirements</a></li>
                        <?php
                        //  if ($this->MUsers->isValidUser($this->session->userdata('userid'), 881120) == 1) {
                        //      echo '<li><a href="#'.$row_id.'-9">Suppliers Info</a></li>';
                        //   }
                        ?>
                        <li><a href="#<?= $row_id ?>-9">PO History</a></li>

                    </ul>
                    <div id="<?= $row_id ?>-1">


                        <?php
                        $skuData['AssemblyRequired'] = $skuData['AssemblyRequired'] ? "Yes" : "No";
                        $skuData['Serialized'] = trim($skuData['Serialized']) ? "Yes" : "No";
                        $skuData['IsSellable'] = trim($skuData['IsSellable']) ? "Yes" : "No";



                        $itemByLine = 6;

                        $fields = array();
                        $information = array();
                        foreach ($skuData as $indice => $actual) {
                            $fields[] = $indice;
                            $information[] = $actual;
                        }



                        $custom = array();
                        foreach ($customfields as $actual1) {
                            $custom[] = $actual1;
                        }
                        ?>
                        <center>
                            <table class="skudata">
                                <th colspan=6 >Main Data</th>
                                <?php
                                $flag = 0;
                                for ($i = 0; $i < 26; $i++) {

                                    if ($fields[$i] == 'Name' or $fields[$i] == 'Description' or $fields[$i] == 'Keywords') {

                                        $information[$i] = trim($information[$i]) ? $information[$i] : "<br>";

                                        echo '<TR class="alt"><TD COLSPAN=' . $itemByLine . '><P><B>' . $fields[$i] . ':</B></P></TD></TR>';
                                        echo '<TR><TD COLSPAN=' . $itemByLine . '><P>' . $information[$i] . '</P></TD></TR>';
                                    } else {
                                        echo'<TR class="alt">';
                                        $output = array_slice($fields, $i, $itemByLine);
                                        foreach ($output as $dato) {

                                            echo '<TD><P><B>' . $dato . ':</B></P></TD>';
                                        }
                                        echo'</TR><TR>';

                                        $output = array_slice($information, $i, $itemByLine);
                                        foreach ($output as $dato) {

                                            echo '<TD><P>' . $dato . '</P></TD>';
                                        }
                                        echo'</TR>';
                                        $i+=5;
                                    }
                                }
                                ?>
                            </table>
                        </center>
                    </div>
                    <div id="<?= $row_id ?>-2">
                        <center>
                            <TABLE class="skudata" >
                                <th colspan=6 >Custom Fields</th>       
                                <?php
                                for ($i = 27; $i < count($fields); $i++) {

                                    if (!trim($information[$i]) == "") {
                                        echo '<TR class="alt"><TD><P ALIGN=LEFT><B>' . $custom[$i - 26] . '</TD>';
                                        echo '<TD><P ALIGN=LEFT>' . $information[$i] . '</P></TD>';
                                        $i++;
                                    }

                                    if (!trim($information[$i]) == "") {
                                        echo '<TD><P ALIGN=LEFT><B>' . $custom[$i - 26] . '</TD>';
                                        echo '<TD><P ALIGN=LEFT>' . $information[$i] . '</P></TD>';
                                        $i++;
                                    }

                                    if (!trim($information[$i]) == "") {
                                        echo '<TD><P ALIGN=LEFT><B>' . $custom[$i - 26] . '</TD>';
                                        echo '<TD><P ALIGN=LEFT>' . $information[$i] . '</P></TD><TR>';
                                    }
                                }
                                ?>
                            </TABLE>
                        </center>
                    </div>
                    <div id="<?= $row_id ?>-3">
                        <iframe src ="<?= base_url() . 'index.php/Tabs/compat?q=' . $skuNumber ?>" width="100%" height=730px frameborder="0">
                        </iframe>
                    </div>

                    <div id="<?= $row_id ?>-4">
                        <iframe src ="http://photos.discount-merchant.com/photos/sku/<? echo $skuNumber ?>/index.php" width="100%" height=730px frameborder="0">
                        </iframe>
                    </div>

                    <div id="<?= $row_id ?>-5">
                        <iframe src ="http://photos.discount-merchant.com/photos/sku/<? echo $skuNumber ?>/Attachments/" width="100%" height=730px frameborder="0">
                        </iframe>
                    </div>

                    <div id="<?= $row_id ?>-6">
                        <iframe src ="<?= base_url() . 'index.php/Tabs/history/' . $skuNumber ?>" style="width:100%;height:730px;  border-width:0px;">
                        </iframe>
                    </div>

                    <div id="<?= $row_id ?>-7">
                        <iframe src ="<?= base_url() . 'index.php/Tabs/invdetails?q=' . $skuNumber ?>" style="width:100%;height:730px; border-width:0px;">
                        </iframe>
                    </div>

                    <div id="<?= $row_id ?>-8">
                        <iframe src ="<?= base_url() . 'index.php/Tabs/assemblyreq?q=' . $skuNumber ?>" style="width:100%;height:730px; border-width:0px;">
                        </iframe>
                    </div>
                    <!--
                    < ?
                       //     if ($this->MUsers->isValidUser($this->session->userdata('userid'), 881120) == 1) {
                       //         echo '<div id="'.$row_id .'-9">';
                        //        echo '<iframe src ="'.base_url() . 'index.php/Tabs/vendorfpcomp/' . $skuNumber.'" style="width:100%;height:400px; border-width:0px;">';
                        //        echo '</iframe>';
                         //       echo '</div>';
                            } ? >
                    -->
                    <div id="<?= $row_id ?>-9">
                        <iframe src ="<?= base_url() . 'index.php/Tabs/pohistory/' . $skuNumber ?>" style="width:100%;height:730px; border-width:0px;">
                        </iframe>
                    </div>
                </div>
            </section>
        </div>   
    </body>
</html>
