<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Sphinx Search Ultimate
 * @version   2.2.8
 * @revision  334
 * @copyright Copyright (C) 2013 Mirasvit (http://mirasvit.com/)
 */


/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Sphinx Search Ultimate
 * @version   2.2.8
 * @revision  299
 * @copyright Copyright (C) 2013 Mirasvit (http://mirasvit.com/)
 */


/**
 * Promotional Offers list
 *
 * @category Mirasvit
 * @package  Mirasvit_SearchIndex
 */
class Mirasvit_SearchIndex_Block_Result_Vbulletin extends Mirasvit_SearchIndex_Block_Result_Abstract
{
    public function getIndexCode()
    {
        return 'vbulletin';
    }

    public function getPostUrl($post)
    {
        return Mage::getStoreConfig('searchindex/vbulletin/base_url').$post->getThreadid();
    }
}