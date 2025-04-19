<?php

namespace FOS\ChatBundle\FormType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Message form type for starting a new conversation.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
class NewThreadMessageFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('recipient', TextType::class, [
                'label' => 'recipient',
                'translation_domain' => 'FOSChatBundle',
            ])
            ->add('subject', TextType::class, [
                'label' => 'subject',
                'translation_domain' => 'FOSChatBundle',
            ])
            ->add('body', TextareaType::class, [
                'label' => 'body',
                'translation_domain' => 'FOSChatBundle',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'intention' => 'message',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    #[\Override]
    public function getBlockPrefix()
    {
        return 'fos_chat_new_thread';
    }
}
