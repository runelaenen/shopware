<?php
/**
 * Shopware 5
 * Copyright (c) shopware AG
 *
 * According to our dual licensing model, this program can be used either
 * under the terms of the GNU Affero General Public License, version 3,
 * or under a proprietary license.
 *
 * The texts of the GNU Affero General Public License with an additional
 * permission and of our proprietary license can be found at and
 * in the LICENSE file you have received along with this program.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * "Shopware" is a registered trademark of shopware AG.
 * The licensing of the program under the AGPLv3 does not imply a
 * trademark license. Therefore any rights, title and interest in
 * our trademarks remain entirely with us.
 */

namespace Shopware\Bundle\ESIndexingBundle\Subscriber;

use Shopware\Components\Plugin\SubscriberInterface;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;

class ORMBacklogSaveSubscriber implements SubscriberInterface
{
    /**
     * @var ORMBacklogSubscriber
     */
    private $backlog;

    /**
     * @var Container
     */
    private $container;

    /**
     * @param ORMBacklogSubscriber $backlog
     * @param Container            $container
     */
    public function __construct(
        ORMBacklogSubscriber $backlog,
        Container $container
    ) {
        $this->backlog = $backlog;
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Controller_Front_DispatchLoopShutdown' => 'onTerminate',
            'Shopware_Command_After_Run' => 'onTerminate',
        ];
    }

    public function onTerminate()
    {
        if (!$this->container->getParameter('shopware.elasticsearch.enabled')) {
            return;
        }
        if (!$this->container->getParameter('shopware.elasticsearch.write_backlog')) {
            return;
        }

        $this->backlog->processQueue();
    }
}
