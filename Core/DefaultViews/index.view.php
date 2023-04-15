<?php @session_start(); ?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.6.3/flowbite.min.css" rel="stylesheet" />
<div class="mb-4 border-b border-gray-200 dark:border-gray-700">
    <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="myTab" data-tabs-toggle="#myTabContent" role="tablist">
        <li class="mr-2" role="presentation">
            <button class="inline-block p-4 border-b-2 rounded-t-lg" id="forgot-tab" data-tabs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="false">Forgot password</button>
        </li>
        <li class="mr-2" role="presentation">
            <button class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300" id="signingOut-tab" data-tabs-target="#dashboard" type="button" role="tab" aria-controls="dashboard" aria-selected="false">Signing Out</button>
        </li>
        <li class="mr-2" role="presentation">
            <button class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300" id="register-tab" data-tabs-target="#settings" type="button" role="tab" aria-controls="settings" aria-selected="false">Register account</button>
        </li>
        <li role="presentation">
            <button class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300" id="signing-tab" data-tabs-target="#contacts" type="button" role="tab" aria-controls="contacts" aria-selected="false">Signing in</button>
        </li>
    </ul>
</div>

<div class="mb-4 border-b border-gray-200 dark:border-gray-700">
    <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="myTab" data-tabs-toggle="#myTabContent" role="tablist">
       <?php if(!empty(\GlobalsFunctions\Globals::user()) && \GlobalsFunctions\Globals::user()[0]['role'] === "Admin"): ?>
        <?php foreach (\GlobalsFunctions\Globals::privateMenus() as $menu=>$value): ?>
        <li class="mr-2" role="presentation">
            <a href="<?php echo $value['view_url']; ?>" class="inline-block p-4 border-b-2 rounded-t-lg" id="forgot-tab" data-tabs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="false"><?php echo $value['view_name'];?></a>
        </li>
        <?php endforeach; ?>
        <?php endif; ?>
    </ul>
</div>

<div id="myTabContent">
    <div class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-800" id="profile" role="tabpanel" aria-labelledby="profile-tab">
        <p class="text-sm text-gray-500 dark:text-gray-400">This is some placeholder content the <strong class="font-medium text-gray-800 dark:text-white">Profile tab's associated content</strong>. Clicking another tab will toggle the visibility of this one for the next. The tab JavaScript swaps classes to control the content visibility and styling.</p>
    </div>
    <div class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-800" id="dashboard" role="tabpanel" aria-labelledby="dashboard-tab">
        <p class="text-sm text-gray-500 dark:text-gray-400">This is some placeholder content the <strong class="font-medium text-gray-800 dark:text-white">Dashboard tab's associated content</strong>. Clicking another tab will toggle the visibility of this one for the next. The tab JavaScript swaps classes to control the content visibility and styling.</p>
    </div>
    <div class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-800" id="settings" role="tabpanel" aria-labelledby="settings-tab">
        <p class="text-sm text-gray-500 dark:text-gray-400">This is some placeholder content the <strong class="font-medium text-gray-800 dark:text-white">Settings tab's associated content</strong>. Clicking another tab will toggle the visibility of this one for the next. The tab JavaScript swaps classes to control the content visibility and styling.</p>
    </div>
    <div class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-800" id="contacts" role="tabpanel" aria-labelledby="contacts-tab">
        <p class="text-sm text-gray-500 dark:text-gray-400">This is some placeholder content the <strong class="font-medium text-gray-800 dark:text-white">Contacts tab's associated content</strong>. Clicking another tab will toggle the visibility of this one for the next. The tab JavaScript swaps classes to control the content visibility and styling.</p>
    </div>
    <script type="application/javascript">
        const signIn = document.getElementById('signing-tab');
        signIn.addEventListener('click',()=> window.location.replace('sign-in'));
        const signUp = document.getElementById('register-tab');
        signUp.addEventListener('click',()=> window.location.replace('registration'));
        const signOut = document.getElementById('signingOut-tab');
        signOut.addEventListener('click', ()=> window.location.replace('sign-out'));
        const forgotP = document.getElementById('forgot-tab');
        forgotP.addEventListener('click', ()=> window.location.replace('reset-password'));
    </script>
</div>
<script src="../path/to/flowbite/dist/flowbite.min.js"></script>


