
const callingStates = (e) => {
   //e.preventDefault();
   var base = window.location.hostname;
   var protocal = window.location.protocol;
   var url = protocal+'//'+base+'/'+'addressing-handler?action=states&code='+e.target.value;
   console.log(url);
   const xhr = new XMLHttpRequest();
   xhr.open('GET', url, true);
   xhr.setRequestHeader('Content-Type', 'application/json');
   xhr.onload = function(){
    if(this.status === 200){
        console.log(this.responseText);
    }
   }

   xhr.onerror = function(){
    console.log(this.responseText);
   }
   xhr.send();
    
}

const countrySelect = document.getElementById('country');

if(countrySelect !== ""){
    countrySelect.addEventListener('change', (e)=>callingStates(e));
}