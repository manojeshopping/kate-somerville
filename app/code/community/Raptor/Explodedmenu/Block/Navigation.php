<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Catalog navigation
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Raptor_Explodedmenu_Block_Navigation extends Mage_Catalog_Block_Navigation
{
	public function drawItem($category,$first=false, $level=0, $last=false) {
		$html = '';
		if (!$category->getIsActive()) {
			return $html;
		}
		$main_category		=	strtolower(str_replace(' ','-',$category->getName()));
		$activeChildren = $this->getActiveChildren($category);
		$html.= '<li class="top_level';
		if ($first) {
			$html .= ' first';
		}
		if (sizeof($activeChildren) > 0) {
			$html .= ' '.$main_category."-nav";
		}

		$html .= '"';

		if (sizeof($activeChildren) > 0) {
			$html.= ' onmouseover="toggleMenu(this,1)" onmouseout="toggleMenu(this,0)"';		}
		if ($last) {
			$html .= ' last';
		}
		$html.= '>'."\n";
		$html.= '<a href="'.$this->getCategoryUrl($category).'"><span>'.$this->htmlEscape($category->getName()).'</span></a>'."\n";
		
		if (sizeof($activeChildren) > 0) {
			$main_category_cms_block	=	$main_category."-cms-block";
			$html .= $this->drawColumns($activeChildren,$main_category_cms_block);
		}
		$html .= "</li>";
		return $html;
	}

	/**
	 * Responsible for splitting the drop down box into columns and rendering the nested menus
	 *
	 * @param unknown_type $children
	 * @return unknown
	 */
	public function drawColumns($children,$main_category_cms_block='') {
		$categoriesPerColumn = $this->getConfigData('explodedmenu', 'columns', 'categories_per_column');

		$html = '';
		$chunks = array_chunk($children, $categoriesPerColumn);
		$html .= '<ul>';
		$i = 0;
		$html .=	$this->getLayout()->createBlock('cms/block')->setBlockId($main_category_cms_block."-before-submenu")->toHtml();
		foreach ($chunks as $key=>$value) {
			$html .= '<li class="columns">';
			$html .= $this->drawNestedMenus($value, 1);
			$html .= '</li>';
			$i++;
		}
		$html .=	$this->getLayout()->createBlock('cms/block')->setBlockId($main_category_cms_block)->toHtml();
		//$html .=	$this->getLayout()->createBlock('cms/block')->setBlockId('main-navigation-banner')->toHtml();
		//$html .= '<div class="nav-banner"><img src="'.$this->getSkinUrl("images/take-the-quiz.gif").'" /></li></div>';
		$html .= '</ul>';
		return $html;
	}

	public function drawNestedMenus($children, $level=1) {
		$html = '<ul>';
		foreach ($children as $child) {
			if ($child->getIsActive()) {
				$html .= '<li class="level' . $level . '">';
				$activeChildren = $this->getActiveChildren($child);
				if($level==1 && sizeof($activeChildren) > 0)
				{
					$html .= '<span class="nav-shopby">'.$this->htmlEscape($child->getName()).'</span>';
				}else{
					$html .= '<a href="'.$this->getCategoryUrl($child).'"><span>'.$this->htmlEscape($child->getName()).'</span></a>';
				}
				if (sizeof($activeChildren) > 0) {
					$html .= $this->drawNestedMenus($activeChildren, $level+1);
				}
				$html .= '</li>';
			}
		}
		$html .= '</ul>';
		return $html;
	}

	/**
	 * Gets all the active children of a category and puts them into an array. N.B. 
	 * we need an array because of the array_chunk() call in drawColumns();
	 *
	 * @param Category $parent
	 * @return unknown
	 */
	protected function getActiveChildren($parent) {
		$activeChildren = array();
		if (Mage::helper('catalog/category_flat')->isEnabled()) {
			$children = $parent->getChildrenNodes();
			$childrenCount = count($children);
		} else {
			$children = $parent->getChildren();
			$childrenCount = $children->count();
		} 
		$hasChildren = $children && $childrenCount;
		if ($hasChildren) {
			foreach ($children as $child) {
				if ($child->getIsActive()) {
					array_push($activeChildren, $child);
				}
			} 
		}
		return $activeChildren;
	}

    /**
     * Get url for category data
     *
     * @param Mage_Catalog_Model_Category $category
     * @return string
     */
    public function getCategoryPath($category)
    {        
		$url = '';
		if ($category instanceof Mage_Catalog_Model_Category) {
        $url = $category->getPathInStore();
	    $url = strtr($url, ".", "-");
	    $url = strtr($url, "/", "-");
        } else {
			// do nothing
        }
        return $url;
    }
	
	public function getConfigData($namespace, $parentKey, $key) {
		$config = Mage::getStoreConfig($namespace);
		if (isset($config[$parentKey]) && isset($config[$parentKey][$key]) && strlen($config[$parentKey][$key]) > 0) {
			$value = $config[$parentKey][$key];
			return $value;
		} else {
			throw new Exception('Value not set');
		}
	}	

}
