<?php

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.0 on 2012-12-12 at 13:17:14.
 */
class AmazonOrderSetTest extends PHPUnit_Framework_TestCase {

    /**
     * @var AmazonOrderSet
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        resetLog();
        $this->object = new AmazonOrderSet('testStore', null, true, null, '/var/www/athena/plugins/amazon/newAmazon/test-cases/test-config.php');
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {
        
    }
    
    public function testSetUp(){
        $obj = new AmazonOrderSet('testStore', '77');
        
        $o = $obj->getOptions();
        $this->assertArrayHasKey('AmazonOrderId.Id.1',$o);
        $this->assertEquals('77', $o['AmazonOrderId.Id.1']);
    }
    
    public function testSetOrderId(){
        $this->assertNull($this->object->setOrderIds(array('123','456')));
        $o = $this->object->getOptions();
        $this->assertArrayHasKey('AmazonOrderId.Id.1',$o);
        $this->assertEquals('123',$o['AmazonOrderId.Id.1']);
        $this->assertArrayHasKey('AmazonOrderId.Id.2',$o);
        $this->assertEquals('456',$o['AmazonOrderId.Id.2']);
        $this->assertNull($this->object->setOrderIds('123456'));
        $o2 = $this->object->getOptions();
        $this->assertArrayHasKey('AmazonOrderId.Id.1',$o2);
        $this->assertEquals('123456',$o2['AmazonOrderId.Id.1']);
        $this->assertArrayNotHasKey('AmazonOrderId.Id.2',$o2);
        $this->assertFalse($this->object->setOrderIds(array())); //won't work for this
        $this->assertFalse($this->object->setOrderIds(77)); //won't work for this
        $this->assertFalse($this->object->setOrderIds(null)); //won't work for other things
    }
    
    public function testFetchOrders(){
        resetLog();
        $this->object->setMock(true,'fetchOrder.xml');
        
        $this->assertFalse($this->object->fetchOrders()); //no order IDs set yet
        
        $this->object->setOrderIds('058-1233752-8214740');
        $this->assertNull($this->object->fetchOrders()); //now it is good
        
        $o = $this->object->getOptions();
        $this->assertEquals('GetOrder',$o['Action']);
        
        $check = parseLog();
        $this->assertEquals('Single Mock File set: fetchOrder.xml',$check[1]);
        $this->assertEquals('Order IDs must be set in order to fetch them!',$check[2]);
        $this->assertEquals('Fetched Mock File: mock/fetchOrder.xml',$check[3]);
        
        $get = $this->object->getOrders();
        $this->assertInternalType('array',$get);
        
        return $this->object;
        
    }
    
    /**
     * @depends testFetchOrders
     */
    public function testGetOrders($o){
        $get = $o->getOrders();
        $this->assertInternalType('array',$get);
        $this->assertInternalType('object',$get[0]);
        
        $this->assertFalse($this->object->getOrders()); //not fetched yet for this object
    }
    
    public function testFetchItems(){
        $this->object->setMock(true,array('fetchOrder.xml','fetchOrderItems.xml'));
        $this->object->setOrderIds('058-1233752-8214740');
        $this->object->fetchOrders();
        resetLog();
        $get = $this->object->fetchItems();
        
        $this->assertInternalType('array',$get);
        $this->assertEquals(1,count($get));
        $this->assertInternalType('object',$get[0]);
        
        $getOne = $this->object->fetchItems('string', 0); //$token will be set to false
        $this->assertInternalType('object',$getOne);
        
        $o = new AmazonOrderList('testStore', null, true);
        $this->assertFalse($o->fetchItems()); //not fetched yet for this object
    }
    
}

require_once('helperFunctions.php');