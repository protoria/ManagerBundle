<?php
namespace Igdr\Bundle\ManagerBundle\Form\Type;

use Igdr\Bundle\ManagerBundle\Model\ManagerFactoryInterface;
use Igdr\Bundle\ManagerBundle\Model\ManagerFactoryTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Entity manager type
 */
class EntityManagerType extends AbstractType implements ManagerFactoryInterface
{
    use ManagerFactoryTrait;

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $managerFactory = $this->managerFactory;
        $choiceList     = function (Options $options) use ($managerFactory) {
            $choices = $managerFactory->get($options['manager'])->order()->where()->findAll();

            $result = array();
            foreach ($choices as $choice) {
                $result[$choice->getId()] = $choice->__toString();
            }

            return $result;
        };

        $getClass = function (Options $options) use ($managerFactory) {
            return $managerFactory->get($options['manager'])->getRepositoryName();
        };

        $resolver->setDefaults(array(
            'manager' => '',
            'class'   => $getClass,
            'choice'  => $choiceList
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'entity';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'entity_manager';
    }
}
