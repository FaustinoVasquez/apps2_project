<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Feeds extends BP_Controller
{

    public function __construct()
    {
        parent::__construct();

        $is_logged_in = $this->session->userdata('is_logged_in');

        if (!isset($is_logged_in) || $is_logged_in != true) {
            redirect(base_url(), 'refresh');
        }

        if ($this->MUsers->isValidUser($this->session->userdata('userid'), 884400) != 1) {// Access Code
            redirect('Catalog/prodcat', 'refresh');
        }
    }


public function index()
    {

        $this->title = "MI Technologiesinc - Vendor Central Feeds";

        $this->description = "Vendor Central Feeds";

        $this->css = array('form.css', 'fluid.css', 'table.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css', 'menu.css',);

        // Define custom javascript
        $this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jqgrid/i18n/grid.locale-en.js', 'jqgrid/jquery.jqGrid.min.js', 'jqgrid/jquery.jqGrid.fluid.js', 'site.js');

        // If the page has menu
        $this->load->library('Layout');
        $menu = new Layout;


        $data = array(
            'menu' => $menu->show_menu($this->MUsers->getUserFullName($this->session->userdata('userid'))),
            'from' => '/channelManagement/feeds/',
            'caption' => 'Vendor Central Feeds',
            'export' => 'exportexcel',
            'subgrid' => 'false', // true or false depends
            'sort' => 'desc',
            'search' => '',
            'customerid' => $this->MUsers->getCustomerId($this->session->userdata('userid')),
        );


        $sql= " SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'VendorCentralLoaderUSA-View'";
        $sql2= "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'VendorCentralLoaderMexico-View'";

        $result = $this->MCommon->getSomeRecords($sql);
        $result2= $this->MCommon->getSomeRecords($sql2);

        $colnames = ["'RowNumber'"];
        $colnames2 = ["'RowNumber'"];

        foreach($result as $row)
        {
            $colnames[] = "'".$row['COLUMN_NAME']."'";
        }
        foreach($result2 as $row)
        {
            $colnames2[] = "'".utf8_encode($row['COLUMN_NAME'])."'";
        }

        $colmodel = [
            "{name:'RowNumber',index:'RowNumber', align:'center', width:80,sorttype:'int'},
            {name:'Dropship vendor code',index:'Dropship vendor code', align:'center', width:300},
            {name:'Vendor Code',index:'Vendor Code', align:'center', width:300},
            {name:'Is a replacement part?',index:'Is a replacement part?', align:'center', width:200},
            {name:'Product Categorization',index:'Product Categorization', align:'center', width:200},
            {name:'Product Classification',index:'Product Classification', align:'center', width:200},
            {name:'Item Name',index:'Item Name', align:'left', width:500},
            {name:'Link for MAIN image of item',index:'Link for MAIN image of item', align:'left', width:500, formatter:formatLink },
            {name:'Suggested Product title',index:'Suggested Product title', align:'left', width:500},
            {name:'Is Variation Item',index:'Is Variation Item', align:'center', width:200},
            {name:'Variation Theme Name',index:'Variation Theme Name', align:'center', width:200},
            {name:'Family Grouping',index:'Family Grouping', align:'center', width:200},
            {name:'Are you the Manufacturer of this item?',index:'Are you the Manufacturer of this item?', align:'center', width:200},
            {name:'Brand Name',index:'Brand Name', align:'center', width:200},
            {name:'New Brand',index:'New Brand', align:'center', width:200},
            {name:'Is ASIN a Replacement for an older model?',index:'Is ASIN a Replacement for an older model?', align:'center', width:200},
            {name:'Older ASIN # being replaced?',index:'Older ASIN # being replaced?', align:'center', width:200},
            {name:'UPC being replaced?',index:'UPC being replaced?', align:'center', width:200},
            {name:'Vendor SKU #',index:'Vendor SKU #', align:'center', width:200},
            {name:'Model Number',index:'Model Number', align:'center', width:200},
            {name:'External ID Type',index:'External ID Type', align:'center', width:200},
            {name:'External ID#',index:'External ID#', align:'center', width:200},
            {name:'Describe the product',index:'Describe the product', align:'center', width:500},
            {name:'Bullet Feature 1',index:'Bullet Feature 1', align:'center', width:500},
            {name:'Bullet Feature 2',index:'Bullet Feature 2', align:'center', width:500},
            {name:'Bullet Feature 3',index:'Bullet Feature 3', align:'center', width:500},
            {name:'Bullet Feature 4',index:'Bullet Feature 4', align:'center', width:500},
            {name:'Bullet Feature 5',index:'Bullet Feature 5', align:'center', width:500},
            {name:'Color Name',index:'Color Name', align:'center', width:200},
            {name:'Style Name',index:'Style Name', align:'center', width:200},
            {name:'Search Keywords',index:'Search Keywords', align:'center', width:500},
            {name:'Product Warranty',index:'Product Warranty', align:'center', width:200},
            {name:'Country of Origin',index:'Country of Origin', align:'center', width:200},
            {name:'Product Launch Date',index:'Product Launch Date', align:'center', width:200},
            {name:'Cost Price',index:'Cost Price', align:'center', width:200},
            {name:'List Price or MSRP',index:'List Price or MSRP', align:'center', width:200},
            {name:'Case Pack Quantity',index:'Case Pack Quantity', align:'center', width:200},
            {name:'Minimum Order Quantity',index:'Minimum Order Quantity', align:'center', width:200},
            {name:'Item Length (in inches)',index:'Item Length (in inches)', align:'center', width:200},
            {name:'Item Length (Units of Measurement)',index:'Item Length (in inches)', align:'center', width:200},
            {name:'Item Height (in inches)',index:'Item Height (in inches)', align:'center', width:200},
            {name:'Item Width (in inches)',index:'Item Width (in inches)', align:'center', width:200},
            {name:'Item Weight (in pounds)',index:'Item Weight (in pounds)', align:'center', width:200},
            {name:'Package Size More Than 20 inches x 15 inches x 15 inches',index:'Package Size More Than 20 inches x 15 inches x 15 inches', align:'center', width:200},
            {name:'Package Weight More Than 20 pounds?',index:'Package Weight More Than 20 pounds?', align:'center', width:200},
            {name:'Is Energy Star Certified?',index:'Is Energy Star Certified?', align:'center', width:200},
            {name:'Antenna Description',index:'Antenna Description', align:'center', width:200},
            {name:'Antenna Type',index:'Antenna Type', align:'center', width:200},
            {name:'Antenna Location',index:'Antenna Location', align:'center', width:200},
            {name:'Power Source',index:'Power Source', align:'center', width:200},
            {name:'Cable Length',index:'Cable Length', align:'center', width:200},
            {name:'Cable Length Units',index:'Cable Length Units', align:'center', width:200},
            {name:'connector-type-used-on-cable',index:'connector-type-used-on-cable', align:'center', width:200},
            {name:'Connecter Gender 1',index:'Connecter Gender 1', align:'center', width:200},
            {name:'Connecter Gender 2',index:'Connecter Gender 2', align:'center', width:200},
            {name:'Mount Bolt Pattern',index:'Mount Bolt Pattern', align:'center', width:200},
            {name:'Mounting Hole Diameter',index:'Mounting Hole Diameter', align:'center', width:200},
            {name:'Mounting Hole Diameter Units',index:'Mounting Hole Diameter Units', align:'center', width:200},
            {name:'Media Type 1',index:'Media Type 1', align:'center', width:200},
            {name:'Media Type 2',index:'Media Type 2', align:'center', width:200},
            {name:'Media Type 3',index:'Media Type 3', align:'center', width:200},
            {name:'Wireless Technology 1',index:'Wireless Technology 1', align:'center', width:200},
            {name:'Wireless Technology 2',index:'Wireless Technology 2', align:'center', width:200},
            {name:'Wireless Technology 3',index:'Wireless Technology 3', align:'center', width:200},
            {name:'Manufacturer Recommended Maximum Weight',index:'Manufacturer Recommended Maximum Weight', align:'center', width:200},
            {name:'Finish',index:'Finish', align:'center', width:200},
            {name:'Connectivity Technology',index:'Connectivity Technology', align:'center', width:200},
            {name:'Media Speed',index:'Media Speed', align:'center', width:200},
            {name:'Headphone Style',index:'Headphone Style', align:'center', width:200},
            {name:'Headphone Ear Cup Motion',index:'Headphone Ear Cup Motion', align:'center', width:200},
            {name:'Noise Reduction Level',index:'Noise Reduction Level', align:'center', width:200},
            {name:'Headphone Features 1',index:'Headphone Features 1', align:'center', width:200},
            {name:'Headphone Features 2',index:'Headphone Features 2', align:'center', width:200},
            {name:'Headphone Features 3',index:'Headphone Features 3', align:'center', width:200},
            {name:'TV Mount Minimum Display Size',index:'TV Mount Minimum Display Size', align:'center', width:200},
            {name:'TV Mount Maximum Display Size',index:'TV Mount Maximum Display Size', align:'center', width:200},
            {name:'Built In Decoders 2',index:'Built In Decoders 2', align:'center', width:200},
            {name:'Frequency Response Curve',index:'Frequency Response Curve', align:'center', width:200},
            {name:'Impedance',index:'Impedance', align:'center', width:200},
            {name:'Control Type',index:'Control Type', align:'center', width:200},
            {name:'Holder Capacity',index:'Holder Capacity', align:'center', width:200},
            {name:'Number Of Outlets',index:'Number Of Outlets', align:'center', width:200},
            {name:'Surge Protection Rating',index:'Surge Protection Rating', align:'center', width:200},
            {name:'Surge Protection Rating Unit Of Measure',index:'Surge Protection Rating Unit Of Measure', align:'center', width:200},
            {name:'Remote Programming Technology',index:'Remote Programming Technology', align:'center', width:200},
            {name:'Has Color Screen',index:'Has Color Screen', align:'center', width:200},
            {name:'Maximum Number Of Supported Devices',index:'Maximum Number Of Supported Devices', align:'center', width:200},
            {name:'Human Interface Input 1',index:'Human Interface Input 1', align:'center', width:200},
            {name:'Human Interface Input 2',index:'Human Interface Input 2', align:'center', width:200},
            {name:'Human Interface Input 3',index:'Human Interface Input 3', align:'center', width:200},
            {name:'Battery Cell Type',index:'Battery Cell Type', align:'center', width:200},
            {name:'Rechargeable Battery Included',index:'Rechargeable Battery Included', align:'center', width:200},
            {name:'Battery Average Life',index:'Battery Average Life', align:'center', width:200},
            {name:'Battery Average Life Units',index:'Battery Average Life Units', align:'center', width:200},
            {name:'Maximum Battery Charges',index:'Maximum Battery Charges', align:'center', width:200},
            {name:'Battery Information',index:'Battery Information', align:'center', width:200},
            {name:'Type of Batteries?',index:'Type of Batteries?', align:'center', width:200},
            {name:'Is the product a lithium battery, packed with a lithium battery or does it contain a lithium battery?',index:'Is the product a lithium battery, packed with a lithium battery or does it contain a lithium battery?', align:'center', width:200},
            {name:'Type of Lithium Battery',index:'Type of Lithium Battery', align:'center', width:200},
            {name:'How Many Batteries Required?',index:'How Many Batteries Required?', align:'center', width:200},
            {name:'Lithium Battery Packaging',index:'Lithium Battery Packaging', align:'center', width:200},
            {name:'Lithium Battery Energy Content (in Watt-Hours)',index:'Lithium Battery Energy Content (in Watt-Hours)', align:'center', width:200},
            {name:'Weight of Lithium in Grams',index:'Weight of Lithium in Grams', align:'center', width:200},
            {name:'Lithium Battery Voltage (in Volts)',index:'Lithium Battery Voltage (in Volts)', align:'center', width:200},
            {name:'Number of Lithium METAL Cells',index:'Number of Lithium METAL Cells', align:'center', width:200},
            {name:'Number of Lithium ION Cells',index:'Number of Lithium ION Cells', align:'center', width:200},
            {name:'Is the product designed or intended for children 12 years of age or younger?',index:'Is the product designed or intended for children 12 years of age or younger?', align:'center', width:200},
            {name:'WARNING: CHOKING HAZARD--Small parts.  Not intended for children under 3 yrs.',index:'WARNING: CHOKING HAZARD--Small parts.  Not intended for children under 3 yrs.', align:'center', width:200},
            {name:'WARNING: CHOKING HAZARD--Toy contains a small ball.  Not intended for children under 3 yrs.',index:'WARNING: CHOKING HAZARD--Toy contains a small ball.  Not intended for children under 3 yrs.', align:'center', width:200},
            {name:'WARNING: CHOKING HAZARD--This toy is a small ball.  Not intended for children under 3 yrs.',index:'WARNING: CHOKING HAZARD--This toy is a small ball.  Not intended for children under 3 yrs.', align:'center', width:200},
            {name:'WARNING: CHOKING HAZARD--Children under 8 yrs. Can choke or suffocate on uninflated or broken balloons.',index:'WARNING: CHOKING HAZARD--Children under 8 yrs. Can choke or suffocate on uninflated or broken balloons.', align:'center', width:200},
            {name:'WARNING: CHOKING HAZARD--Toy contains a marble.  Not intended for children under 3 yrs.',index:'WARNING: CHOKING HAZARD--Toy contains a marble.  Not intended for children under 3 yrs.', align:'center', width:200},
            {name:'WARNING: CHOKING HAZARD--This toy is a marble.  Not intended for children under 3 yrs.',index:'WARNING: CHOKING HAZARD--This toy is a marble.  Not intended for children under 3 yrs.', align:'center', width:200},
            {name:'Is the product an Aerosol or Compressed gas?',index:'Is the product an Aerosol or Compressed gas?', align:'center', width:200},
            {name:'Is the product a liquid or contain a liquid with a flashpoint below 200F(93C)?',index:'Is the product a liquid or contain a liquid with a flashpoint below 200F(93C)?', align:'center', width:200},
            {name:'Is the product a Consumer Commodity ORM-D?',index:'Is the product a Consumer Commodity ORM-D?', align:'center', width:200},
            {name:'Is the product a hazardous material according the Department of Transportation,  Hazardous',index:'Is the product a hazardous material according the Department of Transportation,  Hazardous', align:'center', width:200},
            {name:'Flash Point',index:'Flash Point', align:'center', width:200},
            {name:'Hazmat United Nations Regulatory ID',index:'Hazmat United Nations Regulatory ID', align:'center', width:200},
            {name:'Comments',index:'Comments', align:'center', width:200},
            {name:'Home Automation ASIN?',index:'Home Automation ASIN?', align:'center', width:200},
            {name:'Communication Protocol1',index:'Communication Protocol1', align:'center', width:200},
            {name:'Communication Protocol2',index:'Communication Protocol2', align:'center', width:200},
            {name:'Communication Protocol3',index:'Communication Protocol3', align:'center', width:200},
            {name:'Communication Protocol4',index:'Communication Protocol4', align:'center', width:200},
            {name:'Gateway Compatibility1',index:'Gateway Compatibility1', align:'center', width:200},
            {name:'Gateway Compatibility2',index:'Gateway Compatibility2', align:'center', width:200},
            {name:'Gateway Compatibility3',index:'Gateway Compatibility3', align:'center', width:200},
            {name:'Gateway Compatibility4',index:'Gateway Compatibility4', align:'center', width:200},
            {name:'Can package ship without additional protection or over-box?',index:'Can package ship without additional protection or over-box?', align:'center', width:200},
            {name:'--',index:'--', align:'center', width:200},
            {name:'QOH',index:'QOH', align:'center', width:200},
            {name:'vQOH',index:'vQOH', align:'center', width:200},
            {name:'tQOH',index:'tQOH', align:'center', width:200},
            {name:'LowestCost',index:'LowestCost', align:'center', width:200}"
        ];

        $colmodel2 = [
            "{name:'RowNumber',index:'RowNumber', align:'center', width:50,sorttype:'int'},
            {name:'Codigo de Proveedor',index:'Codigo de Proveedor', align:'center', width:400},
            {name:'Estara disponible el producto para envio directo al cliente?',index:'Estara disponible el producto para envio directo al cliente?', align:'center', width:300},
            {name:'Codigo para Ordenes de Compra',index:'Codigo para Ordenes de Compra', align:'center', width:300},
            {name:'Tipo de ID',index:'Tipo de ID', align:'center', width:300},
            {name:'Numero de ID',index:'Numero de ID', align:'center', width:300},
            {name:'Nombre del producto',index:'Nombre del producto', align:'center', width:500},
            {name:'Es una Variacion?',index:'Es una Variacion?', align:'center', width:200},
            {name:'Nombre del Tipo de Variacion',index:'Nombre del Tipo de Variacion', align:'center', width:200},
            {name:'Agrupacion por Familia',index:'Agrupacion por Familia', align:'center', width:200},
            {name:'Marca',index:'Marca', align:'center', width:300},
            {name:'Nueva Marca',index:'Nueva Marca', align:'center', width:300},
            {name:'Modelo',index:'Modelo', align:'center', width:300},
            {name:'Titulo Recomendado para Mostrar',index:'Titulo Recomendado para Mostrar', align:'center', width:500},
            {name:'Moneda para Costo neto unitario en factura',index:'Moneda para Costo neto unitario en factura', align:'center', width:200},
            {name:'Coste neto unitario en factura (IVA excluido)',index:'Coste neto unitario en factura (IVA excluido)', align:'center', width:200},
            {name:'MSRP - Precio Recomendado al Publico (en MXN, IVA excluido)',index:'MSRP - Precio Recomendado al Publico (en MXN, IVA excluido)', align:'center', width:200},
            {name:'Pais de Origen',index:'Pais de Origen', align:'center', width:200},
            {name:'Altura del Producto (cm)',index:'Altura del Producto (cm)', align:'center', width:200},
            {name:'Longitud del Producto (cm)',index:'Longitud del Producto (cm)', align:'center', width:200},
            {name:'Ancho del Producto (cm)',index:'Ancho del Producto (cm)', align:'center', width:200},
            {name:'Peso del Producto (kg)',index:'Peso del Producto (kg)', align:'center', width:200},
            {name:'El paquete es pesado/voluminoso?',index:'El paquete es pesado/voluminoso?', align:'center', width:200},
            {name:'Altura del Paquete (cm)',index:'Altura del Paquete (cm)', align:'center', width:200},
            {name:'Longitud del Paquete (cm)',index:'Longitud del Paquete (cm)', align:'center', width:200},
            {name:'Ancho del Paquete (cm)',index:'Ancho del Paquete (cm)', align:'center', width:200},
            {name:'Peso del Paquete (kg)',index:'Peso del Paquete (kg)', align:'center', width:200},
            {name:'Se envia en multiples cajas?',index:'Se envia en multiples cajas?', align:'center', width:200},
            {name:'Categoria',index:'Categoria', align:'center', width:200},
            {name:'Subcategoria',index:'Subcategoria', align:'center', width:200},
            {name:'Nodo de Navegacion',index:'Nodo de Navegacion', align:'center', width:200},
            {name:'Productos por Caja',index:'Productos por Caja', align:'center', width:200},
            {name:'Tiene pantalla?',index:'Tiene pantalla?', align:'center', width:200},
            {name:'Tamano de Pantalla',index:'Tamano de Pantalla', align:'center', width:200},
            {name:'Palabras Clave para Busqueda',index:'Palabras Clave para Busqueda', align:'center', width:500},
            {name:'Caracteristica 1',index:'Caracteristica 1', align:'center', width:300},
            {name:'Caracteristica 2',index:'Caracteristica 2', align:'center', width:300},
            {name:'Caracteristica 3',index:'Caracteristica 3', align:'center', width:300},
            {name:'Caracteristica 4',index:'Caracteristica 4', align:'center', width:300},
            {name:'Caracteristica 5',index:'Caracteristica 5', align:'center', width:300},
            {name:'Descripcion Larga del Producto',index:'Descripcion Larga del Producto', align:'center', width:200},
            {name:'Guarantia del Producto',index:'Guarantia del Producto', align:'center', width:200},
            {name:'Color',index:'Color', align:'center', width:200},
            {name:'Son Precisas Baterias?',index:'Son Precisas Baterias?', align:'center', width:200},
            {name:'Estan las Baterias Incluidas?',index:'Estan las Baterias Incluidas?', align:'center', width:200},
            {name:'Cuantas Baterias Son Precisas?',index:'Cuantas Baterias Son Precisas?', align:'center', width:200},
            {name:'Que Tipo de Baterias?',index:'Que Tipo de Baterias?', align:'center', width:200},
            {name:'Vida de la Bateria (Horas)',index:'Vida de la Bateria (Horas)', align:'center', width:200},
            {name:'Contenido Energetico de las Baterias de Litio',index:'Contenido Energetico de las Baterias de Litio', align:'center', width:200},
            {name:'Empaquetado de las Baterias de Litio',index:'Empaquetado de las Baterias de Litio', align:'center', width:200},
            {name:'Peso del Litio en Gramos',index:'Peso del Litio en Gramos', align:'center', width:200},
            {name:'Voltaje de la Bateria de Litio',index:'Voltaje de la Bateria de Litio', align:'center', width:200},
            {name:'Numero de celulas de METAL litio',index:'Numero de celulas de METAL litio', align:'center', width:200},
            {name:'Numero de celulas de ION litio',index:'Numero de celulas de ION litio', align:'center', width:200},
            {name:'Es el ASIN (producto) un liquido, o contiene un liquido?',index:'Es el ASIN (producto) un liquido, o contiene un liquido?', align:'center', width:200},
            {name:'Es el ASIN (producto) un liquido, o contiene un liquido con una temperatura de combustion por debajo de los 200F (93C)?',index:'Es el ASIN (producto) un liquido, o contiene un liquido con una temperatura de combustion por debajo de los 200F (93C)?', align:'center', width:200},
            {name:'Temperatura de Combustion',index:'Temperatura de Combustion', align:'center', width:200},
            {name:'Es el ASIN (producto) un aerosol o gas comprimido?',index:'Es el ASIN (producto) un aerosol o gas comprimido?', align:'center', width:200},
            {name:'Es el ASIN (producto) un material peligroso de acuerdo a la NOM-002-SCT-2011 y a la NOM-011-SCT2?',index:'Es el ASIN (producto) un material peligroso de acuerdo a la NOM-002-SCT-2011 y a la NOM-011-SCT2?', align:'center', width:200},
            {name:'ID de Material Peligroso de Nacional Unidas',index:'ID de Material Peligroso de Nacional Unidas', align:'center', width:200},
            {name:'Esta el etiquetado del producto en espanol?',index:'Esta el etiquetado del producto en espanol?', align:'center', width:200},
            {name:'Esta el paquete en espanol?',index:'Esta el paquete en espanol?', align:'center', width:200},
            {name:'Viene el producto con manual de instrucciones?',index:'Viene el producto con manual de instrucciones?', align:'center', width:200},
            {name:'Esta el manual de instrucciones en espanol?',index:'Esta el manual de instrucciones en espanol?', align:'center', width:200},
            {name:'--',index:'--', align:'center', width:200},
            {name:'QOH',index:'QOH', align:'center', width:200},
            {name:'vQOH',index:'vQOH', align:'center', width:200},
            {name:'tQOH',index:'tQOH', align:'center', width:200},
        "];


        $data['colNames'] = "[" . implode($colnames, ",") . "]";
        $data['colModel'] = "[" . implode($colmodel, ",") . "]";

        $data['colNames2'] = "[" . implode($colnames2, ",") . "]";
        $data['colModel2'] = "[" . implode($colmodel2, ",") . "]";


        $this->build_content($data);
        $this->render_page();
    }

public function getData()
    {

        $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; // get the requested page
        $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : 10; // get how many rows we want to have into the gri
        $sidx = empty($_REQUEST['sidx']) ? $_REQUEST['sidx'] : 'Vendor Code'; // get index row - i.e. user click to sort
        $sord = isset($_REQUEST['sord']) ? $_REQUEST['sord'] : 'asc';

        $search = $this->input->get('ds');


        $selectCount = 'SELECT COUNT(*) AS rowNum';

        $table = ' Inventory.dbo.[VendorCentralLoaderUSA-View]  ';

        $where = '';

        $from = ' FROM ';

        $select = ' SELECT * ';

        $wherefields = array('[Dropship vendor code]','[Vendor Code]','[Product Categorization]','[Product Classification]',
                    '[Item Name]','[Link for MAIN image of item]','[Suggested Product title]','[Variation Theme Name]',
                    '[Family Grouping]','[Brand Name]','[New Brand]','[Vendor SKU #]','[Model Number]',
                    '[External ID Type]','[External ID#]','[Describe the product]','[Bullet Feature 1]',
                    '[Bullet Feature 2]','[Bullet Feature 3]','[Bullet Feature 4]','[Bullet Feature 5]',
                    '[Color Name]','[Style Name]','[Search Keywords]','[Product Warranty]','[Country of Origin]',
                    '[Product Launch Date]','[Cost Price]','[List Price or MSRP]','[Antenna Description]',
                    '[Antenna Type]','[Antenna Location]','[Power Source]','[Cable Length]','[Cable Length Units]',
                    '[connector-type-used-on-cable]','[Connecter Gender 1]','[Connecter Gender 2]',
                    '[Mount Bolt Pattern]','[Mounting Hole Diameter]','[Mounting Hole Diameter Units]',
                    '[Media Type 1]','[Media Type 2]','[Media Type 3]','[Wireless Technology 1]',
                    '[Wireless Technology 2]','[Wireless Technology 3]','[Manufacturer Recommended Maximum Weight]',
                    '[Finish]','[Connectivity Technology]','[Media Speed]','[Headphone Style]',
                    '[Headphone Ear Cup Motion]','[Noise Reduction Level]','[Headphone Features 1]',
                    '[Headphone Features 2]','[Headphone Features 3]','[TV Mount Minimum Display Size]',
                    '[TV Mount Maximum Display Size]','[Built In Decoders 2]','[Frequency Response Curve]',
                    '[Impedance]','[Control Type]','[Holder Capacity]','[Number Of Outlets]','[Surge Protection Rating]',
                    '[Surge Protection Rating Unit Of Measure]','[Remote Programming Technology]','[Has Color Screen]',
                    '[Maximum Number Of Supported Devices]','[Human Interface Input 1]','[Human Interface Input 2]',
                    '[Human Interface Input 3]','[Battery Cell Type]','[Rechargeable Battery Included]',
                    '[Battery Average Life]','[Battery Average Life Units]','[Maximum Battery Charges]',
                    '[Battery Information]','[Type of Lithium Battery]','[Lithium Battery Packaging]',
                    '[Lithium Battery Energy Content (in Watt-Hours)]','[Weight of Lithium in Grams]',
                    '[Lithium Battery Voltage (in Volts)]','[Number of Lithium METAL Cells]',
                    '[Number of Lithium ION Cells]',
                    '[WARNING: CHOKING HAZARD--Small parts.  Not intended for children under 3 yrs.]',
                    '[WARNING: CHOKING HAZARD--Toy contains a small ball.  Not intended for children under 3 yrs.]',
                    '[WARNING: CHOKING HAZARD--This toy is a small ball.  Not intended for children under 3 yrs.]',
                    '[WARNING: CHOKING HAZARD--Children under 8 yrs. Can choke or suffocate on uninflated or broken balloons.]',
                    '[WARNING: CHOKING HAZARD--Toy contains a marble.  Not intended for children under 3 yrs.]',
                    '[WARNING: CHOKING HAZARD--This toy is a marble.  Not intended for children under 3 yrs.]',
                    '[Flash Point]','[Hazmat United Nations Regulatory ID]',
                    '[Comments]','[Communication Protocol1]','[Communication Protocol2]',
                    '[Communication Protocol3]','[Communication Protocol4]','[Gateway Compatibility1]',
                    '[Gateway Compatibility2]','[Gateway Compatibility3]','[Gateway Compatibility4]','[--]',
                    '[QOH]','[vQOH]','[tQOH]','[LowestCost]');


       // $fields = $this->MCommon->getAllfields($table);
        //creamos el were concatenando todos los nombres de campos y las palabras de busqueda
        $where .=$this->MCommon->concatAllWerefields($wherefields, $search);

        $SQL = "{$selectCount}{$from}{$table}{$where}";

        $result = $this->MCommon->getOneRecord($SQL);
        $count = $result['rowNum'];

       // echo $count;

        if ($count > 0) {
            $total_pages = ceil($count / $limit);
        } else {
            $total_pages = 0;
        }
        if ($page > $total_pages)
            $page = $total_pages;

        $start = $limit * $page - $limit; // do not put $limit*($page - 1)
        $start = ($start < 0) ? 0 : $start;
        $finish = $start + $limit;


        $SQL = "WITH mytable AS ($select , ROW_NUMBER() OVER (ORDER BY [Vendor Code] {$sord}) AS RowNumber
                            FROM {$table}{$where})
           {$select}, RowNumber
           FROM mytable
            WHERE RowNumber BETWEEN {$start} AND {$finish}";


       $result = $this->MCommon->getSomeRecords($SQL);
//
//        //  print_r($result);
        $responce = new stdClass();
        $responce->page = $page;
        $responce->total = $total_pages;
        $responce->records = $count;

    $i = 0;
    foreach ($result as $row) {
        $responce->rows[$i]['id'] = $row['RowNumber'];
        $responce->rows[$i]['cell'] = array($row['RowNumber'],
            $row['Dropship vendor code'] = utf8_encode($row['Dropship vendor code']),
            $row['Vendor Code'] = utf8_encode($row['Vendor Code']),
            $row['Is a replacement part?'] = utf8_encode($row['Is a replacement part?']),
            $row['Product Categorization'] = utf8_encode($row['Product Categorization']),
            $row['Product Classification'] = utf8_encode($row['Product Classification']),
            $row['Item Name'] = utf8_encode($row['Item Name']),
            $row['Link for MAIN image of item'] = utf8_encode($row['Link for MAIN image of item']),
            $row['Suggested Product title'] = utf8_encode($row['Suggested Product title']),
            $row['Is Variation Item'] = utf8_encode($row['Is Variation Item']),
            $row['Variation Theme Name'] = utf8_encode($row['Variation Theme Name']),
            $row['Family Grouping'] = utf8_encode($row['Family Grouping']),
            $row['Are you the Manufacturer of this item?']= utf8_encode($row['Are you the Manufacturer of this item?']),
            $row['Brand Name'] = utf8_encode($row['Brand Name']),
            $row['New Brand'] = utf8_encode($row['New Brand']),
            $row['Is ASIN a Replacement for an older model?'] = utf8_encode($row['Is ASIN a Replacement for an older model?']),
            $row['Older ASIN # being replaced?'] = utf8_encode($row['Older ASIN # being replaced?']),
            $row['UPC being replaced?'] = utf8_encode($row['UPC being replaced?']),
            $row['Vendor SKU #'] = utf8_encode($row['Vendor SKU #']),
            $row['Model Number'] = utf8_encode($row['Model Number']),
            $row['External ID Type'] = utf8_encode($row['External ID Type']),
            $row['External ID#'] = utf8_encode($row['External ID#']),
            $row['Describe the product'] = utf8_encode($row['Describe the product']),
            $row['Bullet Feature 1'] = utf8_encode($row['Bullet Feature 1']),
            $row['Bullet Feature 2'] = utf8_encode($row['Bullet Feature 2']),
            $row['Bullet Feature 3'] = utf8_encode($row['Bullet Feature 3']),
            $row['Bullet Feature 4'] = utf8_encode($row['Bullet Feature 4']),
            $row['Bullet Feature 5'] = utf8_encode($row['Bullet Feature 5']),
            $row['Color Name'] = utf8_encode($row['Color Name']),
            $row['Style Name'] = utf8_encode($row['Style Name']),
            $row['Search Keywords'] = utf8_encode($row['Search Keywords']),
            $row['Product Warranty'] = utf8_encode($row['Product Warranty']),
            $row['Country of Origin'] = utf8_encode($row['Country of Origin']),
            $row['Product Launch Date'] = utf8_encode($row['Product Launch Date']),
            $row['Cost Price'] = utf8_encode($row['Cost Price']),
            $row['List Price or MSRP'] = utf8_encode($row['List Price or MSRP']),
            $row['Case Pack Quantity'] = utf8_encode($row['Case Pack Quantity']),
            $row['Minimum Order Quantity'] = utf8_encode($row['Minimum Order Quantity']),
            $row['Item Length (in inches)'] = utf8_encode($row['Item Length (in inches)']),
            $row['Item Length (Units of Measurement)'] = utf8_encode($row['Item Length (Units of Measurement)']),
            $row['Item Height (in inches)'] = utf8_encode($row['Item Height (in inches)']),
            $row['Item Width (in inches)'] = utf8_encode($row['Item Width (in inches)']),
            $row['Item Weight (in pounds)'] = utf8_encode($row['Item Weight (in pounds)']),
            $row['Package Size More Than 20 inches x 15 inches x 15 inches'] = utf8_encode($row['Package Size More Than 20 inches x 15 inches x 15 inches']),
            $row['Package Weight More Than 20 pounds?'] = utf8_encode($row['Package Weight More Than 20 pounds?']),
            $row['Is Energy Star Certified?'] = utf8_encode($row['Is Energy Star Certified?']),
            $row['Antenna Description'] = utf8_encode($row['Antenna Description']),
            $row['Antenna Type'] = utf8_encode($row['Antenna Type']),
            $row['Antenna Location'] = utf8_encode($row['Antenna Location']),
            $row['Power Source'] = utf8_encode($row['Power Source']),
            $row['Cable Length'] = utf8_encode($row['Cable Length']),
            $row['Cable Length Units'] = utf8_encode($row['Cable Length Units']),
            $row['connector-type-used-on-cable'] = utf8_encode($row['connector-type-used-on-cable']),
            $row['Connecter Gender 1'] = utf8_encode($row['Connecter Gender 1']),
            $row['Connecter Gender 2'] = utf8_encode($row['Connecter Gender 2']),
            $row['Mount Bolt Pattern'] = utf8_encode($row['Mount Bolt Pattern']),
            $row['Mounting Hole Diameter'] = utf8_encode($row['Mounting Hole Diameter']),
            $row['Mounting Hole Diameter Units'] = utf8_encode($row['Mounting Hole Diameter Units']),
            $row['Media Type 1'] = utf8_encode($row['Media Type 1']),
            $row['Media Type 2'] = utf8_encode($row['Media Type 2']),
            $row['Media Type 3'] = utf8_encode($row['Media Type 3']),
            $row['Wireless Technology 1'] = utf8_encode($row['Wireless Technology 1']),
            $row['Wireless Technology 2'] = utf8_encode($row['Wireless Technology 2']),
            $row['Wireless Technology 3'] = utf8_encode($row['Wireless Technology 3']),
            $row['Manufacturer Recommended Maximum Weight'] = utf8_encode($row['Manufacturer Recommended Maximum Weight']),
            $row['Finish'] = utf8_encode($row['Finish']),
            $row['Connectivity Technology'] = utf8_encode($row['Connectivity Technology']),
            $row['Media Speed'] = utf8_encode($row['Media Speed']),
            $row['Headphone Style'] = utf8_encode($row['Headphone Style']),
            $row['Headphone Ear Cup Motion'] = utf8_encode($row['Headphone Ear Cup Motion']),
            $row['Noise Reduction Level'] = utf8_encode($row['Noise Reduction Level']),
            $row['Headphone Features 1'] = utf8_encode($row['Headphone Features 1']),
            $row['Headphone Features 2'] = utf8_encode($row['Headphone Features 2']),
            $row['Headphone Features 3'] = utf8_encode($row['Headphone Features 3']),
            $row['TV Mount Minimum Display Size'] = utf8_encode($row['TV Mount Minimum Display Size']),
            $row['TV Mount Maximum Display Size'] = utf8_encode($row['TV Mount Maximum Display Size']),
            $row['Built In Decoders 2'] = utf8_encode($row['Built In Decoders 2']),
            $row['Frequency Response Curve'] = utf8_encode($row['Frequency Response Curve']),
            $row['Impedance'] = utf8_encode($row['Impedance']),
            $row['Control Type'] = utf8_encode($row['Control Type']),
            $row['Holder Capacity'] = utf8_encode($row['Holder Capacity']),
            $row['Number Of Outlets'] = utf8_encode($row['Number Of Outlets']),
            $row['Surge Protection Rating'] = utf8_encode($row['Surge Protection Rating']),
            $row['Surge Protection Rating Unit Of Measure'] = utf8_encode($row['Surge Protection Rating Unit Of Measure']),
            $row['Remote Programming Technology'] = utf8_encode($row['Remote Programming Technology']),
            $row['Has Color Screen'] = utf8_encode($row['Has Color Screen']),
            $row['Maximum Number Of Supported Devices'] = utf8_encode($row['Maximum Number Of Supported Devices']),
            $row['Human Interface Input 1'] = utf8_encode($row['Human Interface Input 1']),
            $row['Human Interface Input 2'] = utf8_encode($row['Human Interface Input 2']),
            $row['Human Interface Input 3'] = utf8_encode($row['Human Interface Input 3']),
            $row['Battery Cell Type'] = utf8_encode($row['Battery Cell Type']),
            $row['Rechargeable Battery Included'] = utf8_encode($row['Rechargeable Battery Included']),
            $row['Battery Average Life'] = utf8_encode($row['Battery Average Life']),
            $row['Battery Average Life Units'] = utf8_encode($row['Battery Average Life Units']),
            $row['Maximum Battery Charges'] = utf8_encode($row['Maximum Battery Charges']),
            $row['Battery Information'] = utf8_encode($row['Battery Information']),
            $row['Type of Batteries?'] = utf8_encode($row['Type of Batteries?']),
            $row['Is the product a lithium battery, packed with a lithium battery or does it contain a lithium battery?'] = utf8_encode($row['Is the product a lithium battery, packed with a lithium battery or does it contain a lithium battery?']),
            $row['Type of Lithium Battery'] = utf8_encode($row['Type of Lithium Battery']),
            $row['How Many Batteries Required?'] = utf8_encode($row['How Many Batteries Required?']),
            $row['Lithium Battery Packaging'] = utf8_encode($row['Lithium Battery Packaging']),
            $row['Lithium Battery Energy Content (in Watt-Hours)'] = utf8_encode($row['Lithium Battery Energy Content (in Watt-Hours)']),
            $row['Weight of Lithium in Grams'] = utf8_encode($row['Weight of Lithium in Grams']),
            $row['Lithium Battery Voltage (in Volts)'] = utf8_encode($row['Lithium Battery Voltage (in Volts)']),
            $row['Number of Lithium METAL Cells'] = utf8_encode($row['Number of Lithium METAL Cells']),
            $row['Number of Lithium ION Cells'] = utf8_encode($row['Number of Lithium ION Cells']),
            $row['Is the product designed or intended for children 12 years of age or younger?'] = utf8_encode($row['Is the product designed or intended for children 12 years of age or younger?']),
            $row['WARNING: CHOKING HAZARD--Small parts.  Not intended for children under 3 yrs.'] = utf8_encode($row['WARNING: CHOKING HAZARD--Small parts.  Not intended for children under 3 yrs.']),
            $row['WARNING: CHOKING HAZARD--Toy contains a small ball.  Not intended for children under 3 yrs.'] = utf8_encode($row['WARNING: CHOKING HAZARD--Toy contains a small ball.  Not intended for children under 3 yrs.']),
            $row['WARNING: CHOKING HAZARD--This toy is a small ball.  Not intended for children under 3 yrs.'] = utf8_encode($row['WARNING: CHOKING HAZARD--This toy is a small ball.  Not intended for children under 3 yrs.']),
            $row['WARNING: CHOKING HAZARD--Children under 8 yrs. Can choke or suffocate on uninflated or broken balloons.'] = utf8_encode($row['WARNING: CHOKING HAZARD--Children under 8 yrs. Can choke or suffocate on uninflated or broken balloons.']),
            $row['WARNING: CHOKING HAZARD--Toy contains a marble.  Not intended for children under 3 yrs.'] = utf8_encode($row['WARNING: CHOKING HAZARD--Toy contains a marble.  Not intended for children under 3 yrs.']),
            $row['WARNING: CHOKING HAZARD--This toy is a marble.  Not intended for children under 3 yrs.'] = utf8_encode($row['WARNING: CHOKING HAZARD--This toy is a marble.  Not intended for children under 3 yrs.']),
            $row['Is the product an Aerosol or Compressed gas?'] = utf8_encode($row['Is the product an Aerosol or Compressed gas?']),
            $row['Is the product a liquid or contain a liquid with a flashpoint below 200F(93C)?'] = utf8_encode($row['Is the product a liquid or contain a liquid with a flashpoint below 200F(93C)?']),
            $row['Is the product a Consumer Commodity ORM-D?'] = utf8_encode($row['Is the product a Consumer Commodity ORM-D?']),
            $row['Is the product a hazardous material according the Department of Transportation,  Hazardous'] = utf8_encode($row['Is the product a hazardous material according the Department of Transportation,  Hazardous']),
            $row['Flash Point'] = utf8_encode($row['Flash Point']),
            $row['Hazmat United Nations Regulatory ID'] = utf8_encode($row['Hazmat United Nations Regulatory ID']),
            $row['Comments'] = utf8_encode($row['Comments']),
            $row['Home Automation ASIN?'] = utf8_encode($row['Home Automation ASIN?']),
            $row['Communication Protocol1'] = utf8_encode($row['Communication Protocol1']),
            $row['Communication Protocol2'] = utf8_encode($row['Communication Protocol2']),
            $row['Communication Protocol3'] = utf8_encode($row['Communication Protocol3']),
            $row['Communication Protocol4'] = utf8_encode($row['Communication Protocol4']),
            $row['Gateway Compatibility1'] = utf8_encode($row['Gateway Compatibility1']),
            $row['Gateway Compatibility2'] = utf8_encode($row['Gateway Compatibility2']),
            $row['Gateway Compatibility3'] = utf8_encode($row['Gateway Compatibility3']),
            $row['Gateway Compatibility4'] = utf8_encode($row['Gateway Compatibility4']),
            $row['Can package ship without additional protection or over-box?'] = utf8_encode($row['Can package ship without additional protection or over-box?']),
            $row['--'] = utf8_encode($row['--']),
            $row['QOH'] = utf8_encode($row['QOH']),
            $row['vQOH'] = utf8_encode($row['vQOH']),
            $row['tQOH'] = utf8_encode($row['tQOH']),
            $row['LowestCost'] = utf8_encode($row['LowestCost']),

        );
        $i++;
    }
        echo json_encode($responce);
    }


public function getData1()
    {

        $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; // get the requested page
        $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : 10; // get how many rows we want to have into the gri
        $sidx = empty($_REQUEST['sidx']) ? $_REQUEST['sidx'] : 'Código de Proveedor'; // get index row - i.e. user click to sort
        $sord = isset($_REQUEST['sord']) ? $_REQUEST['sord'] : 'asc';

        $search = $this->input->get('ds');


        //Select utilizado para realizar el conteo del numero de registros devueltos por la consulta.
        $selectCount = 'SELECT COUNT(*) AS rowNum';

        $table = ' Inventory.dbo.[VendorCentralLoaderMexico-View]  ';

        $where = '';

        $from = ' FROM ';

        $select = "SELECT * ";

        $wherefields = array('[Codigo de Proveedor]',
                    '[Estara disponible el producto para envio directo al cliente?]',
                    '[Codigo para Ordenes de Compra]',
                    '[Tipo de ID]',
                    '[Numero de ID]',
                    '[Nombre del producto]',
                    '[Es una Variacion?]',
                    '[Nombre del Tipo de Variacion]',
                    '[Agrupacion por Familia]',
                    '[Marca]',
                    '[Nueva Marca]',
                    '[Modelo]',
                    '[Titulo Recomendado para Mostrar]',
                    '[Moneda para Costo neto unitario en factura]',
                    '[Coste neto unitario en factura (IVA excluido)]',
                    '[MSRP - Precio Recomendado al Publico (en MXN, IVA excluido)]',
                    '[Pais de Origen]',
                    '[Altura del Producto (cm)]',
                    '[Longitud del Producto (cm)]',
                    '[Ancho del Producto (cm)]',
                    '[Peso del Producto (kg)]',
                    '[El paquete es pesado/voluminoso?]',
                    '[Altura del Paquete (cm)]',
                    '[Longitud del Paquete (cm)]',
                    '[Ancho del Paquete (cm)]',
                    '[Peso del Paquete (kg)]',
                    '[Se envia en multiples cajas?]',
                    '[Categoria]',
                    '[Subcategoria]',
                    '[Nodo de Navegacion]',
                    '[Productos por Caja]',
                    '[Tiene pantalla?]',
                    '[Tamano de Pantalla]',
                    '[Palabras Clave para Busqueda]',
                    '[Caracteristica 1]',
                    '[Caracteristica 2]',
                    '[Caracteristica 3]',
                    '[Caracteristica 4]',
                    '[Caracteristica 5]',
                    '[Descripcion Larga del Producto]',
                    '[Guarantia del Producto]',
                    '[Color]',
                    '[Son Precisas Baterias?]',
                    '[Estan las Baterias Incluidas?]',
                    '[Cuantas Baterias Son Precisas?]',
                    '[Que Tipo de Baterias?]',
                    '[Vida de la Bateria (Horas)]',
                    '[Contenido Energetico de las Baterias de Litio]',
                    '[Empaquetado de las Baterias de Litio]',
                    '[Peso del Litio en Gramos]',
                    '[Voltaje de la Bateria de Litio]',
                    '[Numero de celulas de METAL litio]',
                    '[Numero de celulas de ION litio]',
                    '[Es el ASIN (producto) un liquido, o contiene un liquido?]',
                    '[Es el ASIN (producto) un liquido, o contiene un liquido con una temperatura de combustion por debajo de los 200F (93C)?]',
                    '[Temperatura de Combustion]',
                    '[Es el ASIN (producto) un aerosol o gas comprimido?]',
                    '[Es el ASIN (producto) un material peligroso de acuerdo a la NOM-002-SCT-2011 y a la NOM-011-SCT2?]',
                    '[ID de Material Peligroso de Nacional Unidas]',
                    '[Esta el etiquetado del producto en espanol?]',
                    '[Esta el paquete en espanol?]',
                    '[Viene el producto con manual de instrucciones?]',
                    '[Esta el manual de instrucciones en espanol?]',
                    '[--]',
                    '[QOH]',
                    '[vQOH]',
                    '[tQOH]',
        );

        $where .= $this->MCommon->concatAllWerefields($wherefields, $search);

        $SQL = "{$selectCount}{$from}{$table}{$where}";


        $result = $this->MCommon->getOneRecord($SQL);
        $count = $result['rowNum'];


        if ($count > 0) {
            $total_pages = ceil($count / $limit);
        } else {
            $total_pages = 0;
        }
        if ($page > $total_pages)
            $page = $total_pages;

        $start = $limit * $page - $limit; // do not put $limit*($page - 1)
        $start = ($start < 0) ? 0 : $start;
        $finish = $start + $limit;


        $SQL = "WITH mytable AS ($select , ROW_NUMBER() OVER (ORDER BY [QOH] {$sord}) AS RowNumber
                            FROM {$table}{$where})
           {$select}, RowNumber
           FROM mytable
            WHERE RowNumber BETWEEN {$start} AND {$finish}";

        $result = $this->MCommon->getSomeRecords($SQL);

        //  print_r($result);
        $responce = new stdClass();
        $responce->page = $page;
        $responce->total = $total_pages;
        $responce->records = $count;

        $i = 0;
        foreach ($result as $row) {
            $responce->rows[$i]['id'] = $row['RowNumber'];
            $responce->rows[$i]['cell'] = array($row['RowNumber'],
                $row['Codigo de Proveedor'] = utf8_encode($row['Codigo de Proveedor']),
                $row['Estara disponible el producto para envio directo al cliente?'] = utf8_encode($row['Estara disponible el producto para envio directo al cliente?']),
                $row['Codigo para Ordenes de Compra'] = utf8_encode($row['Codigo para Ordenes de Compra']),
                $row['Tipo de ID'] = utf8_encode($row['Tipo de ID']),
                $row['Numero de ID'] = utf8_encode($row['Numero de ID']),
                $row['Nombre del producto'] = utf8_encode($row['Nombre del producto']),
                $row['Es una Variacion?'] = utf8_encode($row['Es una Variacion?']),
                $row['Nombre del Tipo de Variacion'] = utf8_encode($row['Nombre del Tipo de Variacion']),
                $row['Agrupacion por Familia'] = utf8_encode($row['Agrupacion por Familia']),
                $row['Marca'] = utf8_encode($row['Marca']),
                $row['Nueva Marca'] = utf8_encode($row['Nueva Marca']),
                $row['Modelo'] = utf8_encode($row['Modelo']),
                $row['Titulo Recomendado para Mostrar'] = utf8_encode($row['Titulo Recomendado para Mostrar']),
                $row['Moneda para Costo neto unitario en factura'] = utf8_encode($row['Moneda para Costo neto unitario en factura']),
                $row['Coste neto unitario en factura (IVA excluido)'] = utf8_encode($row['Coste neto unitario en factura (IVA excluido)']),
                $row['MSRP - Precio Recomendado al Publico (en MXN, IVA excluido)'] = utf8_encode($row['MSRP - Precio Recomendado al Publico (en MXN, IVA excluido)']),
                $row['Pais de Origen'] = utf8_encode($row['Pais de Origen']),
                $row['Altura del Producto (cm)'] = utf8_encode($row['Altura del Producto (cm)']),
                $row['Longitud del Producto (cm)'] = utf8_encode($row['Longitud del Producto (cm)']),
                $row['Ancho del Producto (cm)'] = utf8_encode($row['Ancho del Producto (cm)']),
                $row['Peso del Producto (kg)'] = utf8_encode($row['Peso del Producto (kg)']),
                $row['El paquete es pesado/voluminoso?'] = utf8_encode($row['El paquete es pesado/voluminoso?']),
                $row['Altura del Paquete (cm)'] = utf8_encode($row['Altura del Paquete (cm)']),
                $row['Longitud del Paquete (cm)'] = utf8_encode($row['Longitud del Paquete (cm)']),
                $row['Ancho del Paquete (cm)'] = utf8_encode($row['Ancho del Paquete (cm)']),
                $row['Peso del Paquete (kg)'] = utf8_encode($row['Peso del Paquete (kg)']),
                $row['Se envia en multiples cajas?'] = utf8_encode($row['Se envia en multiples cajas?']),
                $row['Categoria'] = utf8_encode($row['Categoria']),
                $row['Subcategoria'] = utf8_encode($row['Subcategoria']),
                $row['Nodo de Navegacion'] = utf8_encode($row['Nodo de Navegacion']),
                $row['Productos por Caja'] = utf8_encode($row['Productos por Caja']),
                $row['Tiene pantalla?'] = utf8_encode($row['Tiene pantalla?']),
                $row['Tamano de Pantalla'] = utf8_encode($row['Tamano de Pantalla']),
                $row['Palabras Clave para Busqueda'] = utf8_encode($row['Palabras Clave para Busqueda']),
                $row['Caracteristica 1'] = utf8_encode($row['Caracteristica 1']),
                $row['Caracteristica 2'] = utf8_encode($row['Caracteristica 2']),
                $row['Caracteristica 3'] = utf8_encode($row['Caracteristica 3']),
                $row['Caracteristica 4'] = utf8_encode($row['Caracteristica 4']),
                $row['Caracteristica 5'] = utf8_encode($row['Caracteristica 5']),
                $row['Descripcion Larga del Producto'] = utf8_encode($row['Descripcion Larga del Producto']),
                $row['Guarantia del Producto'] = utf8_encode($row['Guarantia del Producto']),
                $row['Color'] = utf8_encode($row['Color']),
                $row['Son Precisas Baterias?'] = utf8_encode($row['Son Precisas Baterias?']),
                $row['Estan las Baterias Incluidas?'] = utf8_encode($row['Estan las Baterias Incluidas?']),
                $row['Cuantas Baterias Son Precisas?'] = utf8_encode($row['Cuantas Baterias Son Precisas?']),
                $row['Que Tipo de Baterias?'] = utf8_encode($row['Que Tipo de Baterias?']),
                $row['Vida de la Bateria (Horas)'] = utf8_encode($row['Vida de la Bateria (Horas)']),
                $row['Contenido Energetico de las Baterias de Litio'] = utf8_encode($row['Contenido Energetico de las Baterias de Litio']),
                $row['Empaquetado de las Baterias de Litio'] = utf8_encode($row['Empaquetado de las Baterias de Litio']),
                $row['Peso del Litio en Gramos'] = utf8_encode($row['Peso del Litio en Gramos']),
                $row['Voltaje de la Bateria de Litio'] = utf8_encode($row['Voltaje de la Bateria de Litio']),
                $row['Numero de celulas de METAL litio'] = utf8_encode($row['Numero de celulas de METAL litio']),
                $row['Numero de celulas de ION litio'] = utf8_encode($row['Numero de celulas de ION litio']),
                $row['Es el ASIN (producto) un liquido, o contiene un liquido?'] = utf8_encode($row['Es el ASIN (producto) un liquido, o contiene un liquido?']),
                $row['Es el ASIN (producto) un liquido, o contiene un liquido con una temperatura de combustion por debajo de los 200F (93C)?'] = utf8_encode($row['Es el ASIN (producto) un liquido, o contiene un liquido con una temperatura de combustion por debajo de los 200F (93C)?']),
                $row['Temperatura de Combustion'] = utf8_encode($row['Temperatura de Combustion']),
                $row['Es el ASIN (producto) un aerosol o gas comprimido?'] = utf8_encode($row['Es el ASIN (producto) un aerosol o gas comprimido?']),
                $row['Es el ASIN (producto) un material peligroso de acuerdo a la NOM-002-SCT-2011 y a la NOM-011-SCT2?'] = utf8_encode($row['Es el ASIN (producto) un material peligroso de acuerdo a la NOM-002-SCT-2011 y a la NOM-011-SCT2?']),
                $row['ID de Material Peligroso de Nacional Unidas'] = utf8_encode($row['ID de Material Peligroso de Nacional Unidas']),
                $row['Esta el etiquetado del producto en espanol?'] = utf8_encode($row['Esta el etiquetado del producto en espanol?']),
                $row['Esta el paquete en espanol?'] = utf8_encode($row['Esta el paquete en espanol?']),
                $row['Viene el producto con manual de instrucciones?'] = utf8_encode($row['Viene el producto con manual de instrucciones?']),
                $row['Esta el manual de instrucciones en espanol?'] = utf8_encode($row['Esta el manual de instrucciones en espanol?']),
                $row['--'] = utf8_encode($row['--']),
                $row['QOH'] = utf8_encode($row['QOH']),
                $row['vQOH'] = utf8_encode($row['vQOH']),
                $row['tQOH'] = utf8_encode($row['tQOH']),

            );
            $i++;
        }

        echo json_encode($responce);
    }







    /*
     * CSV Export
     */

    function csvExport($name)
    {

        header('Content-type: application/vnd.ms-excel');
        header("Content-Disposition: attachment; filename=" . $name . '_' . date("D-M-j") . ".xls");
        header("Pragma: no-cache");

        $buffer = $_POST['csvBuffer'];

        try {
            echo $buffer;
        } catch (Exception $e) {

        }
    }

   

}

?>
