<?php
namespace CreationView;
use FormViewCreation\ViewCreation;
use GlobalsFunctions\Globals;

@session_start();

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    if(isset($_POST['creating-view-default'])){
        if(ViewCreation::valiadateForm($_POST)){
            viewCreation::submitForm($_SESSION['forms']['view-creation-form-data-storage']);
            Globals::redirect('creating-view');
        }else{
            $message = $_SESSION['message']['creationviewform'];
            Globals::redirect('creating-view');
        }
    }

}
?>
<div class="hidden sm:block" aria-hidden="true">
    <div class="py-5">
        <div class="border-t border-gray-200"></div>
    </div>
</div>

<div class="mt-10 sm:mt-0">
    <div class="md:grid md:grid-cols-3 md:gap-6">
        <div class="md:col-span-1">
            <div class="px-4 sm:px-0">
                <h3 class="text-lg font-medium leading-6 text-gray-900"><?php  echo $_SESSION['public_data']['view']['view_name']; ?></h3>
                <p class="mt-1 text-sm text-gray-600"><?php  echo $_SESSION['public_data']['view']['view_description']; ?></p>
                <?php if(isset($_SESSION['message']['creationviewform'])){ echo $_SESSION['message']['creationviewform'];} ?>
            </div>
        </div>
        <div class="mt-5 md:col-span-2 md:mt-0">
            <form action="<?php echo $_SESSION['public_data']['view']['view_url']; ?>" method="POST">
                <div class="overflow-hidden shadow sm:rounded-md">
                    <div class="bg-white px-4 py-5 sm:p-6">
                        <div class="grid grid-cols-6 gap-6">
                            <div class="col-span-6 sm:col-span-3">
                                <label for="view-name" class="block text-sm font-medium text-gray-700">View name</label>
                                <input type="text" name="view-name" id="view-name" autocomplete="given-name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label for="view-url" class="block text-sm font-medium text-gray-700">View url</label>
                                <input type="text" name="view-url" id="view-url" autocomplete="family-name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <span id="warnings"></span>
                            </div>

                            <div class="col-span-6 sm:col-span-4">
                                <label for="path-address" class="block text-sm font-medium text-gray-700">View path (.extension)</label>
                                <input type="text" name="path-address" id="path-address" autocomplete="email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                            <div class="col-span-6 sm:col-span-4">
                                <label for="path-address" class="block text-sm font-medium text-gray-700">Is default view</label>
                                <input type="checkbox" name="default" id="path-address" autocomplete="email" class="mt-1">
                            </div>
                            <div class="col-span-6 sm:col-span-3">
                                <label for="accessible" class="block text-sm font-medium text-gray-700">Accessibility</label>
                                <select id="accessible" name="accessible" autocomplete="country-name" class="mt-1 block w-full rounded-md border border-gray-300 bg-white py-2 px-3 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm">
                                    <option value="public">Public</option>
                                    <option value="private">Private</option>
                                    <option value="moderator">Moderator</option>
                                    <option value="administrator">Administrator</option>
                                </select>
                            </div>
                            <div class="col-span-6">
                                <label for="description" class="block text-sm font-medium text-gray-700">View description</label>
                                <textarea cols="9" rows="9" name="description" id="description" autocomplete="street-address" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 text-right sm:px-6">
                        <button type="submit" id="lock" name="creating-view-default" class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="hidden sm:block" aria-hidden="true">
    <div class="py-5">
        <div class="border-t border-gray-200"></div>
    </div>
</div>
<div>
    <script type="application/javascript">
        const urlInput = document.getElementById('view-url').addEventListener('input', (e)=>{
            const inputData = e.target.value;
            if(inputData !== null){
                let found = inputData.indexOf('/');
                if(found >= 0){
                    document.getElementById('lock').setAttribute('disabled', 'true');
                    let sp = document.getElementById('warnings');
                    sp.style.color = 'red';
                    sp.innerText = "(/) is not allowed!";
                }else{
                    document.getElementById('warnings').innerText = " ";

                }
            }
        })
    </script>
</div>
