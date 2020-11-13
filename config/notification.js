// Timed notification block

// create new block at top of parent
// wait a few seconds, delete block
function createNotification(className,parent=document.querySelector("body"),msg,time){
  this.box = document.createElement('div');
  this.box.className = `notification ${className}`;
  this.box.appendChild(document.createTextNode(msg));
  parent.insertBefore(this.box,document.querySelector(".container"));

  // Timeout
  setTimeout(function(){
    document.querySelector(".notification").remove()
  },time*1000);
}