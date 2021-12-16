<?php

namespace Margl\AdditionalGallery\Install;

use Additional_Gallery;
use Exception;

class Installer
{
    private $module;
    private $connection;
    private $db_prefix;
    private $db_engine;

    /**
     * @param $module Additional_Gallery
     * @throws Exception
     */
    public function __construct($module)
    {
        $this->module = $module;
        $this->connection = $this->module->get('doctrine.dbal.default_connection');
        $this->db_prefix = $this->module->getContainer()->getParameter('database_prefix');
        $this->db_engine = $this->module->getContainer()->getParameter('database_engine');;
    }

    /**
     * Main module install function
     * @return bool
     */
    public function install()
    {
        return $this->registerHooks()
            && $this->createImageDir()
            && $this->createTables();
    }

    /**
     * Register module required hooks
     * @return bool
     */
    private function registerHooks()
    {
        return $this->module->registerHook('displayAdminProductsMainStepLeftColumnMiddle')
            && $this->module->registerHook('displayFooterProduct')
            && $this->module->registerHook('actionAdminControllerSetMedia');
    }

    /**
     * Create a directory for image storage
     * @return bool
     */
    private function createImageDir()
    {
        $image_dir = _PS_IMG_DIR_.'p_additional';
        //check if directory exists
        if(!file_exists($image_dir)) {
            //create image dir
            mkdir($image_dir);
        }

        return true;
    }

    /**
     * Create Db tables
     * @return bool
     */
    private function createTables()
    {
        $install_file = __DIR__ . '/../../Resources/data/install.sql';
        $sql_queries = file_get_contents($install_file);
        $sql_queries = str_replace(['PREFIX_', 'DB_ENGINE'], [$this->db_prefix, $this->db_engine], $sql_queries);

        return $this->connection->executeQuery($sql_queries);
    }
}