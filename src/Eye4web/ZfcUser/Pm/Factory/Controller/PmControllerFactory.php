<?php

/**
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */

namespace Eye4web\ZfcUser\Pm\Factory\Controller;

use Eye4web\ZfcUser\Pm\Controller\PmController;
use Eye4web\ZfcUser\Pm\PmService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class PmControllerFactory implements FactoryInterface
{
    /**
     * Create controller
     *
     * @param  ServiceLocatorInterface $controllerManager
     * @return PmController
     */
    public function createService(ServiceLocatorInterface $controllerManager)
    {
        /**
 * @var ServiceLocatorInterface $serviceManager 
*/
        $serviceManager = $controllerManager->getServiceLocator();

        /**
 * @var PmService $pmService 
*/
        $pmService = $serviceManager->get('Eye4web\ZfcUser\Pm\Service\PmService');

        $newConversationForm = $serviceManager->get('Eye4web\ZfcUser\Pm\Form\NewConversationForm');

        $newMessageForm = $serviceManager->get('Eye4web\ZfcUser\Pm\Form\NewMessageForm');

        $deleteConversationsForm = $serviceManager->get('Eye4web\ZfcUser\Pm\Form\DeleteConversationsForm');

        $options = $serviceManager->get('Eye4web\ZfcUser\Pm\Options\ModuleOptions');

        $zfcUserModuleOptions = $serviceManager->get('zfcuser_module_options');

        $controller = new PmController($pmService, $newConversationForm, $newMessageForm, $deleteConversationsForm, $options, $zfcUserModuleOptions);

        /**
 * @var \Zend\EventManager\EventManager $eventManager 
*/
        $eventManager = $serviceManager->get('EventManager');
        $controller->setEventManager($eventManager);

        return $controller;
    }
}
