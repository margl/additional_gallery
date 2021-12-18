<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once 'vendor/autoload.php';

use PrestaShop\Module\AdditionalGallery\Install\Installer;
use PrestaShop\Module\AdditionalGallery\Install\Uninstaller;
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
        $this->registerHook('displayAfterProductThumbs');
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
        /** @var \PrestaShop\Module\AdditionalGallery\Repository\AdditionalImageRepository $image_repository */
        $image_repository = $this->get('prestashop.module.additionalimage.repository.additional_image_repository');

        return $this->get('twig')->render(
            '@Modules/additional_gallery/views/templates/admin/upload.twig',
            [
                'idProduct' => $params['id_product'],
                'additionalImages' => $image_repository->getByProductId($params['id_product'])
            ]
        );
    }
    
    public function hookDisplayAfterProductThumbs($params) {
        $product = $params['product'];

        /** @var \PrestaShop\Module\AdditionalGallery\Repository\AdditionalImageRepository $image_repository */
        $image_repository = $this->get('prestashop.module.additionalimage.repository.additional_image_repository');
        $additional_images = $image_repository->getByProductId($product->id);

        if(empty($additional_images)) {
            return;
        }

        $this->context->smarty->assign([
            'additionalImages' => $additional_images
        ]);

        return $this->context->smarty->fetch('module:additional_gallery/views/templates/front/gallery.tpl');
    }
}