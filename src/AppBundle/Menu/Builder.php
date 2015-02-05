<?php
namespace AppBundle\Menu;

use Knp\Menu\Matcher\Matcher;
use Knp\Menu\Matcher\Voter\RegexVoter;
use Knp\Menu\Matcher\Voter\RouteVoter;
use Knp\Menu\Renderer\ListRenderer;
use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

class Builder extends ContainerAware
{
    /**
     * Create main menu
     *
     * @param FactoryInterface $factory
     * @param array $options
     * @return \Knp\Menu\ItemInterface
     */
    public function mainMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root');

        $menu->setChildrenAttribute('class', 'nav nav-pills');
        $menu->setChildrenAttribute('role', 'tablist');

        $menu->addChild('Home', array('route' => 'dashboard'));
        $menu->addChild('Users', array(
            'label' => 'Users',
            'route' => 'oro_user_index',
            'extras' => array(
                'routes' => array(
                    array('route' => 'oro_user_create'),
                    array('route' => 'oro_user_view'),
                    array('route' => 'oro_user_update')
                )
            ),
            'linkAttributes' => array(
                'class' => 'dropdown-toggle',
                'data-toggle' => 'dropdown',
                'role' => 'link',
                'aria-expanded' => false
            )
        ))
            ->setAttribute('class', 'dropdown')
            ->setChildrenAttributes(array('class' => 'dropdown-menu', 'role' => 'menu'));

        $menu['Users']->addChild('Users list', array('route' => 'oro_user_index'));
        $menu['Users']->addChild('User create', array('route' => 'oro_user_create'));

        $menu->addChild('Projects', array(
            'route' => 'oro_project',
            'extras' => array(
                'routes' => array(
                    array('route' => 'oro_project_create'),
                    array('route' => 'oro_project_view'),
                    array('route' => 'oro_project_update')
                )
            ),
            'linkAttributes' => array(
                'class' => 'dropdown-toggle',
                'data-toggle' => 'dropdown',
                'role' => 'link',
                'aria-expanded' => false
            )
        ))
            ->setAttribute('class', 'dropdown')
            ->setChildrenAttributes(array('class' => 'dropdown-menu', 'role' => 'menu'));
        $menu['Projects']->addChild('Projects list', array('route' => 'oro_project'));
        $menu['Projects']->addChild('Create Project', array('route' => 'oro_project_create'));
        $menu->addChild('Log out', array('route' => 'logout'));

        return $menu;
    }

}
