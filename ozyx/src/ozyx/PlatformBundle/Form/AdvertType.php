<?php
// src/OC/PlatformBundle/Form/AdvertType.php
 
namespace ozyx\PlatformBundle\Form;
 
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
//use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
 
class AdvertType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
      ->add('title',      TextType::class)
      ->add('author',     TextType::class)
      ->add('content',    TextareaType::class)
      ->add('image',      ImageType::class)
      ->add('published',  CheckboxType::class)

      ->add('categories', EntityType::class, array(
          'class'         => 'ozyxPlatformBundle:Category',
          'choice_label'  => 'name',
          'multiple'      => true,
          'expanded'      => true,

      ))

      ->add('Enregistrer', SubmitType::class)
    ;

    $builder
      ->addEventListener(FormEvents::PRE_SET_DATA,
        function(FormEvent $event) {
         
          $advert = $event->getData();
          $form = $event->getForm();
         
          if (null === $advert) {
            return;
          }
         
          if (!$advert){
            $form->add('published', CheckboxType::class, array('required => false'));
          } else {
            $form->remove('published', CheckboxType::class);
          }
       }
      );
      /*
      //Version permettant d'ajouter et de supprimer des catégories sans passer par l'entity correspondante.
      // Voir la fin du fichier entity/CategoryType pour y trouver le javascript afférent.

      ->add('categories', CollectionType::class, array(
        'entry_type'   => CategoryType::class,
        'allow_add'    => true,
        'allow_delete' => true
      ))
      */
  }
 
  public function setDefaultOptions(OptionsResolverInterface $resolver)
  {
    $resolver->setDefaults(array(
      'data_class' => 'ozyx\PlatformBundle\Entity\Advert'
    ));
  }
 
  public function getBlockPrefix()
  {
    return 'ozyx_platformbundle_adverttype';
  }
}