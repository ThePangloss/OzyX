<?php
// src/OC/PlatformBundle/Form/AdvertEditType.php
 
namespace ozyx\PlatformBundle\Form;
 
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;


class AdvertEditType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder->remove('date')
    ;
  }

  public function getName()
  {
    return 'ozyx_platformbundle_advertedittype';
  }

  public function getParent()
  {
    return AdvertType::class;
  }
}