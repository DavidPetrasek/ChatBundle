<?php

namespace FOS\ChatBundle\FormType;

use FOS\ChatBundle\DataTransformer\RecipientsDataTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Description of RecipientsType.
 *
 * @author Łukasz Pospiech <zocimek@gmail.com>
 */
class RecipientsType extends AbstractType
{
    public function __construct(private readonly RecipientsDataTransformer $recipientsTransformer)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addModelTransformer($this->recipientsTransformer);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'invalid_message' => 'The selected recipient does not exist',
        ]);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver): void
    {
        $this->configureOptions($resolver);
    }

    /**
     * {@inheritdoc}
     */
    #[\Override]
    public function getBlockPrefix()
    {
        return 'recipients_selector';
    }

    /**
     * {@inheritdoc}
     */
    #[\Override]
    public function getParent()
    {
        return TextType::class;
    }
}
