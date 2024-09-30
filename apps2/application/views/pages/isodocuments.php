<div class="clear"></div>
<div id="tree">
    <ul style="display:none">
      <li id="http://localhost/isoDoc/cronograma.pdf" title="Look, a tool tip!">cuatro.txt
      <li id="key2" class="selected">item2: selected on init
      <li id="key3" class="folder">Folder with some children
        <ul>
          <li id="key3.1">Sub-item 3.1
          <li id="key3.2">Sub-item 3.2
        </ul>

      <li id="key4" class="expanded">Document with some children (expanded on init)
        <ul>
          <li id="key4.1">Sub-item 4.1
          <li id="key4.2">Sub-item 4.2
        </ul>
    </ul>
  </div>

<?=$baseurl?>
<script type="text/javascript">
    $(function(){
      $("#tree").dynatree({
         onActivate: function(node) {
          docLocation = node.data.key;

          window.open(docLocation, '_blank', 'fullscreen=yes');

        //  window.open(docLocation,"resizeable,scrollbar"); 
       //alert("You activated " + node.data.key);
        }
      });
    });
  </script>


