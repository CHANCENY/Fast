<?php

namespace UI;

use Modules\CountriesModular;

class AddressField extends CountriesModular
{
    public static function addressFieldSet($defaultCountry){
        $countries = self::getAllCountries();
       $options = "";
        foreach ($countries as $countrydata){
            extract($countrydata);

            if($code === $defaultCountry){
                $options .= "<option value ='$code' selected>$country</option>";
            }else {
                $options .= "<option value ='$code'>$country</option>";
            }
        }

        $fields = "<div class='dragArea row'>";

         $country = "<div class='col-md col-sm-12 form-group mb-3'><select class='form-control' name='country' id='country-edit'>
                  $options
                  </select></div>";

        $state = "<div class='col-md col-sm-12 form-group mb-3'><select class='form-control' name='states' id='state-edit' disabled>
                   <option value=''>--State--</option>
                   </select></div>";

        $city = "<div class='col-md col-sm-12 form-group mb-3'><select class='form-control' name='cities' id='city-edit' disabled>
                 <option value=''>--City--</option>
                 </select></div>";
        $address1 = "<div class='col-12 form-group mb-3'><input class='form-control' name='address1' id='address1-edit' placeholder='Address' type='text'></div>";

        $fields .= $country.$state.$city.$address1."</div>";

        $fields .= self::javaScriptAddress();



        return $fields;

    }

    public static function javaScriptAddress(){
        return '<div>
    <script type="application/javascript">
    function removeOptions(selectElement) {
        var i, L = selectElement.options.length - 1;
        for(i = L; i >= 0; i--) {
            selectElement.remove(i);
        }
    }
        const countryEdits = document.getElementById("country-edit");
        const statesEdits = document.getElementById("state-edit");
        if(countryEdits !== ""){
            countryEdits.addEventListener("change", (e)=>{
                e.preventDefault();
                const code = e.target.value;
                const url = window.location.protocol+"//"+window.location.hostname+"/addressing-handler?action=states&code="+code;
                const xhrobj = new XMLHttpRequest();
                xhrobj.open("GET", url, true);
                xhrobj.setRequestHeader("Content-Type", "application/json");
                xhrobj.onload = function(){
                    if(this.status === 200){
                        const data = JSON.parse(this.responseText);
                        const stateEdit = document.getElementById("state-edit");
                        const cityEdits = document.getElementById("city-edit");
                       removeOptions(stateEdit);
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
                const url = window.location.protocol+"//"+window.location.hostname+"/addressing-handler?action=cities&code="+code;
                const xhr = new XMLHttpRequest();
                xhr.open("GET", url, true);
                xhr.setRequestHeader("Content-Type", "application/json");
                xhr.onload = function (){
                    if(this.status === 200){
                        const data = JSON.parse(this.responseText);
                        const cityEdit = document.getElementById("city-edit");
                        removeOptions(cityEdit);
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
    </script>
</div>';
    }
}
?>

