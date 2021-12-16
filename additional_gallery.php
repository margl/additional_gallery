<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once 'vendor/autoload.php';

use Margl\AdditionalGallery\Install\Installer;
use Margl\AdditionalGallery\Install\Uninstaller;
use PrestaShop\PrestaShop\Adapter\SymfonyContainer;

class Additional_Gallery extends Module 
{
    public function __construct()
    {
        $this->name = 'additional_gallery';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Martynas Glinskis';

        parent::__construct();

        $this->displayName = $this->trans('Additional Gallery', [], 'Modules.Additionalgallery.Admin');
        $this->description = $this->trans('Add an additional gallery to your product page', [], 'Modules.Additionalgallery.Admin');
        $this->ps_versions_compliancy = array('min' => '1.7.6.0', 'max' => _PS_VERSION_);
        $this->registerHook('actionAdminControllerSetMedia');
    }

    /**
     * Module installer
     * @return bool
     */
    public function install() 
    {
        xdebug_break();
        $installer = new Installer($this);

        return parent::install() && $installer->install();
    }

    /**
     * Module uninstaller
     * @return bool
     */
    public function uninstall() 
    {
        $uninstaller = new Uninstaller($this);

        return parent::uninstall() && $uninstaller->uninstall();
    }

    /**
     * Used to load js/css
     * @throws Exception
     */
    public function hookActionAdminControllerSetMedia()
    {
        //check if it's a product controller to make sure we aren't trying to load symfony context elsewhere
        if($this->context->controller->controller_name === 'AdminProducts' && $this->isSymfonyContext()) {
            //get symfony request to check the route
            $request = $this->get('request_stack')->getCurrentRequest();
            //check if it's a product form
            if($request->attributes->get('_route') === 'admin_product_form') {
                //load js/css
                $this->context->controller->addJS(
                    _PS_MODULE_DIR_.$this->name.'/views/js/admin/additional_gallery.js'
                );
                $this->context->controller->addCSS(
                    _PS_MODULE_DIR_.$this->name.'/views/css/admin/additional_gallery.css'
                );
            }
        }
    }

    /**
     * Display image upload input
     * @param $params
     * @return mixed
     * @throws Exception
     */
    public function hookDisplayAdminProductsMainStepLeftColumnMiddle($params) 
    {
        return $this->get('twig')->render(
            '@Modules/additional_gallery/views/templates/admin/upload.twig',
            [
                'idProduct' => $params['id_product']
            ]
        );
    }
}