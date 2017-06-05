<?php
/**
 * Created by PhpStorm.
 * User: ahmetturk
 * Date: 05/06/2017
 * Time: 10:30
 */

namespace Fabs\Rest\Services;


use Fabs\Rest\APIBase;
use Phalcon\Di\Injectable;
use Phalcon\Loader;
use ReflectionClass;

class APIHandler extends ServiceBase
{
    private $api_list = [];
    private $registered_folders = [];

    public function add($api_name_or_instance)
    {
        if ($api_name_or_instance instanceof APIBase || is_string($api_name_or_instance)) {
            $this->api_list[$api_name_or_instance] = $api_name_or_instance;
        }
    }

    public function registerFolder($folder_name)
    {
        $this->registered_folders[$folder_name] = $folder_name;
    }

    /**
     * @return APIBase[]
     */
    public function getAPIList()
    {
        $this->loadFoldersAPI();

        $handlers = [];

        foreach ($this->api_list as $key => $api) {
            if (is_string($api)) {
                $api = new $api;
                $this->api_list[$key] = $api;
            }
            $handlers[] = $api;
        }

        return $handlers;
    }

    public function mount()
    {
        foreach ($this->getAPIList() as $api) {
            $api->mount();
        }
    }

    private function loadFoldersAPI()
    {
        if (count($this->registered_folders) > 0) {
            $loader = new Loader();
            $loader->registerDirs($this->registered_folders)->register();

            foreach ($this->registered_folders as $folder_name) {
                $files = glob($folder_name . '/*.php');
                foreach ($files as $file) {
                    $class_name = basename($file, '.php');
                    if (class_exists($class_name)) {
                        $reflection = new ReflectionClass($class_name);
                        if (!$reflection->isAbstract() && $reflection->isSubclassOf(APIBase::class)) {
                            $this->api_list[] = $class_name;
                        }
                    }
                }

                unset($this->registered_folders[$folder_name]);
            }
        }
    }
}