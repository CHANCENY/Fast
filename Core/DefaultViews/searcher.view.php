<?php @session_start();
$t = new \Datainterface\mysql\TablesLayer();
$tables = $t->getTables()->tables();
global $placeholder;
?>
<form id="form-search" method="GET" action="#" class="w-100 float-end">
    <div class="flex">
            <select name="table" id="table" class="py-2 text-sm text-dark-700 dark:text-dark-200">
                <?php if(!empty($tables)): ?>
                <?php foreach ($tables as $key=>$value): ?>
                        <option value="<?php echo $value; ?>" class="inline-flex w-full px-4 py-2 hover:bg-dark-100 dark:hover:bg-gray-600 dark:hover:text-white">
                            <?php $name = str_replace('_',' ',$value);
                               $name = ucfirst($name);
                               $placeholder .= "{$name} ";
                               echo $name;
                            ?>
                        </option>
                <?php endforeach; ?>
                <?php endif; ?>
            </select>
        <div class="relative w-full">
            <input type="search" name="q" id="search-dropdown" class="block p-2.5 w-full z-20 text-sm text-gray-900 bg-gray-50 rounded-r-lg border-l-gray-50 border-l-2 border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-l-gray-700  dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:border-blue-500" placeholder="Search <?php echo trim($placeholder).'...'; ?>" required>
            <button type="submit" class="absolute top-0 right-0 p-2.5 text-sm font-medium text-white bg-blue-700 rounded-r-lg border border-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                <svg aria-hidden="true" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                <span class="sr-only">Search</span>
            </button>
        </div>
    </div>
</form>
<div>
    <script type="application/javascript">
        const categories = document.getElementById('table');
        if(categories !== null){
            categories.addEventListener('change', (e)=>{
                const values = e.target.value;
                let formTag = document.getElementById('form-search');
                switch (values){
                    case 'users':
                        formTag = document.getElementById('form-search');
                        if(formTag !== null){
                            formTag.action = 'users';
                        }
                        break;
                    case 'errors_logs':
                        formTag = document.getElementById('form-search');
                        if(formTag !== null){
                            formTag.action = 'errors';
                        }
                        break;
                    default:
                        formTag = document.getElementById('form-search');
                        if(formTag !== null){
                            formTag.action = 'reciever';
                        }

                }
            })
        }

        const formInSelf = document.getElementById('form-search');
        if(formInSelf !== null){
            formInSelf.addEventListener('submit', (e)=>{
                e.preventDefault();
                const ac = formInSelf.action;
                const term = document.getElementById('search-dropdown').value;
                if(ac.includes('users') || ac.includes('errors')){
                    let url = `${ac}?q=${term}`;
                    window.location.replace(url);
                }else{
                    const tt = document.getElementById('table').value;
                    const link = `${ac}?searchfor=${tt}&q=${term}`;
                    window.location.replace(link);
                }

            })
        }
    </script>
</div>

