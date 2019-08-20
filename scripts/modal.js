// Get the modal
var modal = document.getElementById('myModal');
var modalheader = document.getElementById('myModalHeader');
var modalfooter = document.getElementById('myModalFooter');

// Get the <span> element that closes the modal
var span = document.getElementsByClassName("close")[0];

function openmodal(status)
{
  switch(status)
  {
    case 1:
        modalfooter.innerHTML = "<h3>Rozwiązanie poprawne</h3>";
        modalheader.style.backgroundColor = 'green';
        modalfooter.style.backgroundColor = 'green';
        modalheader.style.color = 'white';
        modalfooter.style.color = 'white';
        break;    
    case 2:
        modalfooter.innerHTML = "<h3>Rozwiązanie niepoprawne</h3>";
        modalheader.style.backgroundColor = 'red';
        modalfooter.style.backgroundColor = 'red';
        modalheader.style.color = 'white';
        modalfooter.style.color = 'white';
        break;
    case 3:
        modalfooter.innerHTML = "<h3>Rozwiązanie niepoprawne</h3>";
        modalheader.style.backgroundColor = 'brown';
        modalfooter.style.backgroundColor = 'brown';
        modalheader.style.color = 'white';
        modalfooter.style.color = 'white';
        break;
    case 4:
        modalfooter.innerHTML = "<h3>Rozwiązanie niepoprawne</h3>";
        modalheader.style.backgroundColor = 'yellow';
        modalfooter.style.backgroundColor = 'yellow';
        modalheader.style.color = 'black';
        modalfooter.style.color = 'black';
        break;
    case 5:
        modalfooter.innerHTML = "<h3>Rozwiązanie niepoprawne</h3>";
        modalheader.style.backgroundColor = 'blue';
        modalfooter.style.backgroundColor = 'blue';
        modalheader.style.color = 'white';
        modalfooter.style.color = 'white';
    default:
        break;
  }

  modal.style.display = "block";
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