<?php

namespace App\Modules;

/*
  Filename : ModulesServiceProvider.php
  Author : Inbound Team (Rohit Singh, Vijaya Bhaskar Chenna, Mohan Kumar Narukulla)
  CreateData : 20-May-2016
  Desc : The service provider for the modules. After being registeredit
  will make sure that each of the modules are properly loaded i.e. with their routes, views etc.
 */
use \Session;
class ModulesServiceProvider extends \Illuminate\Support\ServiceProvider {

    /**
     * Will make sure that the required modules have been fully loaded
     * @return void
     */
    public function boot() {
        // For each of the registered modules, include their routes and Views
        $modules = config("module.modules");

        while (list(, $module) = each($modules)) {

            // Load the routes for each of the modules
            if (file_exists(__DIR__ . '/' . $module . '/routes.php')) {
                include __DIR__ . '/' . $module . '/routes.php';
            }

            // Load the views
            if (is_dir(__DIR__ . '/' . $module . '/Views')) {
                $this->loadViewsFrom(__DIR__ . '/' . $module . '/Views', $module);
            }
        }
    }

    public function register() {
        
    }

}
