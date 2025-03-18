<?php

namespace FOS\ChatBundle\FormHandler;

use FOS\ChatBundle\Composer\ComposerInterface;
use FOS\ChatBundle\FormModel\AbstractMessage;
use FOS\ChatBundle\Model\MessageInterface;
use FOS\ChatBundle\Model\ParticipantInterface;
use FOS\ChatBundle\Security\ParticipantProviderInterface;
use FOS\ChatBundle\Sender\SenderInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Handles messages forms, from binding request to sending the message.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
abstract class AbstractMessageFormHandler
{
    public function __construct
    (
        private RequestStack $requestStack, 
        private ComposerInterface $composer,
        private SenderInterface $sender, 
        private ParticipantProviderInterface $participantProvider
    )
    {}

    /**
     * Processes the form with the request.
     */
    public function process(Form $form) : MessageInterface|false
    {
        $request = $this->getCurrentRequest();

        if ('POST' !== $request->getMethod()) {
            return false;
        }

        $form->handleRequest($request);

        if ($form->isValid()) {
            return $this->processValidForm($form);
        }

        return false;
    }

    /**
     * Processes the valid form, sends the message.
     */
    public function processValidForm(Form $form) : MessageInterface
    {
        $message = $this->composeMessage($form->getData());
        $this->sender->send($message);

        return $message;
    }

    /**
     * Composes a message from the form data.
     */
    abstract private function composeMessage(AbstractMessage $message) : MessageInterface;

    /**
     * Gets the current authenticated user.
     */
    private function getAuthenticatedParticipant() : ParticipantInterface
    {
        return $this->participantProvider->getAuthenticatedParticipant();
    }

    /**
     * BC layer to retrieve the current request directly or from a stack.
     */
    private function getCurrentRequest() : Request
    {
        if (!$this->requestStack) {
            throw new \RuntimeException('Current request was not provided to the form handler.');
        }

        if (!$this->requestStack->getCurrentRequest() instanceof Request) {
            throw new \RuntimeException('Request stack provided to the form handler did not contains a current request.');
        }

        return $this->requestStack->getCurrentRequest();
    }
}
