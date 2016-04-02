<?php
// src/OC/PlatformBundle/Form/UserEditType.php
 
namespace ozyx\UserBundle\Form;
 
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class UserType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
    //MODIFIER POUR : que le collection type utilise la liste d'utilisateurs envoyées en paramètres par le controleur
    //PUIS: Lors du premier envoi de ce formulaire, et donc du choix de l'utilisateur, on ne fait apparaitre que le collection tyep
    //permettant de justement de le choisir. Au deuxieme envoi, on fait disparaitre ce collection type
    // et on fait apparaitre les autres ->add du builder...
		->add('user', EntityType::class, array(
	      'class' 			=> 'UserBundle:User',
	      'choice_label'	=> 'username',
	     ))

		->add('password',   PasswordType::class)
		->add('email',    	EmailType::class)
		->add('roles', ChoiceType::class, array(
			  'expanded'=> true,
			  'multiple'=> true,
		      'choices' => array(
		      		'Admin'			=>	'ROLE_ADMIN',
		      		'Super Admin'	=>	'ROLE_SUPER_ADMIN',
		      	),
		     ))

		/*
		->add('rolesCollection', EntityType::class, array(
		        'multiple'     => true,
		        'expanded'     => true,
		        'by_reference' => false,
		        'choices'	   => array(),
		        //'choice_label' => 'roles',
		        'label' 	   => 'Roles',
		        'class'        => 'UserBundle:User',
		    ))
		->add('roles', EntityType::class, array(
	      'class' 			=> 'UserBundle:User',
	      'choice_label'	=> 'roles',
          'multiple'      => true,
          'expanded'      => true,
	     ))
		->add('roles', CollectionType::class, array(
		      'entry_type' => 'choice',
		      'entry_options' => array(
		          'label' => false, // Ajoutez cette ligne pour enlever le 0 devant le SELECT à venir...
		           'choices' => array(
		              'ROLE_ADMIN' => 'Admin',
		              'ROLE_EDITOR' => 'Editor',
		      		),
		       ),
		     ))
		*/
	    ->add('Enregistrer', SubmitType::class)
    ;
  }
 
  /**
  * @param OptionsResolver $resolver
  */
  public function setDefaultOptions(OptionsResolverInterface $resolver)
  {
	  $resolver->setDefaults(array(
	      'data_class' => 'ozyx\UserBundle\Entity\User'
	  ));
  }
  
  public function getBlockPrefix()
  {
    return 'ozyx_platformbundle_UserEdittype';
  }
}