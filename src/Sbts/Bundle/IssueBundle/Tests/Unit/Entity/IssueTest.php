<?php

namespace Sbts\Bundle\IssueBundle\Tests\Unit\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Util\ClassUtils;

use Sbts\Bundle\IssueBundle\Entity\Issue;

class IssueTest extends \PHPUnit_Framework_TestCase
{
    const TEST_ID = 123;

    /**
     * @var Issue
     */
    protected $entity;

    public function setUp()
    {
        $this->entity = new Issue();
    }

    public function tearDown()
    {
        unset($this->entity);
    }

    public function testCollaborators()
    {
        $this->assertCount(0, $this->entity->getCollaborators());

        $mock = $this->getMockBuilder('Oro\Bundle\UserBundle\Entity\User')->getMock();
        $mock
            ->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(self::TEST_ID));

        $this->entity->addCollaborator($mock);
        $this->assertCount(1, $this->entity->getCollaborators());
        $this->assertEquals(self::TEST_ID, $this->entity->getCollaborators()->get(0)->getId());

        $this->entity->addCollaborator($mock);
        $this->assertCount(1, $this->entity->getCollaborators());

        $this->entity->removeCollaborator($mock);
        $this->assertCount(0, $this->entity->getCollaborators());
    }

    public function testRelated()
    {
        $mock = $this->getMockBuilder('Sbts\Bundle\IssueBundle\Entity\Issue')->getMock();
        $mock
            ->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(1));

        $this->assertCount(0, $this->entity->getRelated());
        /** @var Issue $mock */
        $this->assertEquals($this->entity, $this->entity->addRelated($mock));
        $this->assertEquals(1, $this->entity->getRelated()->get(0)->getId());

        $this->entity->removeRelated($mock);
        $this->assertCount(0, $this->entity->getRelated());
    }

    public function testChildren()
    {
        $mock = $this->getMockBuilder('Sbts\Bundle\IssueBundle\Entity\Issue')->getMock();
        $mock
            ->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(self::TEST_ID));

        $this->assertCount(0, $this->entity->getChildren());
        $this->assertEquals($this->entity, $this->entity->addChild($mock));
        $this->assertEquals(self::TEST_ID, $this->entity->getChildren()->get(0)->getId());

        $this->entity->removeChild($mock);
        $this->assertCount(0, $this->entity->getChildren());
    }

    public function testTags()
    {
        $this->assertInstanceOf('Oro\Bundle\TagBundle\Entity\Taggable', $this->entity);
        $this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $this->entity->getTags());
        $this->assertNull($this->entity->getTaggableId());

        $stub = new \ReflectionProperty(ClassUtils::getClass($this->entity), 'id');
        $stub->setAccessible(true);
        $stub->setValue($this->entity, self::TEST_ID);

        $this->assertSame(self::TEST_ID, $this->entity->getTaggableId());

        $newCollection = new ArrayCollection();
        $this->entity->setTags($newCollection);

        $this->assertSame($newCollection, $this->entity->getTags());
    }

    public function testToString()
    {
        $code = 'SBTS-1';
        $this->entity->setCode($code);

        $this->assertEquals($code, $this->entity);
    }

    public function testPreUpdateAction()
    {
        $this->entity->preUpdateAction();
        $this->assertInstanceOf('DateTime', $this->entity->getUpdatedAt());
    }

    public function testPrePersistAction()
    {
        $this->entity->prePersistAction();
        $this->assertInstanceOf('DateTime', $this->entity->getCreatedAt());
        $this->assertInstanceOf('DateTime', $this->entity->getUpdatedAt());
    }
}
