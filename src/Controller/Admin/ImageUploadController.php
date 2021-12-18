<?php

namespace PrestaShop\Module\AdditionalGallery\Controller\Admin;

use PrestaShop\Decimal\Operation\Addition;
use PrestaShop\Module\AdditionalGallery\Entity\AdditionalImage;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use HelperImageUploader;
use Tools;
use ImageManager;

class ImageUploadController extends FrameworkBundleAdminController
{

    /**
     * Process image upload
     * @param Request $request
     */
    public function uploadAction(Request $request)
    {
        //check if any images are present in request
        if (!$request->files->get('additional_gallery_files')) {
            return new JsonResponse(
                ['error' => $this->trans('No files provided', 'Modules.Additionalgallery.Admin')],
                500
            );
        }

        //check for product ID
        if (!$request->get('idProduct')) {
            return new JsonResponse(
                ['error' => $this->trans('Product not found', 'Modules.Additionalgallery.Admin')],
                500
            );
        }

        //validate images
        $image_helper = new HelperImageUploader('additional_gallery_files');
        $image_helper->setAcceptTypes(['jpeg', 'gif', 'png', 'jpg'])->setMaxSize(Tools::getMaxUploadSize());
        $files = $image_helper->process();

        //get entity manager
        $entity_manager = $this->get('doctrine.orm.default_entity_manager');
        $result = [];
        foreach ($files as $file) {
            if($file['error']) {
                $result[] = [
                    'image_name' => $file['name'],
                    'error' => $file['error']
                ];
                continue;
            }

            $image = new AdditionalImage();
            $image->setProductId($request->get('idProduct'));
            $image->setExtension($this->getFileExtension($file['name']));

            //save the image
            $entity_manager->persist($image);
            $entity_manager->flush();

            //if image is saved successfully move the file to the appropriate location and add it to results
            if (!$image->getId()) {
                return new JsonResponse(
                    ['error' => $this->trans('Failed to save an image', 'Modules.Additionalgallery.Admin')],
                    500
                );
            }

            if (!$this->createImageFolder($image->getImageFolder())) {
                return new JsonResponse(
                    [
                        'error' => $this->trans('There was a problem trying to create image folder',
                            'Modules.Additionalgallery.Admin')
                    ],
                    500
                );
            }

            $image->uri = $image->getImageUri();
            $image_path = $image->getImagePath();
            $save_res = $this->moveImage($file['save_path'], $image_path);
            if ($save_res !== true) {
                $result[] = [
                    'image_name' => $file['name'],
                    'error' => $this->translateImageSaveError($save_res)
                ];
            } else {
                $result[] = [
                    'content' => $this->render('@Modules/additional_gallery/views/templates/admin/partials/single-image.twig', ['image' => $image])->getContent()
                ];
            }
        }

        return new JsonResponse($result);
    }

    /**
     * Process image deletion
     * @param Request $request
     */
    public function deleteAction(Request $request)
    {
        $id_image = (int)$request->get('idImage');

        if (!$id_image) {
            return new JsonResponse(
                [
                    'error' => $this->trans('Image not found', 'Modules.Additionalgallery.Admin')
                ],
                500
            );
        }

        //load image
        $image_repository = $this->get('prestashop.module.additionalimage.repository.additional_image_repository');
        /** @var AdditionalImage $image */
        $image = $image_repository->findOneById($id_image);

        if (!$image->getId()) {
            return new JsonResponse(
                [
                    'error' => $this->trans('Failed to find the image', 'Modules.Additionalgallery.Admin')
                ],
                500
            );
        }

        if (file_exists($image->getImagePath())) {
            if (!unlink($image->getImagePath())) {
                return new JsonResponse(
                    [
                        'error' => $this->trans('There was a problem while trying to delete image',
                            'Modules.Additionalgallery.Admin')
                    ],
                    500
                );
            };
        }

        //get entity manager
        $entity_manager = $this->get('doctrine.orm.default_entity_manager');
        $entity_manager->remove($image);
        $entity_manager->flush();

        return new JsonResponse([
            'status' => 1,
            'message' => $this->trans('Image deleted successfully', 'Modules.Additionalgallery.Admin')
        ]);
    }

    private function createImageFolder($image_folder)
    {
        if (!file_exists($image_folder)) {
            // Apparently sometimes mkdir cannot set the rights, and sometimes chmod can't. Trying both.
            return @mkdir($image_folder, 0775, true)
                || @chmod($image_folder, 0775);
        }

        return true;
    }

    /**
     * Image saving
     * @param $temp_location string
     * @param $move_location string
     *
     * @returns int|bool
     */
    private function moveImage($temp_location, $move_location)
    {
        $error = 0;
        list($width, $height) = getimagesize($temp_location);
        $max_width = 800;
        $max_height = ceil($height * ($max_width / $width));
        if (!ImageManager::resize($temp_location, $move_location, $max_width, $max_height, 'jpg', false, $error)) {
            return $error;
        }

        return true;
    }

    /**
     * @param $error_code
     * @return string
     */
    private function translateImageSaveError($error_code)
    {
        switch ($error_code) {
            case ImageManager::ERROR_FILE_NOT_EXIST:
                return $this->trans('An error occurred while copying image, the file does not exist anymore.',
                    'Admin.Catalog.Notification');
            case ImageManager::ERROR_FILE_WIDTH:
                return $this->trans('An error occurred while copying image, the file width is 0px.',
                    'Admin.Catalog.Notification');
            case ImageManager::ERROR_MEMORY_LIMIT:
                return $this->trans('An error occurred while copying image, check your memory limit.',
                    'Admin.Catalog.Notification');
            default:
                return $this->trans('An error occurred while copying the image.', 'Admin.Catalog.Notification');
        }
    }

    private function getFileExtension($file_name)
    {
        $pos = strpos($file_name, '.');

        return $pos === false ? "" : strtolower(substr($file_name, $pos + 1));
    }
}