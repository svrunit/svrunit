<?php


class TestSuiteTest extends \PHPUnit\Framework\TestCase
{

    /**
     * @test
     * @author Christian Dangl
     */
    public function testName()
    {
        $suite = new TestSuite('my-test');

        $this->assertEquals('my-test', $suite->getName());
    }


    /**
     * @test
     * @author Christian Dangl
     */
    public function testTypeLocal()
    {
        $suite = new TestSuite('my-test');
        $suite->setDockerImage('');
        $suite->setDockerContainer('');
        $suite->setDockerEntrypoint('');

        $this->assertEquals(TestSuite::TYPE_LOCAL, $suite->getType());
    }

    /**
     * @test
     * @author Christian Dangl
     */
    public function testTypeDockerImage()
    {
        $suite = new TestSuite('my-test');
        $suite->setDockerImage('abc');

        $this->assertEquals(TestSuite::TYPE_DOCKER_IMAGE, $suite->getType());
    }

    /**
     * @test
     * @author Christian Dangl
     */
    public function testTypeDockerContainer()
    {
        $suite = new TestSuite('my-test');
        $suite->setDockerContainer('abc');

        $this->assertEquals(TestSuite::TYPE_DOCKER_CONTAINER, $suite->getType());
    }

}