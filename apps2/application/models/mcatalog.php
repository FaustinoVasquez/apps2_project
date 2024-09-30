<?php

class MCatalog extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    /*
     * Metodo que genera un combo con las lineas de productos
     */

    function fillProductLines($label= null) {
        $productLineOptions = array('All');
        $SQL = "SELECT ID,Name FROM [Inventory].[dbo].[ProductLines]";
        //poblamos el dropdown
        
        $label = !$label ? 'All' : $label;
             
        $result = $this->MCommon->fillDropDown($label, $SQL, 'ID', 'Name');
        return $result;
    }

    function fillAmazonChannels($label = null){

      $SQL = "SELECT  FNAmazonFeed.[Channel] as name
              FROM [Inventory].[dbo].[fn_GetAmazonInventoryFeed]() AS FNAmazonFeed
              GROUP BY FNAmazonFeed.[Channel]";
        
        $label = !$label ? 'All' : $label;
        
        //poblamos el dropdown
        $result = $this->MCommon->fillDropDown($label, $SQL, 'name', 'name');
        return $result;
    }
     
       
    /*
     * Metodo que genera un combo con las categorias que tenemos en Inventory
     */
     function fillCategories($label= null) {
        $SQL = "SELECT CAT.ID, CAT.Name  FROM [Inventory].[dbo].[Categories] AS CAT 
        WHERE CAT.ID IN (SELECT DISTINCT PC.CategoryID FROM [Inventory].[dbo].[ProductCatalog] AS PC) 
        order by Name asc";
        
        $label = !$label ? 'All' : $label;
        
        //poblamos el dropdown
        $result = $this->MCommon->fillDropDown($label, $SQL, 'ID', 'Name');
        return $result;
    }

    function fillCategories2($label= null) {
        $SQL = "SELECT CAT.ID, CAT.Name  FROM [Inventory].[dbo].[Categories] AS CAT 
        WHERE CAT.ID IN (SELECT DISTINCT PC.CategoryID FROM [Inventory].[dbo].[ProductCatalog] AS PC) 
        order by Name asc";
        
        $label = !$label ? 'All' : $label;
        
        //poblamos el dropdown
        $result = $this->MCommon->fillDropDown($label, $SQL, 'Name', 'Name');
        return $result;
    }

    function fillCategories3() {
        $SQL = "SELECT CAT.ID, CAT.Name  FROM [Inventory].[dbo].[Categories] AS CAT 
        WHERE CAT.ID IN (SELECT DISTINCT PC.CategoryID FROM [Inventory].[dbo].[ProductCatalog] AS PC) 
        order by Name asc";
        
        $labels = ['0' =>'All', '10000'=>'Packaging Materials','20000' =>'Raw Materials'];
        
        //poblamos el dropdown
        $result = $this->MCommon->fillDropDown3($labels, $SQL, 'ID', 'Name');
        return $result;
    }

    /*
     * Metodo que genera un combo la lista de los suppliers
     */
     function fillSuppliers($label= null) {
        $SQL = "SELECT SupplierId,SupplierName FROM [Inventory].[dbo].[SupplierInfo]";
        
        $label = !$label ? 'All' : $label;
        
        //poblamos el dropdown
        $result = $this->MCommon->fillDropDown($label, $SQL, 'SupplierId', 'SupplierName');
        return $result;
    }

    /*
     * Metodo que genera un combo con los Warehouses
     */
    function fillWarehouses() {
      //  $warehouseOptions = array('All');
        $SQL = "SELECT ID, Name from [Inventory].[dbo].Accounts where family = 'store'";
        //poblamos el dropdown
        $result = $this->MCommon->fillDropDown('All', $SQL, 'ID', 'Name');
        return $result;
    }


    /*
     * Metodo que genera un combo con los Warehouses
     */
    function fillCustomerCategory() {
      //  $warehouseOptions = array('All');
        $SQL = "SELECT DISTINCT CSPM.[CategoryID]
                 ,CUST.FirstName + ' ' + CUST.LastName + ' (' + CUST.Company + ')' AS [CustomerName]
                 ,CAT.[Name]
                FROM [Inventory].[dbo].[CustomerSpecificPricingManagement] AS CSPM
                LEFT OUTER JOIN [OrderManager].[dbo].[Customers] AS CUST ON (CSPM.[CustomerID] = CUST.[CustomerID])
                LEFT OUTER JOIN [Inventory].[dbo].[Categories] AS CAT ON (CSPM.[CategoryID] = CAT.[ID])";
        //poblamos el dropdown
        $result = $this->MCommon->fillDropDown('All', $SQL, 'CategoryID', 'Name');
        return $result;
    }


    /*
     * Metodo que genera un combo con los Warehouses
     */
    function fillCustomerName() {
      //  $warehouseOptions = array('All');
        $SQL = "SELECT DISTINCT CSPM.[CustomerID]
                   ,CUST.FirstName + ' ' + CUST.LastName + ' (' + CUST.Company + ')' AS [CustomerName]
                FROM [Inventory].[dbo].[CustomerSpecificPricingManagement] AS CSPM
                LEFT OUTER JOIN [OrderManager].[dbo].[Customers] AS CUST ON (CSPM.[CustomerID] = CUST.[CustomerID])
                LEFT OUTER JOIN [Inventory].[dbo].[Categories] AS CAT ON (CSPM.[CategoryID] = CAT.[ID])";
        //poblamos el dropdown
        $result = $this->MCommon->fillDropDown('All', $SQL, 'CustomerID', 'CustomerName');
        return $result;
    }



    /*
     * Metodo que genera un combo con las lineas de productos
     */

    function FillPartNumber() {
        $SQL = "SELECT distinct PartNumber FROM [Inventory].[dbo].[Compatibility]";
        //poblamos el dropdown
        $result = $this->MCommon->fillDropDown('', $SQL, 'PartNumber', 'PartNumber');
        return $result;
    }
    
    /*
     * Metodo que genera un combo con los canales de venta
     */

    function fillChannelName() {
        $SQL = "SELECT distinct ChannelName FROM [Inventory].[dbo].[Amazon]";
        //poblamos el dropdown
        $result = $this->MCommon->fillDropDown('', $SQL, 'ChannelName', 'ChannelName');
        return $result;
    }

    /*
     * Metodo que genera un combo con los canales de venta para el sistema utilizado por Depto. Amazon
     */

    function fillChannelNameAmazon() {
        $SQL = "SELECT distinct ChannelName FROM [Inventory].[dbo].[Amazon]";
        //poblamos el dropdown
        $result = $this->MCommon->fillDropDown('All', $SQL, 'ChannelName', 'ChannelName');
        return $result;
    }
    
    function fillAmazonFBAChannelName(){
        $SQL = "SELECT DISTINCT Channel FROM [Inventory].[dbo].[AmazonFBA]  WHERE NOT Channel is null ORDER BY Channel";
        $result = $this->MCommon->fillDropDown('', $SQL, 'Channel', 'Channel');
        return $result;
    }
    /*
     * Metodo que genera un combo con los Country Code
     */

    function fillCountryCode() {
        $SQL = "SELECT distinct CountryCode FROM [Inventory].[dbo].[Amazon] WHERE CountryCode != '' GROUP BY countryCode";
        //poblamos el dropdown
        $result = $this->MCommon->fillDropDown('All', $SQL, 'CountryCode', 'CountryCode');
        return $result;
    }
    
     /*
     * Metodo que genera un combo con los Category segun OM
     */

    function fillCategory() {
        $SQL = "SELECT distinct Category FROM [Inventory].[dbo].[Amazon] order by Category asc";
        //poblamos el dropdown
        $result = $this->MCommon->fillDropDown('All', $SQL, 'Category', 'Category');
        return $result;
    }

    function fillCategoryProducto() {
        $SQL = " SELECT Name FROM [Inventory].[dbo].[Categories] Order by Name";
        //poblamos el dropdown
        $result = $this->MCommon->fillDropDown('All', $SQL, 'Name', 'Name');
        return $result;
    }

    function fillSuppliersDrop() {
        $SQL = " SELECT SupplierName FROM [Inventory].[dbo].[SupplierInfo] Order By SupplierName ";
        //poblamos el dropdown
        $result = $this->MCommon->fillDropDown('All', $SQL, 'SupplierName', 'SupplierName');
        return $result;
    }

    /*
     * Metodo que genera un combo con los Category segun OM
     */

    function fillCategoryName() {
        $SQL = "SELECT DISTINCT CAT.[Name]
                FROM [Inventory].[dbo].[AmazonAnalysisTable] AS AAT
                LEFT OUTER JOIN [Inventory].[dbo].[ProductCatalog] AS PC ON (AAT.[SKU] = PC.[ID])
                LEFT OUTER JOIN [Inventory].[dbo].[Categories] AS CAT ON (PC.[CategoryID] = CAT.[ID])
                ORDER BY CAT.[Name] ASC";
        //poblamos el dropdown
        $result = $this->MCommon->fillDropDown('All', $SQL, 'Name', 'Name');
        return $result;
    }
    
    /*
     * Metodo que genera un combo con manufactures
     */

    function fillManufacturer() {
        $SQL = "SELECT DISTINCT Manufacturer FROM [Inventory].[dbo].[ProductCatalog] WHERE Categoryid in (24)";
        //poblamos el dropdown
        $result = $this->MCommon->fillDropDown('All', $SQL, 'Manufacturer', 'Manufacturer');
        return $result;
    }

    /*
     * Metodo que genera un combo con ListingType
     */

    function fillListingType() {
        $SQL = "SELECT DISTINCT [ListingType] FROM [Inventory].[dbo].[ChannelAdvisorFeed] ORDER BY ListingType ASC";
        //poblamos el dropdown
        $result = $this->MCommon->fillDropDown('All', $SQL, 'ListingType', 'ListingType');
        return $result;
    }

    /*
     * Metodo que genera un combo con manufactures
     */

    function fillManufacturer2() {
        $SQL = "SELECT DISTINCT [Manufacturer] FROM [Inventory].[dbo].[Compatibility] ORDER BY [Manufacturer] ASC";
        //poblamos el dropdown
        $result = $this->MCommon->fillDropDown('All', $SQL, 'Manufacturer', 'Manufacturer');
        return $result;
    }


    /*
     * Metodo que genera un combo con Categorias de Partes
     */

    function fillCategoryParts() {
        $SQL = "SELECT DISTINCT [CATEGORY] FROM [PartsData].[dbo].[PARTSINVENTORY] WHERE CATEGORY != ''";
        //poblamos el dropdown
        $result = $this->MCommon->fillDropDown('All', $SQL, 'CATEGORY', 'CATEGORY');
        return $result;
    }

    /*
     * Metodo que genera un combo con los tipos de Carros
     */

    function fillCartName() {
        $SQL = "SELECT CartName FROM OrderManager.dbo.ShoppingCarts WHERE CartName IN (SELECT [Cart] FROM Inventory.dbo.PendingShipments)";
        //poblamos el dropdown
        $result = $this->MCommon->fillDropDown('All', $SQL, 'CartName', 'CartName');
        return $result;
    }

    /*
     * Metodo que genera un combo con Tamplate Store
     */

    function fillTemplateStore() {
        $SQL = "SELECT Distinct TemplateStoreID FROM [Inventory].[dbo].[ChannelAdvisorTemplates]";
        //poblamos el dropdown
        $result = $this->MCommon->fillDropDown('All', $SQL, 'TemplateStoreID', 'TemplateStoreID');
        return $result;
    }

    /*
     * Metodo que genera un combo con Categorias de Amazon
     */

    function fillCategoryAmazon() {
        $SQL = "SELECT Category FROM Inventory.dbo.Amazon WHERE ISNULL(Category,'') <> '' GROUP BY Category";
        //poblamos el dropdown
        $result = $this->MCommon->fillDropDown('All', $SQL, 'Category', 'Category');
        return $result;
    }
     
    /*
     * Metodo que agrega la busqueda por Categorias al Where
     */

    function filterByCategory($categoryID) {
        $where = '';
        if ($categoryID != 0) {
            $where = ' and (categoryID =' . $categoryID . ')';
        }
        return $where;
    }

    
    /*
     * Metodo que genera un combo con los diferentes metodos de envio
     */

    function fillShipingMethod() {
        $SQL = "SELECT DISTINCT [ShippingMethod]  FROM [Inventory].[dbo].[PendingShipments]";
        //poblamos el dropdown
        $result = $this->MCommon->fillDropDown('All', $SQL, 'ShippingMethod', 'ShippingMethod');
        return $result;
    }

    /*
     * Metodo que agrega la busqueda por cantidad ordenada al where
     */

    function filterByQtyOrdered() {
        return ' and ([Inventory].[dbo].fn_QB_PO(ID) <> 0)';
    }

    /*
     * Metodo que genera el select para la busqueda por DOIR
     */

    function filterByDOIR($historyDays) {
        $today = date('m/d/Y');
        $Idate = $this->MCommon->fixIDatebyDays($today, $historyDays);
        $Fdate = date("Y-m-d");
        $select = "SELECT ID
                        ,[Inventory].[dbo].fn_Vendor_Part_Number(ID) as VPN
                        ,Manufacturer
                        ,Name
                        ,[Inventory].[dbo].fn_get_Global_Stock(id) as CurrentStock
                        ,VirtualStock
                        ,PriceFloor
                        ,PriceCeiling
                        ,TargetInventory
                        ,[Inventory].[dbo].fn_Inventoty_Projection(ID,'{$Idate}','{$Fdate}') as projection
			,[Inventory].[dbo].fn_QB_PO(ID) as BackOrders
			,[Inventory].[dbo].fn_QB_ExpectedDate(ID) as ExpectedDate
			,[Inventory].[dbo].fn_GetProductLineName(ProductLineID) as ProductLineID";
        return $select;
    }

    /*
     * Metodo que agrega la funcionalidad de filtrar por fecha
     */

    function filterbyDate($InitialDate, $FinalDate) {
        return "  and (OrderDate between '{$InitialDate}' and '{$FinalDate}')";
    }

    /*
     * Metodo que obtiene todos un registro de la tabla productCatalog
     */

    function showSku($id) {
        $SQL = "SELECT a.ID as SKU, a.[Manufacturer], a.[ManufacturerPN], b.[Name] as Category, c.[Name] as ProductLine, [Inventory].[dbo].fn_get_countryname(a.[CountryOfOrigin]) as country,
                     a.[MeasuringUnitCode], a.[AssemblyRequired], a.[Serialized], a.[IsSellable], a.[Condition], a.[AlwaysInStock], a.[Name], a.[Description], a.[UpcCode], a.[Keywords], a.[UnitDim] as 'UnitDim (OLD)', a.[UnitWeight] as 'UnitWeight (OLD)', a.[CaseQty],
                     a.[CaseDim], a.[CaseWeight],[Inventory].[dbo].fn_GetShortUserName(a.[LastModifiedUserID]) as LastModifiedUserName,
                     a.[InventoryMin], a.[InventoryMax], a.[AvgStandardShipCost],Avg1DayShipCost, Avg2DayShipCost,AvgIntEconomyShipCost,AvgIntPriorityShipCost, AvgWeightPounds,a.[CustomField01], a.[CustomField02],a.[CustomField03], a.[CustomField04], a.[CustomField05], a.[CustomField06], a.[CustomField07],
                     a.[CustomField08], a.[CustomField09], a.[CustomField10], a.[CustomField11], a.[CustomField12],a.[CustomField13], a.[CustomField14], a.[CustomField15], a.[CustomField16], a.[CustomField17], a.[CustomField18], a.[CustomField19], a.[CustomField20] 
              FROM [Inventory].[dbo].ProductCatalog a, [Inventory].[dbo].Categories b, [Inventory].[dbo].ProductLines c
              WHERE (a.ID =" . $id . ") and (a.CategoryID = b.ID) and (a.CategoryID = b.ID) and (a.ProductLineID = c.ID)";
               
        $data = $this->MCommon->getOneRecord($SQL);
        
        return $data;
    }

    //---------------------------------------------
    //  Custom Fields Metodos
    //---------------------------------------------


    function showCustomFields($id) {

        $SQL = "SELECT b.[CustomField01],b.[CustomField02],b.[CustomField03],b.[CustomField04],b.[CustomField05],b.[CustomField06],b.[CustomField07],b.[CustomField08],b.[CustomField09],
                       b.[CustomField10],b.[CustomField11],b.[CustomField12],b.[CustomField13],b.[CustomField14],b.[CustomField15],b.[CustomField16],b.[CustomField17],b.[CustomField18],
                       b.[CustomField19],b.[CustomField20]
               FROM [Inventory].[dbo].ProductCatalog a, [Inventory].[dbo].ProductLines b
               where (a.[ID] =" . $id . ") and (a.ProductLineID = b.ID)";
        $data = $this->MCommon->getOneRecord($SQL);
        return $data;
    }

    

    //---------------------------------------------
    //  End Custom Fields Metodos
    //---------------------------------------------
    //---------------------------------------------
    //  History Metodos
    //---------------------------------------------


    function showHistory($id, $InitialDate, $FinalDate) {
        $data = array();
        //  $InitialDate = date('Y-m-d', strtotime("-1 month"));
        //   $FinalDate = date("Y-m-d", strtotime("+1 day"));

        $sql = "SELECT a.Date as Date, flow,
            case
                when flow = 1 then 'Added'
                when flow = 2 then 'Removed'
                when flow = 3 then 'Transfered'
                else 'UnknowAdjustment'
            end
            as 'AdjustmentType',
            a.Origin as Origin,[Inventory].[dbo].fn_GetAccountName(a.origin)as OriginName,
            a.Destination as Destination,[Inventory].[dbo].fn_GetAccountName(a.Destination) as DestinationName,
            b.ProductCatalogID as SKU,
            b.Quantity as Quantity
            FROM [Inventory].[dbo].InventoryAdjustments a, [Inventory].[dbo].InventoryAdjustmentDetails b
            WHERE (a.ID = b.InventoryAdjustmentsID)and (b.ProductCatalogID =" . $id . ")
                   and (a.Date between '" . $InitialDate . "' and '" . $FinalDate . "')";


        $result = mssql_query($sql);
        while ($row = mssql_fetch_array($result, MSSQL_ASSOC)) {
            $data[] = $row;
        }

        return $data;
    }

    /*
     * Funcion calcInventario Utilizada en el proceso de history
     */

    function calcInventory($sku, $date) {

        $sql = " SELECT SUM(case a.Flow when '1' then 1 when '2' then -1 end * b.Quantity) as stock FROM InventoryAdjustments a,  InventoryAdjustmentDetails b
                   WHERE ( a.ID = b.InventoryAdjustmentsID )
           
                   AND ( b.ProductCatalogID = {$sku})
                   AND (a.date <= '{$date}')";
        $result = $this->MCommon->getOneRecord($sql);

        return($result['stock']);
    }

    //---------------------------------------------
    //  END History Metodos
    //---------------------------------------------
    //---------------------------------------------
    //  Inventory Details Metodos
    //---------------------------------------------

    function Wharehouse() {
        $SQL = "SELECT ID, name from [Inventory].[dbo].Accounts where family = 'store'";
        return $this->MCommon->getSomeRecords($SQL);
    }

    function Quantity($sku, $wrhse) {

        $SQL = "SELECT [Inventory].[dbo].fn_GetAccountName({$wrhse}) as ID
                                    ,[ProductCatalogName]
                                    ,[Quantity] as CurrentStock
                                FROM [Inventory].[dbo].[Inventory] a,
                                    [Inventory].[dbo].Accounts b
                                WHERE (a.AccountID = b.ID) and
                                    (a.AccountID = {$wrhse}) and (ProductCatalogID = {$sku}) ";

        return $this->MCommon->getOneRecord($SQL);
    }

    //---------------------------------------------
    //  END Inventory Details Metodos
    //---------------------------------------------
    //---------------------------------------------
    //  TotalCost Metodos
    //---------------------------------------------

    function getCostbyProducLineAtDate($dateto) {
        $SQL = "Select ProductlineID, 
                       [Inventory].[dbo].fn_GetProductLineName(ProductlineID) as ProductLineName,
                      sum([Inventory].[dbo].fn_StockAtDate(id,'{$dateto}') * UnitCost) as ProductLineCost
               FROM [Inventory].[dbo].ProductCatalog
               group by ProductLineID";
        $result = $this->MCommon->getSomeRecords($SQL);
        return $result;
    }

    function getCostbyProducLine() {
        $SQL = "SELECT ProductlineID,
                  [Inventory].[dbo].fn_GetProductLineName(ProductlineID) as ProductLineName, 
                  sum (CurrentStock * UnitCost) as ProductLineCost
            FROM ProductCatalog 
            GROUP BY ProductlineID 
            ORDER BY ProductlineID";


        $result = $this->MCommon->getSomeRecords($SQL);
        return $result;
    }

    //---------------------------------------------
    //  END TotalCost Metodos
    //---------------------------------------------
    //
  



   //---------------------------------------------
    //  PoS Metodos
    //---------------------------------------------

  function getPos($datefrom, $dateto){
       $SQL = "SELECT DISTINCT comments FROM [Inventory].[dbo].[Bins_History]  WHERE Scancode = 'IN55' and (Stamp between '{$datefrom} 00:00:00' AND '{$dateto} 23:59:59') Order by comments desc";

      $result = $this->MCommon->fillDropDown1('All', $SQL, 'comments', 'comments');

      return $result;

  }

    //---------------------------------------------
    //  EndPoS Metodos
    //---------------------------------------------
    //
    //
  

   //---------------------------------------------
    //  Bin Metodos
    //---------------------------------------------

  function filterBins(){
       $SQL = "SELECT DISTINCT [Bin_Id] FROM [Inventory].[dbo].[Bins]";

      $result = $this->MCommon->fillautocompleteajax($SQL,'Bin_Id');

      return $result;

  }

    //---------------------------------------------
    //  EndPoS Metodos
    //---------------------------------------------
    //
    //
  

    /*
     * Metodo que extrae el categoryID de la tabal de productCatalog
     */
     function getCategoryId($sku) {
        $SQL = "SELECT categoryId FROM [Inventory].[dbo].[productCatalog] WHERE ID =".$sku;
        
        $result = $this->db->query($SQL)->row()->categoryId;

        return $result;
    }



    function getTransferReasons(){
        $SQL = "SELECT [listvalue],
                        CASE When listname =  'BINRSNIN' THEN 'In_'+ listdisplay
                             When listname = 'BINRSNOUT' THEN 'Out_'+listdisplay 
                        END as listdisplay
                FROM [Inventory].[dbo].[All_List] 
                WHERE listname IN ('BINRSNIN','BINRSNOUT') and(listdisplay <>'Placeholder')";

         $result = $this->MCommon->fillDropDown1('All Reasons', $SQL, 'listvalue', 'listdisplay');

      return $result;
    }
    

}

?>
