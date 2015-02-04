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
                    array('route' => 'oro_user_create')
                )
            ),
            'linkAttributes' => array(
                'class' => 'dropdown-toggle',
                'data-toggle' => 'dropdown',
                'role' => 'link',
                'aria-expanded' => false)
            ))
            ->setAttribute('class', 'dropdown')
            ->setChildrenAttributes(array('class' => 'dropdown-menu', 'role' => 'menu'));

        $request = $this->container->get('request');
        $voter = new RouteVoter($request);
        $matcher = new Matcher();
        $matcher->addVoter($voter);

        $menu['Users']->addChild('Users list', array('route' => 'oro_user_index'));
        $menu['Users']->addChild('User create', array('route' => 'oro_user_create'));
        //$menu['Users']->addChild('Comments', array('route' => 'oro_user_comments'));
        //$menu->addChild('Projects', array('route' => 'oro_project_index'));
        $menu->addChild('Log out', array('route' => 'logout'));

        return $menu;
    }

}
