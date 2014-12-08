<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Eye4web\ZfcUser\Pm\Controller;

use Eye4web\ZfcUser\Pm\Service\PmServiceInterface;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Stdlib\ResponseInterface as Response;

class PmController extends AbstractActionController
{
    protected $pmService;

    protected $newConversationForm;

    protected $newMessageForm;

    public function __construct(PmServiceInterface $pmService, $newConversationForm, $newMessageForm)
    {
        $this->pmService = $pmService;
        $this->newConversationForm = $newConversationForm;
        $this->newMessageForm = $newMessageForm;
    }

    public function indexAction()
    {
        $user = $this->ZfcUserAuthentication()->getIdentity();
        $conversations = $this->pmService->getUserConversations($user->getId());

        $viewModel = new ViewModel([
            'conversations' => $conversations
        ]);
        $viewModel->setTemplate('eye4web/zfc-user/pm/index.phtml');

        return $viewModel;
    }

    public function readConversationAction()
    {
        $form = $this->newMessageForm;
        $conversation = $this->pmService->getConversation($this->params('conversationId'));
        $messages = $this->pmService->getMessages($conversation);
        $user = $this->ZfcUserAuthentication()->getIdentity();

        $this->pmService->markRead($conversation, $user);

        $viewModel = new ViewModel([
            'conversation' => $conversation,
            'messages' => &$messages,
            'form' => $form
        ]);
        $viewModel->setTemplate('eye4web/zfc-user/pm/read-conversation.phtml');

        $redirectUrl = $this->url()->fromRoute('eye4web/zfc-user/pm/read-conversation', ['conversationId' => $conversation->getId()]);
        $prg = $this->prg($redirectUrl, true);

        if ($prg instanceof Response) {
            return $prg;
        } elseif ($prg === false) {
            return $viewModel;
        }

        $form->setData($prg);
        if (!$form->isValid()) {
            return $viewModel;
        }

        $user = $this->zfcUserAuthentication()->getIdentity();
        $message = $this->pmService->newMessage($conversation, $form->getData()['message'], $user);

        $form->get('message')->setValue('');

        $messages[] = $message;

        return $viewModel;
    }

    public function newConversationAction()
    {
        $users = $this->pmService->getUsers();
        $form = $this->newConversationForm;

        $viewModel = new ViewModel([
            'users' => $users,
            'form' => $form,
        ]);
        $viewModel->setTemplate('eye4web/zfc-user/pm/new-conversation.phtml');

        $redirectUrl = $this->url()->fromRoute('eye4web/zfc-user/pm/new-conversation');
        $prg = $this->prg($redirectUrl, true);

        if ($prg instanceof Response) {
            return $prg;
        } elseif ($prg === false) {
            return $viewModel;
        }

        $form->setData($prg);
        if (!$form->isValid()) {
            return $viewModel;
        }

        $user = $this->zfcUserAuthentication()->getIdentity();
        $this->pmService->newConversation($form->getData(), $user);

        return $this->redirect()->toRoute('eye4web/zfc-user/pm');
    }
}
