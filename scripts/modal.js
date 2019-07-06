// Get the modal
var modal = document.getElementById('myModal');
var modalheader = document.getElementById('modal-header');

// Get the <span> element that closes the modal
var span = document.getElementsByClassName("close")[0];

function openmodal(status)
{
  modal.style.display = "block";

  if(status==1)
    modalheader.style.backgroundColor = 'green';
  else if(status == 2)
    modalheader.style.backgroundColor = 'red';
  else
    modalheader.style.backgroundColor = 'gray';
}

// When the user clicks on <span> (x), close the modal
span.onclick = function() {
  modal.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
  if (event.target == modal) {
    modal.style.display = "none";
  }
}