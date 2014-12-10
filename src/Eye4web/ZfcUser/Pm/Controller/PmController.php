<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Eye4web\ZfcUser\Pm\Controller;

use Eye4web\ZfcUser\Pm\Form\DeleteConversationsForm;
use Eye4web\ZfcUser\Pm\Form\NewConversationForm;
use Eye4web\ZfcUser\Pm\Form\NewMessageForm;
use Eye4web\ZfcUser\Pm\Options\ModuleOptions;
use Eye4web\ZfcUser\Pm\Service\PmServiceInterface;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Stdlib\ResponseInterface as Response;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\ArrayAdapter;

class PmController extends AbstractActionController
{
    /**
     * @var PmServiceInterface
     */
    protected $pmService;

    /**
     * @var NewConversationForm
     */
    protected $newConversationForm;

    /**
     * @var NewMessageForm
     */
    protected $newMessageForm;

    /**
     * @var DeleteConversationsForm
     */
    protected $deleteConversationsForm;

    /**
     * @var ModuleOptions
     */
    protected $options;

    public function __construct(PmServiceInterface $pmService,
                                NewConversationForm $newConversationForm,
                                NewMessageForm $newMessageForm,
                                DeleteConversationsForm $deleteConversationsForm,
                                ModuleOptions $options)
    {
        $this->pmService = $pmService;
        $this->newConversationForm = $newConversationForm;
        $this->newMessageForm = $newMessageForm;
        $this->deleteConversationsForm = $deleteConversationsForm;
        $this->options = $options;
    }

    public function indexAction()
    {
        $user = $this->ZfcUserAuthentication()->getIdentity();
        $form = $this->deleteConversationsForm;
        $conversations = $this->pmService->getUserConversations($user->getId());

        // Paginator
        $paginator = new Paginator(new ArrayAdapter($conversations));
        $page = $this->params('page', 1);
        $paginator->setDefaultItemCountPerPage($this->options->getConversationsPerPage());
        $paginator->setCurrentPageNumber($page);

        $viewModel = new ViewModel([
            'conversations' => $paginator,
            'form' => $form
        ]);
        $viewModel->setTemplate('eye4web/zfc-user/pm/index.phtml');

        $redirectUrl = $this->url()->fromRoute('eye4web/zfc-user/pm/list', ['page' => $page]);
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

        $this->pmService->deleteConversations($prg['collectionIds'], $user);

        return $this->redirect()->toRoute('eye4web/zfc-user/pm/list');
    }

    public function readConversationAction()
    {
        $form = $this->newMessageForm;
        $conversation = $this->pmService->getConversation($this->params('conversationId'));
        $messages = $this->pmService->getMessages($conversation);
        $user = $this->ZfcUserAuthentication()->getIdentity();

        // Paginator
        $paginator = new Paginator(new ArrayAdapter($messages));
        $page = $this->params('page', 1);
        $paginator->setDefaultItemCountPerPage($this->options->getMessagesPerPage());
        $paginator->setCurrentPageNumber($page);

        $this->pmService->markRead($conversation, $user);

        $viewModel = new ViewModel([
            'conversation' => $conversation,
            'messages' => &$paginator,
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

        return $this->redirect()->toRoute('eye4web/zfc-user/pm/list');
    }
}
