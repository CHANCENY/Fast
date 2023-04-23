<?php

use GlobalsFunctions\Globals;

@session_start();

$countries = \Modules\CountriesModular::getAllCountries();


$options = "";
foreach ($countries as $country){
    $option = "<option value='{$country['code']}'>{$country['country']}</option>";
    $options .=$option;
}

if($_SERVER['REQUEST_METHOD'] === "POST"){

    if(isset($_POST['registration'])){
       if(empty($_POST['firstname']) || empty($_POST['lastname']) || empty($_POST['email']) || empty($_POST['password'])){
           \Alerts\Alerts::alert('danger', "Firstname , Lastname, Email, Password they are mandatory");
           goto out;
       }
       $country = \Modules\CountriesModular::getCountryName(htmlspecialchars(strip_tags($_POST['country'])));
       $address = $country.', '.htmlspecialchars(strip_tags($_POST['state'])).', '.
           htmlspecialchars(strip_tags($_POST['city'])).', '.htmlspecialchars(strip_tags($_POST['zip']));
       $site = \Sessions\SessionManager::getSession('site');
        $defaultImage[]  = Globals::protocal().'://'.Globals::serverHost().
            Globals::home().'/Files/profile_default2.jpg';
        $defaultImage[]  = Globals::protocal().'://'.Globals::serverHost().
            Globals::home().'/Files/profile_default.avif';
        $data = [
                "firstname"=>htmlspecialchars(strip_tags($_POST['firstname'])),
                "lastname"=>htmlspecialchars(strip_tags($_POST['lastname'])),
               "mail"=> htmlspecialchars(strip_tags($_POST['email'])),
               "password"=>htmlspecialchars($_POST['password']),
               "phone"=>htmlspecialchars(strip_tags($_POST['phone'])),
               "address"=>$address,
               "role"=> $site === false ? "Admin" : "user",
               "image" => $defaultImage[random_int(0,1)]
        ];

        $check = \Datainterface\Selection::selectById('users', ['mail'=>htmlspecialchars(strip_tags($_POST['email']))]);

        if(empty($check)){
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
            $user = \Datainterface\Insertion::insertRow('users', $data);
            if(!empty($user)){
                echo \Alerts\Alerts::alert('info', "User created {$_POST['firstname']}");
                \Sessions\SessionManager::setSession('site', true);
                \GlobalsFunctions\Globals::redirect('/');
            }else{
                echo \Alerts\Alerts::alert('danger', "Failed to create user");
            }
        }else{
            echo \Alerts\Alerts::alert('danger', "Email already exist in system");
        }


        out:
    }
}
?>
<form class="w-full max-w-lg" method="POST" action="<?php echo $_SESSION['public_data']['view']['view_url']; ?>">
    <input type="hidden" name="formid" value="registration-form-id01">
    <div class="flex flex-wrap -mx-3 mb-6">
        <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="grid-first-name">
                First Name
            </label>
            <input name="firstname" class="appearance-none block w-full bg-gray-200 text-gray-700 border border-red-500 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white" id="grid-first-name" type="text" placeholder="Jane">
            <p class="text-red-500 text-xs italic">Please fill out this field.</p>
        </div>
        <div class="w-full md:w-1/2 px-3">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="grid-last-name">
                Last Name
            </label>
            <input name="lastname" class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" id="grid-last-name" type="text" placeholder="Doe">
        </div>
        <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="grid-first-name">
               Email
            </label>
            <input name="email" class="appearance-none block w-full bg-gray-200 text-gray-700 border border-red-500 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white" id="grid-first-name" type="text" placeholder="janedoe@gmail.com">
            <p class="text-red-500 text-xs italic">Please fill out this field.</p>
        </div>
        <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="grid-first-name">
                Phone
            </label>
            <input name="phone" class="appearance-none block w-full bg-gray-200 text-gray-700 border border-red-500 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white" id="grid-first-name" type="text" placeholder="+913567304">
            <p class="text-red-500 text-xs italic">Please fill out this field.</p>
        </div>
    </div>
    <div class="flex flex-wrap -mx-3 mb-6">
        <div class="w-full px-3">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="grid-password">
                Password
            </label>
            <input name="password" class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" id="grid-password" type="password" placeholder="******************">
            <p class="text-gray-600 text-xs italic">Make it as long and as crazy as you'd like</p>
        </div>
    </div>
    <div class="flex flex-wrap -mx-3 mb-2">
        <div class="w-full md:w-1/3 px-3 mb-6 md:mb-0">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="grid-state">
                COUNTRY
            </label>
            <div class="relative">
                <select name="country" class="block appearance-none w-full bg-gray-200 border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500" id="grid-state">
                    <?php echo $options; ?>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                </div>
            </div>
        </div>
        <div class="w-full md:w-1/3 px-3 mb-6 md:mb-0">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="grid-city">
                CITY
            </label>
            <input name="city" class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" id="grid-city" type="text" placeholder="Albuquerque">
        </div>
        <div class="w-full md:w-1/3 px-3 mb-6 md:mb-0">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="grid-city">
                STATE
            </label>
            <input name="state" class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" id="grid-city" type="text" placeholder="New mexico">
        </div>
        <div class="w-full md:w-1/3 px-3 mb-6 md:mb-0">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="grid-zip">
                Zip
            </label>
            <input name="zip" class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" id="grid-zip" type="text" placeholder="90210">
        </div>
    </div>
    <div class="flex space-x-2 justify-center">
        <button type="submit" name="registration" value="Submit now" class="inline-block px-6 py-2.5 bg-blue-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-blue-700 hover:shadow-lg focus:bg-blue-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-blue-800 active:shadow-lg transition duration-150 ease-in-out">Submit now</button>
    </div>
</form>

