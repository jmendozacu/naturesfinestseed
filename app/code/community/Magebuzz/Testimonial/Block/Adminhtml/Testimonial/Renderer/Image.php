<?php
/**
 * @category  Magebuzz
 * @package   Magebuzz_Testimonial
 * @version   0.1.5
 * @copyright Copyright (c) 2012-2015 http://www.magebuzz.com
 * @license   http://www.magebuzz.com/terms-conditions/
 */
class Magebuzz_Testimonial_Block_Adminhtml_Testimonial_Renderer_Image extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract{

    public function render(Varien_Object $row)
    {
        if($row->getData($this->getColumn()->getIndex())==""){
            return "";
        }else{
            $html = '<img ';
            $html .= 'id="' . $this->getColumn()->getId() . '" ';
            $html .= 'width="80" ';
            $html .= 'height="80" ';
            $html .= 'src="' . Mage::getBaseUrl("media") .'magebuzz/avatar/' . $row->getData($this->getColumn()->getIndex()) . '"';
            $html .= 'class="grid-image ' . $this->getColumn()->getInlineCss() . '"/>';
        }

        return $html;
    }
}