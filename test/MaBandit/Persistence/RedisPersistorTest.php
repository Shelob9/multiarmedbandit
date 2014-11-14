<?php

namespace MaBandit\Test\Persistence;

class RedisPersistorTest extends \PHPUnit_Framework_TestCase
{

  public static function setupBeforeClass()
  {
    $factory = new \M6Web\Component\RedisMock\RedisMockFactory();
    $client = $factory->getAdapter('Predis\Client', true);
    \MaBandit\Persistence\RedisPersistor::setClient($client);
  }

  public function testSavesAndLoadsLevers()
  {
    $p = new \MaBandit\Persistence\RedisPersistor();
    $l = \MaBandit\Lever::forValue('x')->inflate(
      new \MaBandit\Persistence\PersistedLever('foo', 'x', 1, 2));
    $p->saveLever($l);
    $f = new \MaBandit\Persistence\PersistedLever('foo', 'x');
    $this->assertNotEquals($l, $f);
    $this->assertEquals($l, $p->loadLever($f));
  }

  public function testLoadsLeversForExperiment()
  {
    $p = new \MaBandit\Persistence\RedisPersistor();
    $l = \MaBandit\Lever::forValue('x')->inflate(
      new \MaBandit\Persistence\PersistedLever('foo', 'y', 1, 2));
    $l1 = \MaBandit\Lever::forValue('x')->inflate(
      new \MaBandit\Persistence\PersistedLever('bar', 'y', 3, 4));
    $p->saveLever($l);
    $p->saveLever($l1);
    $f = new \MaBandit\Persistence\PersistedLever('we', 'y');
    $actual = $p->loadLeversForExperiment($f);
    $expected = array('foo' => $l, 'bar' => $l1);
    $this->assertEquals($expected, $actual);
  }
}
