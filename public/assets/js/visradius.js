function setWindowSize() {
  var myWidth = 0;
  if( typeof( window.innerWidth ) == 'number' ) {
    //Non-IE
    myWidth = window.innerWidth;
   } else if( document.documentElement && document.documentElement.clientWidth ) {
    //IE 6+ in 'standards compliant mode'
    myWidth = document.documentElement.clientWidth;

  } else if( document.body && document.body.clientWidth  ) {
    //IE 4 compatible
    myWidth = document.body.clientWidth;

  }

  document.getElementById("width").value=myWidth-25;

}