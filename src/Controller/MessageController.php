<?php

namespace FOS\ChatBundle\Controller;

use FOS\ChatBundle\FormFactory\ReplyMessageFormFactory;
use FOS\ChatBundle\FormHandler\ReplyMessageFormHandler;
use FOS\ChatBundle\Provider\ProviderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;


// TODO: replace all $this->container->get... with argument dependency injection


class MessageController extends AbstractController
{
    /**
     * Displays the authenticated participant inbox.
     */
    public function inbox(ProviderInterface $provider) : Response
    {
        $threads = $provider->getInboxThreads();

        return $this->render('@FOSMessage/Message/inbox.html.twig', [
            'threads' => $threads,
        ]);
    }

    /**
     * Displays the authenticated participant messages sent.
     */
    public function sent(ProviderInterface $provider) : Response
    {
        $threads = $provider->getSentThreads();

        return $this->render('@FOSMessage/Message/sent.html.twig', [
            'threads' => $threads,
        ]);
    }

    /**
     * Displays the authenticated participant deleted threads.
     */
    public function deleted(ProviderInterface $provider) : Response
    {
        $threads = $provider->getDeletedThreads();

        return $this->render('@FOSMessage/Message/deleted.html.twig', [
            'threads' => $threads,
        ]);
    }

    /**
     * Displays a thread, also allows to reply to it.
     */
    public function thread(string $threadId, ProviderInterface $provider, ReplyMessageFormFactory $replyMessageFormFactory, ReplyMessageFormHandler $formHandler) : Response
    {
        $thread = $provider->getThread($threadId);
        $form = $replyMessageFormFactory->create($thread);

        if ($message = $formHandler->process($form)) {
            return new RedirectResponse($this->generateUrl('fos_chat_thread_view', [
                'threadId' => $message->getThread()->getId(),
            ]));
        }

        return $this->render('@FOSMessage/Message/thread.html.twig', [
            'form' => $form->createView(),
            'thread' => $thread,
        ]);
    }

    /**
     * Create a new message thread.
     */
    public function newThread() : Response
    {
        $form = $this->container->get('fos_chat.new_thread_form.factory')->create();
        $formHandler = $this->container->get('fos_chat.new_thread_form.handler');

        if ($message = $formHandler->process($form)) {
            return new RedirectResponse($this->container->get('router')->generate('fos_chat_thread_view', [
                'threadId' => $message->getThread()->getId(),
            ]));
        }

        return $this->render('@FOSMessage/Message/newThread.html.twig', [
            'form' => $form->createView(),
            'data' => $form->getData(),
        ]);
    }

    /**
     * Deletes a thread.
     */
    public function delete(string $threadId, ProviderInterface $provider) : RedirectResponse
    {
        $thread = $provider->getThread($threadId);
        $this->container->get('fos_chat.deleter')->markAsDeleted($thread);
        $this->container->get('fos_chat.thread_manager')->saveThread($thread);

        return new RedirectResponse($this->container->get('router')->generate('fos_chat_inbox'));
    }

    /**
     * Undeletes a thread.
     */
    public function undelete(string $threadId, ProviderInterface $provider) : RedirectResponse
    {
        $thread = $provider->getThread($threadId);
        $this->container->get('fos_chat.deleter')->markAsUndeleted($thread);
        $this->container->get('fos_chat.thread_manager')->saveThread($thread);

        return new RedirectResponse($this->container->get('router')->generate('fos_chat_inbox'));
    }

    /**
     * Searches for messages in the inbox and sentbox.
     */
    public function search() : Response
    {
        $query = $this->container->get('fos_chat.search_query_factory')->createFromRequest();
        $threads = $this->container->get('fos_chat.search_finder')->find($query);

        return $this->render('@FOSMessage/Message/search.html.twig', [
            'query' => $query,
            'threads' => $threads,
        ]);
    }
}
