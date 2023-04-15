<?php @session_start(); ?>

<div class="relative overflow-x-auto">
    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
        <tr>
            <th scope="col" class="px-6 py-3">
                Database Name
            </th>
            <th scope="col" class="px-6 py-3">
                Database Host
            </th>
            <th scope="col" class="px-6 py-3">
                Database User
            </th>
            <th scope="col" class="px-6 py-3">
                Database Password
            </th>
        </tr>
        </thead>
        <tbody>
        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-dark">
                <?php echo \Datainterface\Database::getDbname(); ?>
            </th>
            <td class="px-6 py-4">
                <?php echo \Datainterface\Database::getHost(); ?>
            </td>
            <td class="px-6 py-4">
                <?php echo \Datainterface\Database::getUser(); ?>
            </td>
            <td class="px-6 py-4">
                <?php echo str_repeat("*", strlen(
                    empty(\Datainterface\Database::getPassword()) ?? "No password"
                )); ?>
            </td>
        </tr>
        </tbody>
    </table>
</div>

