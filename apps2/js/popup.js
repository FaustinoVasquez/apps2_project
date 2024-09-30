
function popupform(myform, windowname)
{
if (! window.focus)return true;
window.open('', windowname, 'height=380,width=560,scrollbars=yes');
myform.target=windowname;
if (top.location != self.location) {
top.location = self.location
}
return true;
}



