<?php
@session_start();

global $sections;
$sections = false;
if($_SERVER['REQUEST_METHOD'] === 'POST'){
  if(isset($_POST['forgot-default-button'])){
      if(empty($_POST['email']) || empty($_POST['password']) || empty($_POST['confirm-password'])){
          echo \Alerts\Alerts::alert('warning', 'Fields are empty fill all the fields');
      }else{
          $code = strval(random_int(0, 100)).strval(random_int(0, 100)).strval(random_int(0, 100));
         $data = [
                 "subject"=>"Reset Code: ".\GlobalsFunctions\Globals::titleView(),
                 "message"=>"<p>Your reset code is <stron>{$code}</stron> please dont share with anyone</p>",
                 "attached"=>false,
                 "reply"=>false,
                 "altbody"=>\GlobalsFunctions\Globals::titleView(),
                 "user"=>array(htmlspecialchars(strip_tags($_POST['email']))),
         ];

         if($_POST['password'] === $_POST['confirm-password']){
             $forgot = [
                     "code"=>$code,
                 "newpassword"=>htmlspecialchars(strip_tags($_POST['password']))
             ];

             $user = \Datainterface\Selection::selectById('users', ['mail'=>htmlspecialchars(strip_tags($_POST['email']))]);
             if(!empty($user)){
                 if(\FormViewCreation\ForgotPassword::forgotPassword($data)){
                     $user['forgot'] = $forgot;
                     \Sessions\SessionManager::setSession('forgot', $user);
                     $sections = true;
                 }
             }else{
                 echo \Alerts\Alerts::alert('danger', "Email don`t match any email in system");
             }
         }
      }

  }

  if(isset($_POST['code-confirm-default-button'])){
     $user = \Sessions\SessionManager::getSession('forgot');
     if(empty($user)){
         echo \Alerts\Alerts::alert('warning', "Sorry something went wrong during wait to insert code");
     }else{
         $code = htmlspecialchars(strip_tags($_POST['code']));
         if($code === $user['forgot']['code']){
             if(\FormViewCreation\ForgotPassword::changePassword($user['forgot']['newpassword'],$user[0]['uid'])){
                 echo \Alerts\Alerts::alert('info', "Password changed successfully!");
                 $sections = false;
             }else{
                 echo \Alerts\Alerts::alert('warning', "Failed to change password");
             }
         }
     }
  }
}
?>
<?php if($sections === false): ?>
<section class="bg-gray-50 dark:bg-gray-900">
  <div class="flex flex-col items-center justify-center px-6 py-8 mx-auto md:h-screen lg:py-0">
      <a href="#" class="flex items-center mb-6 text-2xl font-semibold text-gray-900 dark:text-white">
          <img class="w-8 h-8 mr-2" src="https://flowbite.s3.amazonaws.com/blocks/marketing-ui/logo.svg" alt="logo">
          Flowbite    
      </a>
      <div class="w-full p-6 bg-white rounded-lg shadow dark:border md:mt-0 sm:max-w-md dark:bg-gray-800 dark:border-gray-700 sm:p-8">
          <h2 class="mb-1 text-xl font-bold leading-tight tracking-tight text-gray-900 md:text-2xl dark:text-dark">
              Change Password
          </h2>
          <form class="mt-4 space-y-4 lg:mt-5 md:space-y-5" action="<?php echo \GlobalsFunctions\Globals::url(); ?>" method="POST">
              <div>
                  <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-dark">Your email</label>
                  <input type="email" name="email" id="email" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="name@company.com" required="">
              </div>
              <div>
                  <label for="password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-dark">New Password</label>
                  <input type="password" name="password" id="password" placeholder="••••••••" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required="">
              </div>
              <div>
                  <label for="confirm-password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-dark">Confirm password</label>
                  <input type="confirm-password" name="confirm-password" id="confirm-password" placeholder="••••••••" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required="">
              </div>
              <div class="flex items-start">
                  <div class="flex items-center h-5">
                    <input id="newsletter" aria-describedby="newsletter" type="checkbox" class="w-4 h-4 border border-gray-300 rounded bg-gray-50 focus:ring-3 focus:ring-primary-300 dark:bg-gray-700 dark:border-gray-600 dark:focus:ring-primary-600 dark:ring-offset-gray-800" required="">
                  </div>
                  <div class="ml-3 text-sm">
                    <label for="newsletter" class="font-light text-gray-500 dark:text-gray-300">I accept the <a class="font-medium text-primary-600 hover:underline dark:text-primary-500" href="#">Terms and Conditions</a></label>
                  </div>
              </div>
              <button type="submit" name="forgot-default-button" class="w-full text-white bg-primary-600 hover:bg-primary-700 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">Reset passwod</button>
          </form>
      </div>
  </div>
</section>
<?php else: ?>
    <section class="bg-gray-50 dark:bg-gray-900">
        <div class="flex flex-col items-center justify-center px-6 py-8 mx-auto md:h-screen lg:py-0">
            <a href="#" class="flex items-center mb-6 text-2xl font-semibold text-gray-900 dark:text-white">
                <img class="w-8 h-8 mr-2" src="https://flowbite.s3.amazonaws.com/blocks/marketing-ui/logo.svg" alt="logo">
                Flowbite
            </a>
            <div class="w-full p-6 bg-white rounded-lg shadow dark:border md:mt-0 sm:max-w-md dark:bg-gray-800 dark:border-gray-700 sm:p-8">
                <h2 class="mb-1 text-xl font-bold leading-tight tracking-tight text-gray-900 md:text-2xl dark:text-dark">
                    Change Password
                </h2>
                <form class="mt-4 space-y-4 lg:mt-5 md:space-y-5" action="<?php echo \GlobalsFunctions\Globals::url(); ?>" method="POST">
                    <div>
                        <label for="confirm-password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-dark">Confirm password</label>
                        <input type="text" name="code" id="code" placeholder="Code" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required="">
                    </div>
                    <button type="submit" name="code-confirm-default-button" class="w-full text-white bg-primary-600 hover:bg-primary-700 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">Verify code</button>
                </form>
            </div>
        </div>
    </section>
<?php endif; ?>