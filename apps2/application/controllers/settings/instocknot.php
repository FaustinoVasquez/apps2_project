<?php

if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

class Instocknot extends BP_Controller {

	public function __construct() {
		parent::__construct();

		$is_logged_in = $this->session->userdata('is_logged_in');

		if (!isset($is_logged_in) || $is_logged_in != true) {
			redirect(base_url(), 'refresh');
		}
		//    if ($this->MUsers->isValidUser($this->session->userdata('userid'), 882100) != 1) {
		//           redirect('Catalog/prodcat', 'refresh');
		//       }

	}

	function index() {
		// Define Meta
		$this->_title = "MI Technologiesinc - In Stock Notifications";

		$this->_description = "In Stock Notifications";


		 $this->css = array('form.css', 'fluid.css', 'table.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css', 'menu.css',);

  //       // Define custom javascript
        $this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jqgrid/i18n/grid.locale-en.js', 'jqgrid/jquery.jqGrid.min.js', 'jqgrid/jquery.jqGrid.fluid.js', 'site.js');

        $this->load->library('Layout');
        $menu = new Layout;

         $data = array(
            'menu' => $menu->show_menu($this->MUsers->getUserFullName($this->session->userdata('userid'))),
            'from' => 'settings/instocknot',
            'caption' => 'In Stock Notifications',
            'search' => ''
        );

		$data['headers'] = "['Id','SKU','QtyRequired','Notes']";

		$data['body'] = "[
		        {name:'Id',index:'Id', width:60, align:'center', hidden:true},
                {name:'ProductCatalogID',index:'ProductCatalogID', width:60, align:'center',editable:true,edittype: 'custom',editoptions:{'custom_element' : autocomplete_element,'custom_value' : autocomplete_value,style:'width:85%'},editrules: { required: true, number: true}},
                {name:'QtyRequired',index:'QtyRequired', width:60, align:'center',editable:true,editoptions:{style:'width:85%'},editrules: { required: true, number: true}},
                {name:'Notes',index:'Notes', width:60, align:'center',editable:true,editoptions:{style:'width:85%'}},
                ]";

		//Generar el contenido....
		$this->build_content($data);
		$this->render_page();
	}

	function getData() {


		$dataSearch = isset($_REQUEST['ds'])?$_GET['ds']:'';

		$page  = isset($_REQUEST['page'])?$_REQUEST['page']:1;// get the requested page
		$limit = isset($_REQUEST['rows'])?$_REQUEST['rows']:10;// get how many rows we want to have into the gri
		$sidx  = isset($_REQUEST['sidx'])?$_REQUEST['sidx']:'ProductCatalogID';// get index row - i.e. user click to sort
		$sord  = isset($_REQUEST['sord'])?$_REQUEST['sord']:'desc';

		$userId = $this->session->userdata('userid');

		//Select utilizado para realizar el conteo del numero de registros devueltos por la consulta.
		$selectCount = 'Select count(*) as rowNum';
		//Select General con los campos necesarios para la vista
		$select = "SELECT  [Id]
						  ,[ProductCatalogID]
					      ,[QtyRequired]
					      ,[Notes]";

		//Se utiliza en la seccion que extrae los registros de la hoja activa
		$selectSlice = "SELECT [Id],
							   [ProductCatalogID]
						      ,[QtyRequired]
						      ,[Notes]";

		$from  = ' from ';
		$table = '[Inventory].[dbo].[InStockNotification]';
		$where = '';

		//Campos en sobre los que se haran busquedas de palabras..
		$wherefields = array('ProductCatalogID', 'QtyRequired', 'Notes');
		//creamos el were concatenando todos los nombres de campos y las palabras de busqueda
		$where .= $this->MCommon->concatAllWerefields($wherefields, $dataSearch);

		$where .= " and [UserID] = {$userId} ";


		$SQL = "{$selectCount}{$from}{$table}{$where}";


		$result = $this->MCommon->getOneRecord($SQL);
		$count  = $result['rowNum'];

		if ($count > 0) {
			$total_pages = ceil($count/$limit);
		} else {
			$total_pages = 0;
		}
		if ($page > $total_pages) {
			$page = $total_pages;
		}

		$start  = $limit*$page-$limit;// do not put $limit*($page - 1)
		$start  = ($start < 0)?0:$start;
		$finish = $start+$limit;

		$SQL = "WITH mytable AS ({$select}, ROW_NUMBER() OVER (ORDER BY {$sidx} {$sord}) AS RowNumber
                                FROM {$table}{$where})
				{$selectSlice}, RowNumber
               FROM mytable
               WHERE RowNumber BETWEEN {$start} AND {$finish}";


   

		$result = $this->MCommon->getSomeRecords($SQL);

		$responce          = new stdClass();
		$responce->page    = $page;
		$responce->total   = $total_pages;
		$responce->records = $count;
		$i                 = 0;

		foreach ($result as $row) {
			$responce->rows[$i]['id']   = $row['Id'];
			$responce->rows[$i]['cell'] = array($row['Id'],
				$row['ProductCatalogID'],
				$row['QtyRequired'],
				$row['Notes'],		
			);
			$i++;
		}

		echo json_encode($responce);
	}




    function crudOperation(){
    	$id = $this->input->post('id');

		$ProductCatalogID = $this->input->post('ProductCatalogID'); 
		$QtyRequired = $this->input->post('QtyRequired');
		$Notes =  $this->input->post('Notes');
		$UserID = $this->session->userdata('userid');
	
 	
	if ($_POST['oper']== 'add'){

	    
	    $SQL = "insert into [Inventory].[dbo].[InStockNotification] 
	    		(	 ProductCatalogID
	    		  	,QtyRequired
	    		  	,Notes
	    		  	,UserID
	    		) 
	           	values
	           	(	 '{$ProductCatalogID}'
	           		, {$QtyRequired}
	           		,'{$Notes}'
	           		, {$UserID}
	           	)";

	}

	if ($_POST['oper']== 'edit'){
	    
	    $SQL = "update [Inventory].[dbo].[InStockNotification] 
	            set ProductCatalogID = {$ProductCatalogID}
	               ,QtyRequired= {$QtyRequired}
	               ,Notes ='{$Notes}'
	               ,UserID = {$UserID}
	            where Id={$id} ";
	}


	if ($_POST['oper']== 'del'){
	    
	    $SQL = "DELETE FROM [Inventory].[dbo].[InStockNotification] WHERE Id={$id} ";
	}


	$this->MCommon->saveRecord($SQL,'Inventory');
	    
    }


    public function autocompletesku(){

    	$term = $this->input->get('term');

    	$SQL = "SELECT [ID] FROM [Inventory].[dbo].[ProductCatalog] WHERE ID like '{$term}%' ";
     	$query = $this->db->query($SQL);
     	$data = "[";

		if ($query->num_rows() > 0)
		{
		   foreach ($query->result() as $row)
		   {
		       $data .= '"'.$row->ID .'",' ;
		   }
		}

        $data = substr($data, 0, -1);
		echo $data .']';
    }



	function csvExport($name) {

        header('Content-type: application/vnd.ms-excel');
        header("Content-Disposition: attachment; filename=" . $name . '_' . date("D-M-j") . ".xls");
        header("Pragma: no-cache");

        $buffer = $_POST['csvBuffer'];

        try {
            echo $buffer;
        } catch (Exception $e) {
            
        }
    }


  //   private function _checkDirectory($sku ='999999'){

  //   	if (!is_dir('uploads/'.$sku)) {
  //   		$old = umask(0);
  //   		mkdir('./uploads/' . $sku, 0777, TRUE);
  //   		chmod('./uploads/' . $sku, 0777);

  //   		umask($old);
  //   		 copy('./uploads/index/index.php', './uploads/'.$sku.'/index.php');
  //   		return true;
		// } 

  //   	return false;

  //   }

  //   private function _countFilesInDirectory($sku){
		
		// $dir = './uploads/'.$sku;
    	
  //   	$files = glob($dir . "*.jpg");
		// if ($files){
		//  return count($files);
		// }

		// return false;

  //   }

  


  //   public function uploadImages(){

  //   	$sku = $this->input->post('sku');
  //   	$filecount = 0;
   
  //   	if(!$this->_checkDirectory($sku)){
		// 	$filecount = $this->_countFilesInDirectory($sku);
		// }


  //   	$status = "";
  //       $msg = "";
  //       $file_element_name = 'image';

  //   	$config['upload_path'] = './uploads/';
  //       $config['allowed_types'] = 'jpg';
  //       $config['max_size'] = 1024 * 8;


  //        $this->load->library('upload',$config);

  //       if ($_FILES['image']) {
  //       	$images= $this->_upload_files('image',$sku,$filecount);

  //   	}
  //       echo json_encode($images);
  //   }

 // /**
 // * @return array an array of your files uploaded.
 // */

	// private function _upload_files($field='userfile',$sku,$count){
	//     $files = array();
	//     foreach( $_FILES[$field] as $key => $all ){
	//         foreach( $all as $i => $val ){
	//             $files[$i][$key] = $val; 
	//         }

	//     }

	//     $files_uploaded = array();
	    
	//     for ($i=0; $i < count($files); $i++) { 
	//     	$count++;      
	//         $_FILES[$field] = $files[$i];
	        
	//         if ($this->upload->do_upload($field)){

	//         	$type = explode(".", $_FILES[$field]['name']);
	//         	$type = $type[count($type)-1];
	//         	$url = './uploads/'.$sku.'/'.$sku.'_'.$count.'.'.$type;

	//         	if(is_uploaded_file($_FILES[$field]['tmp_name'])){
	//         		move_uploaded_file($_FILES[$field]['tmp_name'], $url);
	//         		unlink('./uploads/'.$_FILES[$field]['name']);
	//         	}

	//             $files_uploaded = 1;
	//         }
	//         else
	//             $files_uploaded = 0;
	//     }
	//     return $files_uploaded;
	// }

 //    function getTabs(){

 //        $sku = $this->input->get('id');

 // 		$images =  "<div><object type='text/html' data='http://remotespict.mitechnologiesinc.com/{$sku}/' width ='100%' height='350px' style='overflow:auto;border:1px ridge #F0F0F0 '></object></div>";

       
 //        $tabs = '<div class="tabs">
 //                    <ul>
 //                        <li><a href="#tabs-1">Images</a></li>
 //                    </ul>
 //                    <div id="tabs-1"><center>'.$images.'</center></div>
 //                </div>';
 //        echo $tabs;
 //    }

}

?>

