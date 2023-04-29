function removeOptions(selectElement) {
    if(selectElement === null){
        return;
    }
    var i, L = selectElement.options.length - 1;
    for(i = L; i >= 0; i--) {
        selectElement.remove(i);
    }
}
const countryEdits = document.getElementById("country-edit");
const statesEdits = document.getElementById("state-edit");

const host = document.getElementById('host-address');
if(host  !== null){
    if(countryEdits !== ""){
        countryEdits.addEventListener("change", (e)=>{
            e.preventDefault();
            const code = e.target.value;
            const url = host+"/addressing-handler?action=states&code="+code;
            const xhrobj = new XMLHttpRequest();
            xhrobj.open("GET", url, true);
            xhrobj.setRequestHeader("Content-Type", "application/json");
            xhrobj.onload = function(){
                if(this.status === 200){
                    const data = JSON.parse(this.responseText);
                    let stateEdit = document.getElementById("state-edit");
                    let cityEdits = document.getElementById("city-edit");
                    const stateDiv = document.getElementById("state-div");
                    removeOptions(stateEdit);
                    if(stateEdit === null){
                        stateEdit = document.createElement('select');
                        stateEdit.id = "state-edit";
                        stateEdit.name = 'states';
                        stateEdit.className ="form-control";
                        stateDiv.appendChild(stateEdit);
                    }
                    stateEdit = document.getElementById("state-edit");
                    stateEdit.disabled = false;

                    if(data.length > 0){
                        data.forEach((state)=>{
                            const opt = document.createElement("option");
                            opt.value = state.rowid;
                            opt.textContent = state.state;
                            stateEdit.appendChild(opt);
                        })
                    }else{
                        cityEdits.remove();
                        stateEdit.remove();
                    }
                }
            }
            xhrobj.send();
        })
    }

    if(statesEdits !== ""){
        statesEdits.addEventListener("change", (e)=>{
            e.preventDefault();
            const code = e.target.value;
            const url = host+"/addressing-handler?action=cities&code="+code;
            const xhr = new XMLHttpRequest();
            xhr.open("GET", url, true);
            xhr.setRequestHeader("Content-Type", "application/json");
            xhr.onload = function (){
                if(this.status === 200){
                   const data = JSON.parse(this.responseText);
                    let cityEdit = document.getElementById("city-edit");
                    const cityDiv = document.getElementById("city-div");
                    removeOptions(cityEdit);
                    if(cityEdit === null){
                        cityEdit = document.createElement('select');
                        cityEdit.id = "city-edit";
                        cityEdit.name = 'cities';
                        cityEdit.className = "form-control";
                        cityDiv.appendChild(cityEdit);
                    }
                    cityEdit = document.getElementById("city-edit");
                    cityEdit.disabled = false;
                    if(data.length > 0){
                        data.forEach((city)=>{
                            const opt = document.createElement("option");
                            opt.value = city.rowid;
                            opt.textContent = city.city;
                            cityEdit.appendChild(opt);
                        })
                    }else{
                        cityEdit.remove();
                    }
                }
            }
            xhr.send();
        })
    }
}