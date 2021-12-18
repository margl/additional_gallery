<?php

namespace PrestaShop\Module\AdditionalGallery\Install;

use Additional_Gallery;
use PrestaShop\PrestaShop\Core\Exception\ContainerNotFoundException;

class Uninstaller
{
    private $module;
    private $connection;
    private $db_prefix;

    /*
     * TODO: Should I delete the Image directory (and files within) or leave it be if module is uninstalled?
     */

    /**
     * @param $module Additional_Gallery
     * @throws ContainerNotFoundException
     */
    public function __construct($module)
    {
        $this->module = $module;
        $this->connection = $this->module->get('doctrine.dbal.default_connection');
        $this->db_prefix = $this->module->getContainer()->getParameter('database_prefix');
    }

    /**
     * Main module uninstall function
     * @return bool
     */
    public function uninstall()
    {
        return $this->dropTables();
    }

    /**
     * Drop module tables
     * @return bool
     */
    private function dropTables()
    {
        $install_file = __DIR__ . '/../../Resources/data/uninstall.sql';
        $sql_queries = file_get_contents($install_file);
        $sql_queries = str_replace('PREFIX_', $this->db_prefix, $sql_queries);

        return $this->connection->executeQuery($sql_queries);
    }
}