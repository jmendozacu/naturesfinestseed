<?php
/**
 * Mageplaza_BetterBlog extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category       Mageplaza
 * @package        Mageplaza_BetterBlog
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Tag Posts list block
 *
 * @category    Mageplaza
 * @package     Mageplaza_BetterBlog
 * @author      Sam
 */
class Mageplaza_BetterBlog_Block_Tag_Post_List extends Mageplaza_BetterBlog_Block_Post_List
{
    /**
     * initialize
     *
     * @access public
     * @return void
     * @author Sam
     */
    public function __construct()
    {
        parent::__construct();
        $tag = $this->getTag();
         if ($tag) {
             $this->getPosts()->addTagFilter($tag->getId());
             $this->getPosts()->unshiftOrder('related_tag.position', 'ASC');
         }
    }

    /**
     * prepare the layout
     *
     * @access protected
     * @return Mageplaza_BetterBlog_Block_Tag_Post_List
     * @author Sam
     */
    protected function _prepareLayout()
    {
        return $this;
    }

    /**
     * get the current tag
     *
     * @access public
     * @return Mageplaza_BetterBlog_Model_Tag
     * @author Sam
     */
    public function getTag()
    {
        return Mage::registry('current_tag');
    }
}
