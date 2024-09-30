
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
  <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
  <style>
  .carousel-inner > .item > img,
  .carousel-inner > .item > a > img {
      width: 70%;
      margin: auto;
  }
  </style>

<center>
  <div id="div-content">
    <div class="col-3" style="text-align:left;">
      <button class="btn-gray" onClick="location.href='<?php echo base_url() ?>/index.php/Catalog/prodcat'"> <span class="btn-title"> MIT </span></button>
    </div>
    <div class="col-4">
      <form action="demo_form.asp" id ='myForm'>
      <center>
        <div>
          <label class="lb-1">Search: </label><span id='rBrandSelect'>
          <select name ="myBrands" id ="myBrands" class="sel-controler1"></select></span>
          <label class="lb-1" >Country: </label><span id='rCountryCodeSelect'>
          <select name ="myCountryCode" id ="myCountryCode" class="sel-controler1"></select></span>
          <label class="lb-1" >Status: </label><span id='rStatusAsinSelect'>
          <select name ="myStatusAsin" id ="myStatusAsin" class="sel-controler1"></select></span>
          <!--  ---------------- I D -------------------  -->
          <input type="hidden" name="myPotSku" id="myPotSku" value="">
          <input type="hidden" name="myPotAsin" id="myPotAsin" value="">
          <input type="hidden" name="myID" id="myID" value="">
        </div>
      </form> 
      </center>
    </div>
    <!--  -----------------------------------  -->
    <center>
      <div class="col-1">
        <!-- <h3>AMAZON</h3> -->
          <img src="https://www.sellerexpress.com/wp-content/uploads/2013/02/amazon_logo_large.png" alt="" height="30px">
          <br><br>
          <span id='rImage1URL' class="img"></span>
      </div>
    </center>
    <div class="col-2">
      <div><a onClick="lock();"><img src="http://koehr.in/img/lock-icon.png" alt="" style="height:15px; cursor:pointer;" id="iconLock"></a></div>
      <div id="ST" style="display:none;"><label style="color:#778ca0;">Search Term: </label><span id='rSearchTerm' style="color:#778ca0;"></span>
      <br><hr>
      </div>
      <div id="lb-status"><label style="color:#fff">STATUS: </label><span id='rStatusAsin' style="color:#fff"></span></div>

      <div><label>Country Code: </label><span id='rCountryCode'></span></div>
      <div><label>ASIN: </label><span id='rASIN'></span></div>
      <div><label>Title: </label><span id='rTitle'></span></div>
      <div><label>Manufacturer: </label><span id='rManufacturer'></span></div>
      <div><label>Brand: </label><span id='rBrand'></span></div>
      <div><label>Potential SKU: </label><span id='rPotentialSKU'></span></div>
      <div><label>Auto Analytics: </label><span id='rAutoAnalytics'></span></div>
      <hr>
      <div><label>Bullet1: </label><span id='rBullet1'></span></div>
      <div><label>Bullet2: </label><span id='rBullet2'></span></div>
      <div><label>Bullet3: </label><span id='rBullet3'></span></div>
      <div><label>Bullet4: </label><span id='rBullet4'></span></div>
      <div><label>Bullet5: </label><span id='rBullet5'></span></div>
      <hr>
      <div id="descripction"><label>Description: </label><span id='rDescription'></span></div>
    </div>
    <!--  -----------------------------------  -->
    <div class="clear"></div>
    <div class="line"></div>
    <div class="clear"></div>
    <!--  -----------------------------------  -->
    <div class="col-1">
      <!-- <h3>MI TECHNOLOGIES</h3> -->
      <img src="http://static.wixstatic.com/media/cfe283_7204f203aff143de9f2bb26b940fcc57.png_srz_p_261_83_75_22_0.50_1.20_0.00_png_srz" alt="" height="40px">
      <br><br>
      <div id="myCarousel" class="carousel slide img" data-ride="carousel">
        <ol class="carousel-indicators">
          <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
          <li data-target="#myCarousel" data-slide-to="1"></li>
          <li data-target="#myCarousel" data-slide-to="2"></li>
          <li data-target="#myCarousel" data-slide-to="3"></li>
          <li data-target="#myCarousel" data-slide-to="4"></li>
        </ol>
        <!-- Wrapper for slides -->
        <div class="carousel-inner" role="listbox">
          <div class="item active">
            <div id="lImage1URL"></div>
          </div>
          <div class="item">
            <div id="lImage2URL"></div>
          </div>
          <div class="item">
            <div id="lImage3URL"></div>
          </div>
          <div class="item">
            <div id="lImage4URL"></div>
          </div>
          <div class="item">
            <div id="lImage5URL"></div>
          </div>
        </div>

      <!-- Left and right controls -->
        <a class="left carousel-control" href="#myCarousel" role="button" data-slide="prev">
          <span class="glyphicon" aria-hidden="true" style="font-size:20px; font-weight:bold;"> < </span>
          <span class="sr-only">Previous</span>
        </a>
        <a class="right carousel-control" href="#myCarousel" role="button" data-slide="next">
          <span class="glyphicon" aria-hidden="true" style="font-size:20px; font-weight:bold;"> > </span>
          <span class="sr-only">Next</span>
        </a>
      </div>
    </div>
    <!--  -----------------------------------  -->
    <div class="col-2">
      <div>
        <label>Wrong Data: </label>
        <input type="text" id="sText" class="inp-controler" placeholder="Search ..">
        <label><input type="button" id='sButton' value="Submit" class="btn-blue"></label>
        <select name ="altSkus" id ="altSkus" class="sel-controler2"></select>
      </div>
      <div><label>Has Housing: </label><input type="checkbox" name="hasHousing" id="hasHousing"></div>
      <div><label>Map To SKU: </label><span id='lPotentialSku'></span><select name ="SkuWH" id ="SkuWH" class="sel-controler" ></select><select name ="SkuBL" id ="SkuBL" class="sel-controler" hidden></select></div>
      <div><label>Manufacturer: </label><span id='lManufacturer' ></span></div>
      <div><label>Description: </label><span id='lDescription' ></span></div>
      <br>
       <div style="text-align:center;">
        <button id="Before" class="btn-gray"> <span class="btn-title"> < </span></button>
          <button type="button" id="Approved"  class="btn-green"><span class="btn-title">Approve</span></button>
          <button type="button" id="Skip"  class="btn-orange"><span class="btn-title">Skip</span></button>
          <button type="button" id="Decline"     class="btn-red"><span class="btn-title">Decline</span></button>
        <button id="After" class="btn-gray"> <span class="btn-title"> > </span></button>
      </div>
      <br>
      <hr>
      <label>More Information</label>
      <div id="extraInfo" class="div-info"></div>
    </div>
  </div>
</center>

<script>

function lock(){
  var displayV = document.getElementById('ST').style.display;
  if(displayV == "")
  {
    document.getElementById('ST').style.display="none";
    document.getElementById("iconLock").src="http://koehr.in/img/lock-icon.png";
  }
  else
  {
    document.getElementById('ST').style.display="";
    document.getElementById("iconLock").src="https://cdn2.iconfinder.com/data/icons/windows-8-metro-style/512/unlock.png";
  }
}


$(function(){

//CLick Principal Buttons

  $('#Before').click(function(){
    clearWrongForm();
    initializeData1('B');
  });

  $('#After').click(function(){
    clearWrongForm();
    initializeData1('A');
  });

  //CLick Principal Buttons
  
  $('#Approved').click(function(){

     var sku = visibleElement();
     var altSku = jQuery("#altSkus option:selected").val();
     
     if (altSku){
     var asin = $("#myPotAsin").val();
     var countrycode = jQuery("#myCountryCode option:selected").val();
     var approved = $.xResponse('saveApproved','POST','json',{altSku: altSku, asin:asin, cc:countrycode});
     }else{
      var asin = $("#myPotAsin").val();
      var countrycode = jQuery("#myCountryCode option:selected").val();
      var approved = $.xResponse('saveApproved','POST','json',{sku: sku, asin:asin, cc:countrycode});
     }


     if (approved){
      alert('Asin Approved');
      clearWrongForm();
      initializeData1('N');

     }else{
      alert('Something is wrong please check')
     }

  });


$('#Decline').click(function(){

     var asin = $("#myPotAsin").val();
     var countrycode = jQuery("#myCountryCode option:selected").val();;
     var Decline = $.xResponse('declineSkip','POST','json',{ asin:asin, cc:countrycode, message:'Declined'});

     if (Decline){
      alert('Asin Declined');
      initializeData1('N'); 

     }else{
      alert('Something is wrong please check')
     }

  });

$('#Skip').click(function(){
     var asin = $("#myPotAsin").val();
     var countrycode = jQuery("#myCountryCode option:selected").val();;
     var skip = $.xResponse('declineSkip','POST','json',{asin:asin, cc:countrycode,message:'Skipped'});

     if (skip){
      alert('Asin Skipped');
      // clearWrongForm();
     }else{
      alert('Something is wrong please check')
     }

     initializeData1('N');
  });

  $('#sButton').click(function(){

    var search = $('#sText').val();

    if (search){
      var altSkus = $.xResponse('searchArternativeSkus','get','json',{ sd:search });
      addElementsToAltSkusSelect(altSkus);
      var selected = $('#altSkus').val();
      selected  = selected.split(";");
      getBareOrHousing(selected);
    }
   
   return false;

  });

  $('#altSkus').on('change',function(){
    var selected = $('#altSkus').val();
    selected  = selected.split(";");
    getBareOrHousing(selected);
    return false;
  });
});

// $(function() {
//   $("input[type=submit],button" )
//     .button()
//     .click(function( event ) {
//       event.preventDefault();
//     });

// });

$(function(){

  $("#hasHousing").click(function(){
    if ($(this).is(':checked')) {
      $('#SkuWH').show();
      initializeForm2WH();
    } 
    else {
      $('#SkuBL').show();
      $('#SkuWH').hide();
      initializeForm2BL();
    }
  });
});

$(function(){

  initializeForm1();

  $( "#myBrands" ).on('change',function(){
     initializeCountryCodes();
     initializeStatusAsin();
   $( "#myForm" ).submit();
  });

  $( "#myCountryCode" ).on('change',function(){
    initializeStatusAsin();
   $( "#myForm" ).submit();
  });

  $( "#myStatusAsin" ).on('change',function(){
   $( "#myForm" ).submit();
  });

  $( "#myForm" ).on('submit',function(e){
    e.preventDefault();
    initializeData1('N');
  });

  $( "#SkuWH" ).on('change',function(){

    var newSku = jQuery("#SkuWH option:selected").val();
    if (newSku !='------------'){
      initializeDataWH();
    }
  });

  $( "#SkuBL" ).on('change',function(){
    var newSku = jQuery("#SkuBL option:selected").val();
    if (newSku !='------------'){
      initializeDataBL();
    }
  });
})
$.extend({
    xResponse: function(url,type,dataType,data) {
        // local var
        var theResponse = null;
        // jQuery ajax
        $.ajax({
            url: url,
            type: type,
            data: data,
            dataType: dataType,
            async: false,
            success: function(respText) {
                theResponse = respText;
            }
        });
        // Return the response text
        return theResponse;
    }
});

function getBareOrHousing(lamp){

   var typeLamp = lamp.pop();
   typeLamp = typeLamp.split(" ");
   typeLamp = typeLamp.pop();
   if (typeLamp == 'Bare'){
    $("#hasHousing").attr('checked',false);
    $('#SkuBL').show();
    $('#SkuWH').hide();
    initializeForm2BL();
   }

   if (typeLamp == 'Housing'){
    $("#hasHousing").attr('checked',true);
    $('#SkuBL').hide();
    $('#SkuWH').show();
    initializeForm2WH();
   }
}

function clearWrongForm(){
  $('#altSkus').html('');
  $('#sText').val('');
  return false;
}

function initializeForm1(){

  clearWrongForm();
  var brands = $.xResponse('getBrands','GET','json');
  addElementsBrandsForm(brands);
  
  var brand = jQuery("#myBrands option:selected").val();
  var countrycodes = $.xResponse('getCountryCodes','GET','json',{br: brand});
  addElementsToCcForm(countrycodes);

  var countrycode = jQuery("#myCountryCode option:selected").val();
  var statusasin = $.xResponse('getStatusAsin','GET','json',{br: brand, cc: countrycode});
  addElementsToStForm(statusasin);

  initializeData1('N');
}

function initializeCountryCodes(){
  var brand = jQuery("#myBrands option:selected").val();
  var countrycodes = $.xResponse('getCountryCodes','GET','json',{br: brand});
  addElementsToCcForm(countrycodes);
}

function initializeStatusAsin(){
  var brand = jQuery("#myBrands option:selected").val();
  var countrycode = jQuery("#myCountryCode option:selected").val();
  var statusasin = $.xResponse('getStatusAsin','GET','json',{br: brand, cc: countrycode});
  addElementsToStForm(statusasin);
}

function initializeData1(flag){
  var brand = jQuery("#myBrands option:selected").val();
  var countrycode = jQuery("#myCountryCode option:selected").val();
  var statusasin = jQuery("#myStatusAsin option:selected").val();

  if(flag == 'A' || flag == 'B') {var id = jQuery("#myID").val();}
  else {
    if(statusasin != 0){
      flag = 'S';
    }
    var id = 0;
  }
  var rawAsin = $.xResponse('getRawAsin','GET','json',{br: brand, cc: countrycode, sa: statusasin, id: id, fg: flag});
  addElementsToMainData(rawAsin);
  initializeForm2WH();
}
 

function initializeForm2WH(){

    document.getElementById("hasHousing").checked = true;
    $('#SkuBL').hide();
    $('#SkuWH').show();
    var potsku = $('#myPotSku').val();
    var potElements = $.xResponse('getPotencialSkuSelectWH','GET','json',{sku: potsku});
    addElementsToSecondFormFirstSelect(potElements);
    initializeDataWH();
}

function initializeDataWH(){

    var potsku = $('#SkuWH').val();
    var potElements = $.xResponse('getPotencialSkuElements','GET','json',{sku: potsku});
    addElementsSecondData(potElements);
}

function initializeForm2BL(){
 $('#SkuWH').hide();
    var potsku = $('#myPotSku').val();
    var potElements = $.xResponse('getPotencialSkuSelectBL','GET','json',{sku: potsku});
    addElementsToSecondFormSecondSelect(potElements);
    initializeDataBL();
}


function initializeDataBL(){
    var potsku = $('#SkuBL').val();
    var potElements = $.xResponse('getPotencialSkuElements','GET','json',{sku: potsku});
    addElementsSecondData(potElements);
}



function addElementsBrandsForm(brands){

  $('#myBrands').html(brands.rawBrand);
  return false;
}

function addElementsToCcForm(countrycodes){

  $('#myCountryCode').html(countrycodes.rawCountry);
  return false;
}

function addElementsToStForm(statusasin){

  $('#myStatusAsin').html(statusasin.rawStatusAsin);
  return false;
}

function addElementsToMainData(rawAsin){

  $('#rSearchTerm').html(rawAsin.rSearchTerm);
  // $('#rIsApproved').html(rawAsin.rIsApproved);
  $('#rASIN').html(rawAsin.rASIN);
  $('#rMITSKU').html(rawAsin.rMITSKU);
  $('#rPotentialSKU').html(rawAsin.rPotentialSKU);
  $('#rManufacturer').html(rawAsin.rManufacturer);
  $('#rBrand').html(rawAsin.rBrand);
  $('#rTitle').html(rawAsin.rTitle);
  $('#rStatusAsin').html(rawAsin.rStatusAsin);
  $('#rImage1URL').html('<a href="'+rawAsin.rImage1URL+'" target="_blank"><img src="'+rawAsin.rImage1URL+'" class="img"/></a>');
  $('#rCountryCode').html(rawAsin.rCountryCode);
  $('#rBullet1').html(rawAsin.rBullet1);
  $('#rBullet2').html(rawAsin.rBullet2);
  $('#rBullet3').html(rawAsin.rBullet3);
  $('#rBullet4').html(rawAsin.rBullet4);
  $('#rBullet5').html(rawAsin.rBullet5);
  $('#rDescription').html(rawAsin.rDescription);
  $('#rAutoAnalytics').html(rawAsin.rAutoAnalytics);
  $('#rId').html(rawAsin.rId);
  $('#myPotSku').val(rawAsin.rPotentialSKU);
  $('#myPotAsin').val(rawAsin.rASIN);
  $('#myID').val(rawAsin.rId);

  document.getElementById('lb-status').style.backgroundColor = '#fff';
  if(rawAsin.rStatusAsin == 'Approved'){
    document.getElementById('lb-status').style.backgroundColor = '#449d44';
  }
  if(rawAsin.rStatusAsin == 'Skipped'){
    document.getElementById('lb-status').style.backgroundColor = '#ec971f';
  }
  if(rawAsin.rStatusAsin == 'Declined'){
    document.getElementById('lb-status').style.backgroundColor = '#c9302c';
  }
  return false;
}

function addElementsToSecondFormFirstSelect(potSku) {

  $('#SkuWH').html(potSku.psSelect);

  return false;
}

function addElementsToSecondFormSecondSelect(potSku) {

  $('#SkuBL').html(potSku.psSelect);

  return false;
}

function addElementsSecondData(potSku) {

  $('#lManufacturer').html(potSku.lmanufacturer);
  $('#lDescription').html(potSku.lName);
  $('#lImage1URL').html('<a href="'+potSku.lImage1URL+'" target="_blank"><img src="'+potSku.lImage1URL+'" class="img" /></a>');
  $('#lImage2URL').html('<a href="'+potSku.lImage2URL+'" target="_blank"><img src="'+potSku.lImage2URL+'" class="img" /></a>');
  $('#lImage3URL').html('<a href="'+potSku.lImage3URL+'" target="_blank"><img src="'+potSku.lImage3URL+'" class="img" /></a>');
  $('#lImage4URL').html('<a href="'+potSku.lImage4URL+'" target="_blank"><img src="'+potSku.lImage4URL+'" class="img" /></a>');
  $('#lImage5URL').html('<a href="'+potSku.lImage5URL+'" target="_blank"><img src="'+potSku.lImage5URL+'" class="img" /></a>');
  $('#extraInfo').html(potSku.extraInfo);
 

  return false;
}


function addElementsToAltSkusSelect(potSku) {

  $('#altSkus').html(potSku);

  return false;
}


function visibleElement(){
  if ($('#SkuWH').is(":visible")){
     return sku = $('#SkuWH').val();   
  }

  if ($('#SkuBL').is(":visible")){
     return sku = $('#SkuBL').val(); 
  }
}
</script>