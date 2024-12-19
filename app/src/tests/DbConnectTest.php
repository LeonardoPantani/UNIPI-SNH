<?php

use PHPUnit\Framework\TestCase;

require_once '../libs/utils/db/db_connect.php';

class DbConnectTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    public function testDbConnectOk()
    {
        $conn = db_connect();

        $this->assertInstanceOf(PDO::class, $conn);
    }

    public function testDbConnectFailUser()
    {   
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('[1045]');
    
        db_connect(null, "wrong");
    }

    public function testDbConnectFailPassword()
    {   
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('[1045]');
    
        db_connect(null, null, "wrong");
    }

    public function testDbConnectFailDBName()
    {   
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('[1044]');
    
        db_connect(null, null, null, "wrong");
    }

    public function testDbConnectFailHost()
    {   
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('[2002]');
    
        db_connect("wrong");
    }
}
