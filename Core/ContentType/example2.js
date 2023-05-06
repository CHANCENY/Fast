const callRelated = () =>{
    const baseUrl = document.getElementById('layout-content-form').getAttribute('data-call');
    const xhr = new XMLHttpRequest();
    xhr.open('GET', '/content-type-callbacks?call=@contentType', true);
    xhr.setRequestHeader('Content-Type','application/json');
    xhr.onload =function (){
        if(this.status === 200){
            console.log(this.responseText);
            const data = JSON.parse(this.responseText);
            if(data !== null){
                const selectTag  =document.getElementById('@id');
                data.forEach((item) =>{
                    let option = document.createElement('option');
                    option.value = item.value;
                    option.textContent = item.text;
                    selectTag.appendChild(option);
                });
            }
        }
    }
    xhr.send();
}

document.getElementById('@id').addEventListener('click', (e)=>{
    callRelated();
})