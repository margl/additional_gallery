<?php

namespace Margl\AdditionalGallery\Controller\Admin;

use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use HelperImageUploader;
use Tools;

class ImageUploadController extends FrameworkBundleAdminController
{

    /**
     * Process image upload
     * @param Request $request
     */
    public function uploadAction(Request $request)
    {
        //check if any images are present in request
        if(!$request->files->get('additional_gallery_files')) {
            return new JsonResponse(
                ['error' => $this->trans('No files provided', 'Modules.Additionalgallery.Admin')],
                500
            );
        }

        //check for product ID
        if(!$request->get('productId')) {
            return new JsonResponse(
                ['error' => $this->trans('Product not found', 'Modules.Additionalgallery.Admin')],
                500
            );
        }

        //validate images
        $image_helper = new HelperImageUploader('additional_gallery_files');
        $image_helper->setAcceptTypes(['jpeg', 'gif', 'png', 'jpg'])->setMaxSize(Tools::getMaxUploadSize());
        $files = $image_helper->process();

        foreach ($files as $file) {
            //todo: create new AdditionalImage object and save/resize the file to appropriate location
        }
    }

    /**
     * Process image deletion
     * @param Request $request
     */
    public function deleteAction(Request $request) {

    }
}