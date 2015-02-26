<?php
namespace Oro\UserBundle\Security;

use Symfony\Component\Security\Core\Authorization\Voter\AbstractVoter;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\User\UserInterface;


class UserVoter extends AbstractVoter
{
    const CREATE = 'CREATE';
    const EDIT_ROLE = 'EDIT_ROLE';
    const MODIFY = 'MODIFY';
    const VIEW = 'VIEW';
    const VIEW_LIST = 'VIEW_LIST';

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * Return an array of supported classes. This will be called by supportsClass
     *
     * @return array an array of supported classes, i.e. array('Acme\DemoBundle\Model\Product')
     */
    protected function getSupportedClasses()
    {
        return array('Oro\UserBundle\Entity\User');
    }

    /**
     * Return an array of supported attributes. This will be called by supportsAttribute
     *
     * @return array an array of supported attributes, i.e. array('CREATE', 'READ')
     */
    protected function getSupportedAttributes()
    {
        return array(
            self::CREATE,
            self::EDIT_ROLE,
            self::MODIFY,
            self::VIEW,
            self::VIEW_LIST
        );
    }

    /**
     * Perform a single access check operation on a given attribute, object and (optionally) user
     * It is safe to assume that $attribute and $object's class pass supportsAttribute/supportsClass
     * $user can be one of the following:
     *   a UserInterface object (fully authenticated user)
     *   a string               (anonymously authenticated user)
     *
     * @param string               $attribute
     * @param object               $userEntity
     * @param UserInterface|string $user
     *
     * @return bool
     */
    protected function isGranted($attribute, $userEntity, $user = null)
    {
        if (!is_object($user)) {
            return false;
        }

        $authChecker = $this->container->get('security.authorization_checker');
        switch ($attribute) {
            case self::CREATE:
            case self::EDIT_ROLE:
                if ($authChecker->isGranted('ROLE_ADMIN')) {
                    return true;
                }
                break;
            case self::VIEW_LIST:
                if ($authChecker->isGranted(array('ROLE_ADMIN', 'ROLE_MANAGER'))) {
                    return true;
                }
                break;
            case self::VIEW:
                if ($authChecker->isGranted(array('ROLE_ADMIN', 'ROLE_MANAGER'))) {
                    return true;
                }
                if ($authChecker->isGranted(array('ROLE_USER'))
                    && $userEntity->getUsername() == $user->getUsername()) {
                    return true;
                }
                break;
            case self::MODIFY:
                if ($authChecker->isGranted(array('ROLE_ADMIN'))) {
                    return true;
                }
                if ($authChecker->isGranted(array('ROLE_USER', 'ROLE_MANAGER'))
                    && $userEntity->getUsername() == $user->getUsername()) {
                    return true;
                }
                return false;
                break;
        }

        return false;
    }
}
