<?php

namespace App\Tests\Unit\Manager;

use App\Entity\Spot;
use App\Entity\User;
use App\Manager\SpotManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\User\UserInterface;

class SpotManagerTest extends KernelTestCase
{
    private SpotManager $manager;
    private $entityManagerMock;
    private $securityMock;

    public function setup(): void 
    {
        $this->entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $this->securityMock = $this->createMock(Security::class);

        $this->manager = new SpotManager(
            $this->entityManagerMock, 
            $this->securityMock
        );
    }

    public function testInitSpotOwner(): void
    {
        $spot = new Spot();
        $spot->setLatitude(45.421539);
        $spot->setLongitude(5.685141);
        $spot->setDescription('test');
        $spot->setIsFavorite(false);
        $spot->setId(1);
        
        $userId = $this->createMock(UserInterface::class);
        $userId
            ->expects($this->once())
            ->method('getUserIdentifier')
            ->willReturn('1');

        $this->securityMock
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($userId);

        $owner = $this->createMock(EntityRepository::class);
        $owner
            ->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($this->getUserEntity());
        
        $this->entityManagerMock
            ->expects($this->once())
            ->method('getRepository')
            ->willReturn($owner);
        
        $spotResult = $this->manager->initSpotOwner($spot);

        $expectedResult = $this->getSpotEntity();

        $this->assertEquals($expectedResult, $spotResult);
    }

    public function testInitSpotOwnerNotFoundException(): void
    {
        $spot = new Spot();
        $spot->setLatitude(45.421539);
        $spot->setLongitude(5.685141);
        $spot->setDescription('test');
        $spot->setIsFavorite(false);
        $spot->setId(1);
        
        $userId = $this->createMock(UserInterface::class);
        $userId
            ->expects($this->once())
            ->method('getUserIdentifier')
            ->willReturn('1');

        $this->securityMock
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($userId);

        $owner = $this->createMock(EntityRepository::class);
        $owner
            ->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn(null);
        
        $this->entityManagerMock
            ->expects($this->once())
            ->method('getRepository')
            ->willReturn($owner);

        $this->expectException(NotFoundHttpException::class);

        $this->manager->initSpotOwner($spot);
    }

    public function testCheckAccess(): void
    {
        $spot = $this->getSpotEntity();

        $userId = $this->createMock(UserInterface::class);
        $userId
            ->expects($this->once())
            ->method('getUserIdentifier')
            ->willReturn('1');

        $this->securityMock
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($userId);
        
        $this->manager->checkAccess($spot);

        $this->assertTrue(true);
    }

    public function testCheckAccessAccessDeniedException(): void
    {
        $spot = $this->getSpotEntity();

        $userId = $this->createMock(UserInterface::class);
        $userId
            ->expects($this->once())
            ->method('getUserIdentifier')
            ->willReturn('10');

        $this->securityMock
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($userId);
        
        $this->expectException(AccessDeniedHttpException::class);

        $this->manager->checkAccess($spot);
    }

    private function getUserEntity(): User
    {
        $user = new User();
        $user->setEmail('test@gmail.com');
        $user->setEmailVerified(false);
        $user->setPseudo('test');
        $user->setPassword('password');
        $user->setPicture(null);
        $user->setCreatedAt(new \DateTimeImmutable('2025-02-18T15:51:08+00:00'));
        $user->setUpdatedAt(new \DateTime('2025-02-18T15:53:42+00:00'));
        $user->setId(1);

        return $user;
    }

    private function getSpotEntity(): Spot
    {
        $spot = new Spot();
        $spot->setLatitude(45.421539);
        $spot->setLongitude(5.685141);
        $spot->setDescription('test');
        $spot->setIsFavorite(false);
        $spot->setOwner($this->getUserEntity());
        $spot->setId(1);

        return $spot;
    }

}
