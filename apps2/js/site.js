   $(function() {
        $( ".date-pick" ).datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat:"mm/dd/yy"
        });
    });


     myajax = function(myValue,myUrl){
     var returnData;
     
     var request= $.ajax({
            url: myUrl,
            type: "GET",
            dataType: "json",
            async: false
        });
        request.done(function( msg ) {
            returnData = msg;
        });
        request.fail(function( jqXHR, textStatus ) {
            alert( "Request failed: " + textStatus );
        });   
        
        return returnData;
 };


function exportExcel(grid,name)
{
    var mya=new Array();
    mya=$(grid).getDataIDs();  // Get All IDs
    var data=$(grid).getRowData(mya[0]);     // Get First row to get the labels
    if (data['stats']){delete data['stats']}
    var colNames=new Array();
    var ii=0;
    for (var i in data){colNames[ii++]=i;}    // capture col names
    var html="";
    for(k=0;k<colNames.length;k++)
    {
        html=html+colNames[k]+"\t";     // output each Column as tab delimited
    }
    html=html+"\n";                    // Output header with end of line
    for(i=0;i<mya.length;i++)
    {
        data=$(grid).getRowData(mya[i]); // get each row
        for(j=0;j<colNames.length;j++)
        {   

            if (data[colNames[j]].substr(0,2) =='<a'){               
                data[colNames[j]]=data[colNames[j]].toUpperCase();
                var startPos=(data[colNames[j]].indexOf('>')) +1;
                var endPos=(data[colNames[j]].indexOf('<',startPos));
                data[colNames[j]]=data[colNames[j]].toString().substring(startPos,endPos);
            }

            html=html+data[colNames[j]]+"\t"; // output each Row as tab delimited
        }

        html=html+"\n";  // output each row with end of line
    }

    html=html+"\n";  // end of line at the end
    document.forms[1].csvBuffer.value=html;
    document.forms[1].method='POST';
    document.forms[1].action='csvExport/'+name; 
    document.forms[1].target='_blank';
    document.forms[1].submit();
}

function exportExcel1(grid,name)
{
    var mya=new Array();
    mya=$(grid).getDataIDs();  // Get All IDs
    var data=$(grid).getRowData(mya[0]);     // Get First row to get the labels
    if (data['stats']){delete data['stats']}
    if (data['ID']){delete data['ID']}
    if (data['IsActive']){delete data['IsActive']}
    if (data['CustomerID']){delete data['CustomerID']}
    if (data['CategoryID']){delete data['CategoryID']}
    if (data['CustomerName']){delete data['CustomerName']}
    var colNames=new Array();
    var ii=0;
    for (var i in data){colNames[ii++]=i;}    // capture col names
    var html="";
    for(k=0;k<colNames.length;k++)
    {
        html=html+colNames[k]+"\t";     // output each Column as tab delimited
    }
    html=html+"\n";                    // Output header with end of line
    for(i=0;i<mya.length;i++)
    {
        data=$(grid).getRowData(mya[i]); // get each row
        for(j=0;j<colNames.length;j++)
        {   

            if (data[colNames[j]].substr(0,2) =='<a'){               
                data[colNames[j]]=data[colNames[j]].toUpperCase();
                var startPos=(data[colNames[j]].indexOf('>')) +1;
                var endPos=(data[colNames[j]].indexOf('<',startPos));
                data[colNames[j]]=data[colNames[j]].toString().substring(startPos,endPos);
            }

            html=html+data[colNames[j]]+"\t"; // output each Row as tab delimited
        }

        html=html+"\n";  // output each row with end of line
    }

    html=html+"\n";  // end of line at the end
    document.forms[1].csvBuffer.value=html;
    document.forms[1].method='POST';
    document.forms[1].action='csvExport/'+name; 
    document.forms[1].target='_blank';
    document.forms[1].submit();
}


function exportExcel2(grid,myaction)
{
    var mya=new Array();
    mya=$(grid).getDataIDs();  // Get All IDs
    var data=$(grid).getRowData(mya[0]);     // Get First row to get the labels
    if (data['stats']){delete data['stats']}
    var colNames=new Array();
    var ii=0;
    for (var i in data){colNames[ii++]=i;}    // capture col names
    var html="";
    for(k=0;k<colNames.length;k++)
    {
        html=html+colNames[k]+"\t";     // output each Column as tab delimited
    }
    html=html+"\n";                    // Output header with end of line
    for(i=0;i<mya.length;i++)
    {
        data=$(grid).getRowData(mya[i]); // get each row
        for(j=0;j<colNames.length;j++)
        {   

            if (data[colNames[j]].substr(0,2) =='<a'){               
                data[colNames[j]]=data[colNames[j]].toUpperCase();
                var startPos=(data[colNames[j]].indexOf('>')) +1;
                var endPos=(data[colNames[j]].indexOf('<',startPos));
                data[colNames[j]]=data[colNames[j]].toString().substring(startPos,endPos);
            }

            html=html+data[colNames[j]]+"\t"; // output each Row as tab delimited
        }

        html=html+"\n";  // output each row with end of line
    }

    html=html+"\n";  // end of line at the end
    document.forms[1].csvBuffer.value=html;
    document.forms[1].method='POST';
    document.forms[1].action= myaction; 
    document.forms[1].target='_blank';
    document.forms[1].submit();
}


function exportExcel4(grid,name)
{
    var mya=new Array();
    mya=$(grid).getDataIDs();  // Get All IDs
    var data=$(grid).getRowData(mya[0]);     // Get First row to get the labels
    if (data['stats']){delete data['stats']}
    var colNames=new Array();
    var ii=0;
    for (var i in data){colNames[ii++]=i;}    // capture col names
    var html="";
    for(k=0;k<colNames.length;k++)
    {
        html=html+colNames[k]+"\t";     // output each Column as tab delimited
    }
    html=html+"\n";                    // Output header with end of line
    for(i=0;i<mya.length;i++)
    {
        data=$(grid).getRowData(mya[i]); // get each row
        for(j=0;j<colNames.length;j++)
        {   

            if (data[colNames[j]].substr(0,2) =='<a'){               
                data[colNames[j]]=data[colNames[j]].toUpperCase();
                var startPos=(data[colNames[j]].indexOf('>')) +1;
                var endPos=(data[colNames[j]].indexOf('<',startPos));
                data[colNames[j]]=data[colNames[j]].toString().substring(startPos,endPos);
            }

            html=html+data[colNames[j]]+"\t"; // output each Row as tab delimited
        }

        html=html+"\n";  // output each row with end of line
    }

    html=html+"\n";  // end of line at the end
    document.forms[4].csvBuffer.value=html;
    document.forms[4].method='POST';
    document.forms[4].action='csvExport/'+name; 
    document.forms[4].target='_blank';
    document.forms[4].submit();
}


function exportExcel5(grid,name)
{
    var mya=new Array();
    mya=$(grid).getDataIDs();  // Get All IDs
    var data=$(grid).getRowData(mya[0]);     // Get First row to get the labels
    if (data['stats']){delete data['stats']}
    var colNames=new Array();
    var ii=0;
    for (var i in data){colNames[ii++]=i;}    // capture col names
    var html="";
    for(k=0;k<colNames.length;k++)
    {
        html=html+colNames[k]+"\t";     // output each Column as tab delimited
    }
    html=html+"\n";                    // Output header with end of line
    for(i=0;i<mya.length;i++)
    {
        data=$(grid).getRowData(mya[i]); // get each row
        for(j=0;j<colNames.length;j++)
        {   

            if (data[colNames[j]].substr(0,2) =='<a'){               
                data[colNames[j]]=data[colNames[j]].toUpperCase();
                var startPos=(data[colNames[j]].indexOf('>')) +1;
                var endPos=(data[colNames[j]].indexOf('<',startPos));
                data[colNames[j]]=data[colNames[j]].toString().substring(startPos,endPos);
            }

            html=html+data[colNames[j]]+"\t"; // output each Row as tab delimited
        }

        html=html+"\n";  // output each row with end of line
    }

    html=html+"\n";  // end of line at the end
    document.forms[2].csvBuffer.value=html;
    document.forms[2].method='POST';
    document.forms[2].action='csvExport/'+name; 
    document.forms[2].target='_blank';
    document.forms[2].submit();
}




function showDialog(sku,baseUrl)
{   
    //var newTitle = "SKU( "+sku+" ) Details." 
   // $("#dialog-modal").attr("title", newTitle);
   // 
    $("#dialog-modal").empty();
    $("#dialog-modal").dialog(
    {
        width: 1500,
        height: 530,
        modal:true,
        open: function(event, ui)
        {
            var prueba = ('<div id="tabs">'+
                            '<ul>'+
                                '<li><a href="#tabs-1">SkuDAta</a></li>'+
                                '<li><a href="#tabs-2">Custom Fields</a></li>'+
                                '<li><a href="#tabs-3">Compatibilities</a></li>'+
                                '<li><a href="#tabs-4">Images</a></li>'+
                                '<li><a href="#tabs-5">Attachment</a></li>'+
                                '<li><a href="#tabs-6">History</a></li>'+
                                '<li><a href="#tabs-7">Inventory Details</a></li>'+
                                '<li><a href="#tabs-8">Assambly Requirements</a></li>'+
                                '<li><a href="#tabs-9">vQOH Details</a></li>'+
                                '<li><a href="#tabs-10">Cost Info</a></li>'+
                                '<li><a href="#tabs-11">PO History</a></li>'+
                                '<li><a href="#tabs-12">Advanced</a></li>'+
                            '</ul>'+
                            '<div id="tabs-1">'+
                                '<iframe src="'+baseUrl+'index.php/Tabs/showskudata/'+sku+'" width="100%" height=400px frameborder="0"></iframe>'+
                            '</div>'+
                            '<div id="tabs-2">'+
                                '<iframe src="'+baseUrl+'index.php/Tabs/customfields/'+sku+'" width="100%" height=400px frameborder="0"></iframe>'+
                            '</div>'+
                            '<div id="tabs-3">'+
                                '<iframe src ="'+baseUrl+'index.php/Tabs/compat?q='+sku+'" width="100%" height=400px frameborder="0"></iframe>'+
                            '</div>'+
                            '<div id="tabs-4">'+
                                '<iframe src ="http://photos.discount-merchant.com/photos/sku/'+sku+'/" width="100%" height=400px frameborder="0"></iframe>'+
                            '</div>'+
                            '<div id="tabs-5">'+
                                '<iframe src ="http://photos.discount-merchant.com/photos/sku/'+sku+'/Attachments/" width="100%" height=400px frameborder="0"></iframe>'+
                            '</div>'+
                            '<div id="tabs-6">'+
                                '<iframe src ="'+baseUrl+'index.php/Tabs/history/'+sku+'" style="width:100%;height:400px; border-width:0px;"></iframe>'+
                            '</div>'+
                            '<div id="tabs-7">'+
                                ' <iframe src ="'+baseUrl+'index.php/Tabs/invdetailsnew/'+sku+'" style="width:100%;height:400px; border-width:0px;"></iframe>'+
                            '</div>'+
                            '<div id="tabs-8">'+
                                '<iframe src ="'+baseUrl+'index.php/Tabs/assemblyreq?q='+sku+'" style="width:100%;height:400px; border-width:0px;"></iframe>'+
                            '</div>'+
                            '<div id="tabs-9">'+
                                '<iframe src ="'+baseUrl+'index.php/Tabs/vqohdetails/'+sku+'" style="width:100%;height:400px; border-width:0px;"></iframe>'+
                            '</div>'+
                            '<div id="tabs-10">'+
                                '<iframe src ="'+baseUrl+'index.php/Tabs/costinfo/'+sku+'" style="width:100%;height:400px; border-width:0px;"></iframe>'+
                            '</div>'+
                            '<div id="tabs-11">'+
                                '<iframe src ="'+baseUrl+'index.php/Tabs/pohistory/'+sku+'" style="width:100%;height:400px; border-width:0px;"></iframe>'+
                            '</div>'+
                            '<div id="tabs-12">'+
                                '<iframe src ="'+baseUrl+'index.php/Tabs/advanced/'+sku+'" style="width:100%;height:400px; border-width:0px;"></iframe>'+
                            '</div>'+
                        '</div>'); 

           $(this).append(prueba);
           $( "#tabs" ).tabs();
        }
    }); 
}

