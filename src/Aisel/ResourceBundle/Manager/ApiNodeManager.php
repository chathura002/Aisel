<?php

/*
 * This file is part of the Aisel package.
 *
 * (c) Ivan Proskuryakov
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Aisel\ResourceBundle\Manager;

use LogicException;
use Doctrine\ODM\MongoDB\DocumentManager;
use Aisel\ResourceBundle\Utility\UrlUtility;
use Aisel\ResourceBundle\Document\Category;

/**
 * ApiNodeManager
 *
 * @author Ivan Proskuryakov <volgodark@gmail.com>
 */
class ApiNodeManager
{

    /**
     * @var null
     */
    protected $model = null;

    /**
     * @var DocumentManager
     */
    protected $dm;

    /**
     * @var array
     */
    protected $locales;

    /**
     * Constructor
     *
     * @param DocumentManager $dm
     * @param string $locales
     */
    public function __construct(DocumentManager $dm, $locales)
    {
        $this->locales = explode('|', $locales);
        $this->dm = $dm;
    }

    /**
     * Save name for single node
     *
     * @param  array $params
     * @return object
     *
     * @throws LogicException
     */
    public function save($params)
    {
        if ($categoryId = $params['id']) {
            $node = $this
                ->dm
                ->getRepository($this->model)
                ->find($categoryId);

            if (!($node)) {
                throw new LogicException('Nothing was found');
            }
        }

        if ($params['name']) {
            $node->setTitle($params['name']);
        }

        $this->dm->persist($node);
        $this->dm->flush();

        return $node;
    }

    /**
     * Remove single node
     *
     * @param  array $params
     * @return object
     *
     * @throws LogicException
     */
    public function remove($params)
    {
        if ($categoryId = $params['id']) {
            $node = $this->dm->getRepository($this->model)->find($categoryId);

            if (!($node)) {
                throw new LogicException('Nothing found');
            }
        }

        $this->dm->remove($node);
        $this->dm->flush();

        return $node;
    }

    /**
     * Creates child node
     *
     * @param  array $params
     * @return object
     *
     * @throws LogicException
     */
    public function addChild($params)
    {
        /** @var $node Category */
        if ($categoryId = $params['parentId']) {
            $parent = $this->dm->getRepository($this->model)->find($categoryId);

            if (!($parent)) {
                throw new LogicException('Nothing found');
            }
        }

        $node = new $this->nodeEntity();

        $node->setTitle($params['name']);
        $node->setParent($parent);
        $node->setStatus(false);
        $this->dm->persist($node);
        $this->dm->flush();

//        // Update Parent
//        $parent->removeChild($node);
//        $parent->addChild($node);
//        $this->dm->persist($parent);
//        $this->dm->flush();

        return $node;
    }

    /**
     * Creates Node
     *
     * @param  array $params
     * @return object
     *
     * @throws LogicException
     */
    public function addSibling($params)
    {
        $node = new $this->nodeEntity();
        $node->setTitle($params['name']);
        $node->setStatus(false);
        $this->dm->persist($node);
        $this->dm->flush();

        return $node;
    }

    /**
     * Update parent for Node
     *
     * @param  array $params
     * @return Category $child
     *
     * @throws LogicException
     */
    public function updateParent($params)
    {
        /**
         * @var $child Category
         * @var $parent Category
         */
        $repo = $this
            ->dm
            ->getRepository($this->model);

        if ($categoryParentId = $params['parentId']) {
            $parent = $repo->find($categoryParentId);

            if (!$parent) {
                throw new LogicException('Nothing found');
            }
        }

        if ($categoryId = $params['id']) {
            $child = $repo->find($categoryId);

            if (!$child) {
                throw new LogicException('Nothing found');
            }
        }
        $child->setParent($parent);
        $this->dm->persist($child);
        $this->dm->flush();

//        // Update Parent
//        $parent->removeChild($child);
//        $parent->addChild($child);
//        $this->dm->persist($parent);
//        $this->dm->flush();

        return $child;
    }

    //---------------------------
    //--------- FRONTEND --------
    //---------------------------

//    /**
//     * validate metaUrl for Category Entity and return one we can use
//     *
//     * @param string $url
//     * @param int    $categoryId
//     *
//     * @return string
//     */
//    public function normalizeCategoryUrl($url, $categoryId = null)
//    {
//        $category = $this->dm->getRepository($this->model)->findTotalByURL($url, $categoryId);
//        $utility = new UrlUtility();
//        $validUrl = $utility->process($url);
//
//        if ($category) {
//            $validUrl = $validUrl . '-' . time();
//        }
//
//        return $validUrl;
//    }

}