<?php

require_once Mage::getModuleDir('controllers', 'Mage_Adminhtml').DS.'Cms/WysiwygController.php';

class Aicod_AdminWysiwygImageUrlFix_Cms_WysiwygController extends Mage_Adminhtml_Cms_WysiwygController
{
    public function directiveAction()
    {
        $directive = $this->getRequest()->getParam('___directive');
        $directive = Mage::helper('core')->urlDecode($directive);
        $url = Mage::getModel('core/email_template_filter')->filter($directive);
        try {
            $image = Varien_Image_Adapter::factory('GD2');
            $urlinfo = parse_url($url);
            if($urlinfo['scheme'] || $urlinfo['host']) {
              $url = trim($urlinfo['path'],'/');
            }
            $notok = (!is_file($url) || !is_readable($url));
            //var_dump($urlinfo);
            //var_dump($url);
            //var_dump($notok);
            //exit;
            if($notok) {
              throw new Exception("Wysiwyg image not found or not readable: {$url}!");
            }
            $image->open($url);
            $image->display();
        } catch (Exception $e) {
            $url = Mage::getSingleton('cms/wysiwyg_config')->getSkinImagePlaceholderUrl();
            $notok = (!is_file($url) || !is_readable($url));
            if(!$notok) {
              $image = Varien_Image_Adapter::factory('GD2');
              $image->open($url);
              $image->display();
            } else {
              //$image = imagecreate(100, 100);
              $image = imagecreate(100, 100);
              $bkgrColor = imagecolorallocate($image,10,10,10);
              imagefill($image,0,0,$bkgrColor);
              $textColor = imagecolorallocate($image,255,255,255);
              imagestring($image, 4, 10, 10, 'Skin image', $textColor);
              //imagestring($image, 4, 10, 10, 'Image Not Found', $textColor);
              header('Content-type: image/png');
              imagepng($image);
              imagedestroy($image);
            }
        }
    }
}
