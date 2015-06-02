<?php

namespace Custom\FchatBundle\Security\User;

use Custom\FchatBundle\Entity\User;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\EntityUserProvider;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthAwareUserProviderInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class FchatUserProvider extends EntityUserProvider implements UserProviderInterface, OAuthAwareUserProviderInterface
{
    /**
     * @var ObjectManager
     */
    protected $em;
    
    /**
     * @var string
     */
    protected $class;

    /**
     * @var ObjectRepository
     */
    protected $repository;

    /**
     * @var array
     */
    protected $properties = array(
        'identifier' => 'id',
    );

    /**
     * Constructor.
     *
     * @param ManagerRegistry $registry    Manager registry.
     * @param string          $class       User entity class to load.
     * @param array           $properties  Mapping of resource owners to properties
     * @param string          $managerName Optional name of the entity manager to use
     */

    public function __construct(ManagerRegistry $registry, $class)
    {
        $this->em         = $registry->getManager();
        $this->class      = $class;
        $this->repository = $this->em->getRepository($class);

	}

   /**
     * {@inheritdoc}
     */
    public function loadUserByUsername($facebookId)
    {
        $user = $this->repository->findOneByFacebookId($facebookId);
        if (!$user) {
            throw new UsernameNotFoundException(sprintf("User '%s' not found.", $facebookId));
        }

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
		$facebookId =  		$response->getUsername();
		$username   = 		$response->getRealName();
		$profilePicture = 	$response->getProfilePicture();
		$friendlist     =	$response->getPath('friends');
		dump($response);
		dump($profilePicture);
		dump($friendlist);
        $resourceOwnerName = $response->getResourceOwner()->getName();

		if(null === $user = $this->repository->findOneBy(array('facebookId' => $facebookId)))
		{
//			throw new \RuntimeException(sprintf("No property defined for entity for resource owner '%s'.", $resourceOwnerName));
	
			$user = new $this->class();
			$user->setFacebookId($facebookId);
			$user->setUsername($username);
			$this->em->persist($user);
			$id = $this->em->flush();

		}
 			throw new \RuntimeException(sprintf("No property defined for entity for resource owner '%s'.", $resourceOwnerName));
        return $user;
    }



}
