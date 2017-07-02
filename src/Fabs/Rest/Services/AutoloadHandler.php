<?php
/**
 * Created by PhpStorm.
 * User: ahmetturk
 * Date: 19/06/2017
 * Time: 15:42
 */

namespace Fabs\Rest\Services;


use Fabs\Rest\APIBase;
use Fabs\Rest\TaskBase;
use Phalcon\Loader;
use Phalcon\Mvc\Micro\Collection;
use ReflectionClass;

class AutoloadHandler extends ServiceBase
{
    private $api_list = [];

    private $task_list = [];
    private $registered_namespaces = [];
    private $registered_folders = [];

    public function registerFolder($folder_name)
    {
        $this->registered_folders[] = $folder_name;
    }

    public function registerNamespace($namespace_name, $folder_name)
    {
        $this->registered_namespaces[$namespace_name] = $folder_name;
    }

    /**
     * @return APIBase[]
     */
    private function getAPIList()
    {
        $this->loadFolders();

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

    /**
     * @return TaskBase[]
     */
    private function getTaskList()
    {
        $this->loadFolders();
        $handlers = [];
        foreach ($this->task_list as $key => $task) {
            if (is_string($task)) {
                $task = new $task;
                $this->task_list[$key] = $task;
            }
            $handlers[] = $task;
        }
        return $handlers;
    }

    public function mount()
    {
        if (PHP_SAPI === 'cli') {
            foreach ($this->getTaskList() as $task) {
                $collection = new Collection();
                $collection->setHandler($task)
                    ->setPrefix('/task/')
                    ->get($task->getName(), 'execute');
                $this->application->mount($collection);
            }
        } else {
            foreach ($this->getAPIList() as $api) {
                $api->mount();
            }
        }
    }

    private function loadFolders()
    {
        $this->registered_folders = array_unique($this->registered_folders);

        $loader = new Loader();

        if (count($this->registered_folders) > 0) {
            $loader->registerDirs($this->registered_folders);
        }

        if (count($this->registered_namespaces) > 0) {
            $loader->registerNamespaces($this->registered_namespaces);
        }

        $loader->register();

        foreach ($this->registered_folders as $folder_name) {
            $this->loadFolderWithNamespace(null, $folder_name);
            unset($this->registered_folders[$folder_name]);
        }

        foreach ($this->registered_namespaces as $namespace_name => $folder_name) {
            $this->loadFolderWithNamespace($namespace_name, $folder_name);
            unset($this->registered_namespaces[$namespace_name]);
        }
    }

    private function loadFolderWithNamespace($namespace_name, $folder_name)
    {
        $files = glob($folder_name . '/*.php');
        foreach ($files as $file) {
            $class_name = basename($file, '.php');

            if ($namespace_name !== null) {
                $class_name = $namespace_name . '\\' . $class_name;
            }

            if (class_exists($class_name)) {
                $reflection = new ReflectionClass($class_name);
                if (!$reflection->isAbstract()) {
                    if (PHP_SAPI == 'cli') {
                        if ($reflection->isSubclassOf(TaskBase::class)) {
                            $this->task_list[] = $class_name;
                        }
                    } else {
                        if ($reflection->isSubclassOf(APIBase::class)) {
                            $this->api_list[] = $class_name;
                        }
                    }
                }
            }
        }
    }
}