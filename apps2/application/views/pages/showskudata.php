<style> body{
    background-color: white;
}
.skudata{
    font-size: 11px;
}
</style>


<?php $skuData['AssemblyRequired'] = $skuData['AssemblyRequired'] ? "Yes" : "No"; $skuData['Serialized'] = trim($skuData['Serialized']) ? "Yes" : "No"; 
$skuData['IsSellable'] = trim($skuData['IsSellable']) ? "Yes" : "No";


$itemByLine = 6;


echo 'hollaaaaa';

$fields = array(); $information = array(); foreach ($skuData as $indice => $actual) {
    $fields[] = $indice;
    $information[] = $actual;
}

?> <center>
    <table class="skudata">
        <th colspan=6 >Main Data</th>
        <?php
        $flag = 0;
        for ($i = 0; $i < 28; $i++) {

            if ($fields[$i] == 'Name' or $fields[$i] == 'Description' or $fields[$i] == 'UpcCode' or $fields[$i] == 'Keywords') {

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
    </table> </center>
