<style>
body{
    background-color: white;
}
.skudata{
    font-size: 11px;
}
</style>

<?php 

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