<?php

/**
 * @file TestStorage.php
 * This file is part of Movim.
 *
 * @brief Tests the Storage module.
 *
 * @author Guillaume Pasquet <etenil@etenilsrealm.nl>
 *
 * @version 1.0
 * @date 27 April 2011
 *
 * Copyright (C)2011 Movim Project.
 *
 * %license%
 */
define('DB_DEBUG', true);
define('DB_LOGFILE', 'queries.log');

class Account extends StorageBase
{
    // Storable fields.
    protected $balance;
    protected $interest;
    protected $owners;

    protected function type_init()
    {
        $this->balance = StorageType::float();
        $this->interest = StorageType::float();
    }
}

class Owner extends StorageBase
{
    protected $name;
    protected $dob;
    protected $account;

    protected function type_init()
    {
        $this->name = StorageType::varchar(256);
        $this->dob = StorageType::date();
        $this->foreignkey('account', 'Account');
    }
}


class TestStorage
{
    function __construct()
    {
        Conf::$conf_path = "tests/php/Storage";
        $this->db_file = ut_res('tests.db');
        $this->_wipe();
    }

    private function _wipe()
    {
        if(isset($this->sdb))
            unset($this->sdb);
        if(isset($this->db))
            unset($this->db);

        unlink($this->db_file);
        $this->sdb = new StorageEngineSqlite($this->db_file);
        $this->db = new SQLite3($this->db_file);
    }

    function testCreate()
    {
        $test = new Account();
        $this->sdb->create($test);
        unset($test);

        $numtables = $this->db->querySingle(
            'SELECT count(name) as count FROM sqlite_master WHERE type="table" AND name="Account"');
        ut_equals($numtables, 1);
    }

    function testSave()
    {
        $account = new Account();
        $account->balance = 100;
        $account->interest = 0.025;
        $this->sdb->save($account);

        $count = $this->db->querySingle(
            'SELECT count(*) as count FROM Account '.
            'WHERE balance="100" AND interest="0.025"');
        ut_equals($count, 1);
    }

    function testLoad()
    {
        $account = new Account();
        $this->sdb->select($account, array('id' => 1));
        ut_equals($account->balance, 100);
        ut_equals($account->interest, 0.025);
    }

    function _testCreateLinked()
    {
        $this->_wipe();

        $owner = new Owner();
        $owner->create($this->sdb);
        $numtables = $this->db->querySingle(
            'SELECT count(name) as count FROM sqlite_master '.
            'WHERE type="table" AND (name="Owner" OR name="Account")');
        ut_equals($numtables, 2);
    }

    function __destruct()
    {
    }
}

?>
