<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Mime\MimeTypes;
use Symfony\Component\Validator\Constraints\File;
class CSVFileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $mimeTypes = new MimeTypes();
        $builder
            ->add('calls_log', FileType::class, [
                'label' => 'Upload CSV file',
                'mapped' => false,
                'required' => true,
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => $mimeTypes->getMimeTypes('csv'),
                        'mimeTypesMessage' => 'Please upload a valid CSV file'
                    ])
                ]
            ]);
    }
}