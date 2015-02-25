<?php
namespace Oro\IssueBundle\Security;

use Symfony\Component\Security\Core\Authorization\Voter\AbstractVoter;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\User\UserInterface;


class CommentVoter extends AbstractVoter
{
    const DELETE = 'DELETE';
    const EDIT = 'EDIT';

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
        return array('Oro\IssueBundle\Entity\IssueComment');
    }

    /**
     * Return an array of supported attributes. This will be called by supportsAttribute
     *
     * @return array an array of supported attributes, i.e. array('CREATE', 'READ')
     */
    protected function getSupportedAttributes()
    {
        return array(
            self::DELETE,
            self::EDIT,
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
     * @param object               $comment
     * @param UserInterface|string $user
     *
     * @return bool
     */
    protected function isGranted($attribute, $comment, $user = null)
    {
        if (!is_object($user)) {
            return false;
        }

        $authChecker = $this->container->get('security.authorization_checker');
        switch ($attribute) {
            case self::EDIT:
            case self::DELETE:
                if ($authChecker->isGranted('ROLE_ADMIN')) {
                    return true;
                }
                if ($comment->getUser()->getUsername() == $user->getUsername()) {
                    return true;
                }
                return false;
        }

        return false;
    }
}
