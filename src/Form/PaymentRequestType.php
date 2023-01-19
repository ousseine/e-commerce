<?php

namespace App\Form;

use App\Entity\PaymentRequest;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PaymentRequestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('created_at')
            ->add('paid_at')
            ->add('validated')
            ->add('stripSessionId')
            ->add('fromOrder')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PaymentRequest::class,
        ]);
    }
}
