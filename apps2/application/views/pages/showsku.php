

<script type="text/javascript">
    $(function() {
        $( "#<?= $row_id ?>" ).tabs();
    });
</script>



<section id="tabs">
    <div id="<?= $row_id ?>">
        <ul>
            <li><a href="#<?= $row_id ?>-1">SKU Data</a></li>
            <li><a href="#<?= $row_id ?>-2">Custom Fields</a></li>
            <li><a href="#<?= $row_id ?>-3">Shipping Information</a></li>
            <li><a href="#<?= $row_id ?>-4">Compatibilities</a></li>
            <li><a href="#<?= $row_id ?>-5">Images</a></li>
            <li><a href="#<?= $row_id ?>-6">Attachments</a></li>
            <li><a href="#<?= $row_id ?>-7">History</a></li>
            <li><a href="#<?= $row_id ?>-8">Inventory Details</a></li>
            <li><a href="#<?= $row_id ?>-9">Inventory Analytics</a></li>

            <li><a href="#<?= $row_id ?>-10">Assembly Requirements</a></li>
            <li><a href="#<?= $row_id ?>-11">vQOH Details</a></li>
            <li><a href="#<?= $row_id ?>-12">Cost Info</a></li>
            <?php
          //  if ($this->MUsers->isValidUser($this->session->userdata('userid'), 881120) == 1) {
          //      echo '<li><a href="#'.$row_id.'-9">Suppliers Info</a></li>';
         //   }
            ?>
	    <li><a href="#<?= $row_id ?>-13">PO History</a></li>
        <!--<li><a href="#<?= $row_id ?>-14">Advanced</a></li> -->

        </ul>
        <div id="<?= $row_id ?>-1"> 


            <?php
            $skuData['AssemblyRequired'] = $skuData['AssemblyRequired'] ? "Yes" : "No";
            $skuData['Serialized'] = trim($skuData['Serialized']) ? "Yes" : "No";
            $skuData['IsSellable'] = trim($skuData['IsSellable']) ? "Yes" : "No";
            $skuData['AlwaysInStock'] = trim($skuData['AlwaysInStock']) ? "Yes" : "No";



 
            $itemByLine = 5;

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
                    for ($i = 0; $i < 30; $i++) {

                        if ($fields[$i] == 'Name' or $fields[$i] == 'Description' or $fields[$i] == 'UpcCode' or $fields[$i] == 'Keywords' or $fields[$i] == 'UnitDim (OLD)' or $fields[$i] == 'UnitWeight (OLD)' ) {

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

                          $i = 0;
                          echo '<TR class="alt">';
                          foreach ($customfields as $key => $value) {
 
                            if (!trim($skuData[$key]) == "") {
                                echo '<TD><P ALIGN=LEFT><B>' . $customfields[$key] . '</TD>';
                                echo '<TD><P ALIGN=LEFT>' . $skuData[$key] . '</P></TD>';
                                $i++;
                            }

                            if ($i==3){
                                echo '</TR>'; 
                                $i=0;
                            }

                          }
                        ?>
                </TABLE>
            </center>
        </div>

        <div id="<?= $row_id ?>-3">
            <iframe src ="<?= base_url() . 'index.php/Tabs/shipinf/' . $skuNumber ?>" width="100%" height=400px frameborder="0">
            </iframe>
        </div>

        <div id="<?= $row_id ?>-4">
            <iframe src ="<?= base_url() . 'index.php/Tabs/compat?q=' . $skuNumber ?>" width="100%" height=400px frameborder="0">
            </iframe>
        </div>

        <div id="<?= $row_id ?>-5">
            <iframe src ="http://photos.discount-merchant.com/photos/sku/<?=$skuNumber ?>/index.php" width="100%" height=410px frameborder="0">
            </iframe>
        </div>

        <div id="<?= $row_id ?>-6">
            <iframe src ="http://photos.discount-merchant.com/photos/sku/<?=$skuNumber ?>/Attachments/" width="100%" height=410px frameborder="0">
            </iframe>
        </div>

        <div id="<?= $row_id ?>-7">
            <iframe src ="<?= base_url() . 'index.php/Tabs/history/' . $skuNumber ?>" style="width:100%;height:410px;  border-width:0px;">
            </iframe>
        </div>

        <div id="<?= $row_id ?>-8">
            <iframe src ="<?= base_url() . 'index.php/Tabs/invdetailsnew/' . $skuNumber ?>" style="width:100%;height:410px; border-width:0px;">
            </iframe>
        </div>
         <div id="<?= $row_id ?>-9">
            <iframe src ="<?= base_url() . 'index.php/Tabs/invanalitics/' . $skuNumber ?>" style="width:100%;height:410px; border-width:0px;">
            </iframe>
        </div>

        <div id="<?= $row_id ?>-10">
            <iframe src ="<?= base_url() . 'index.php/Tabs/assemblyreq?q=' . $skuNumber ?>" style="width:100%;height:410px; border-width:0px;">
            </iframe>
        </div>
        <div id="<?= $row_id ?>-11">
            <iframe src ="<?= base_url() . 'index.php/Tabs/vqohdetails/'.$skuNumber ?>" style="width:100%;height:410px; border-width:0px;">
            </iframe>
        </div>
        <div id="<?= $row_id ?>-12">
            <iframe src ="<?= base_url() . 'index.php/Tabs/costinfo/'.$skuNumber ?>" style="width:100%;height:410px; border-width:0px;">
            </iframe>
        </div>

        <div id="<?= $row_id ?>-13">
            <iframe src ="<?= base_url() . 'index.php/Tabs/pohistory/' . $skuNumber ?>" style="width:100%;height:410px; border-width:0px;">
            </iframe>
        </div>
        <!-- <div id="<?= $row_id ?>-14">
            <iframe src ="<?= base_url() . 'index.php/Tabs/advanced/' . $skuNumber ?>" style="width:100%;height:410px; border-width:0px;">
            </iframe>
        </div> -->
    </div>
</section>
