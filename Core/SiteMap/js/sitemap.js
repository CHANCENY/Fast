
const querySelector = document.querySelectorAll('a');

document.addEventListener('DOMContentLoaded', (e)=>{
    if(querySelector !== null){

        let links = [];
        for (let i = 0; i < querySelector.length; i++){
           const hrefAttribute = querySelector[i].href;
           links.push(hrefAttribute);
        }

        const data = {links};
        const xhr = new XMLHttpRequest();
        xhr.open('POST','developement/site-map',true);
        xhr.setRequestHeader('Content-Type','application/json');
        xhr.onload = function (){
            if(this.status === 200){
                console.log(this.responseText);
                const datas = JSON.parse(this.responseText);
            }
        }
        xhr.send(JSON.stringify(data));
    }
})