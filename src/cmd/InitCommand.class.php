<?php

class InitCommand extends BaseCommand
{
    public function execute(array $args, array $options = array())
    {
        # init (create table)
        if ($this->createTable()){
            $this->padding("Create migrations table : ".$this->color(strtoupper("completed"),\ConsoleKit\Colors::GREEN));
        } else {
            $this->padding("Create migrations table : ".$this->color(strtoupper("already exist"),\ConsoleKit\Colors::YELLOW));
        }
    }

    /**
     * createTable
     * @return bool
     * @throws Exception
     */
    public function createTable()
    {
        global $DB;
        $this->errors = false;
        if ( !$DB->Query("SELECT 'file' FROM bim_migrations", true) ) {
            $this->errors = $DB->RunSQLBatch(__DIR__.'/../db/install/install.sql');
        } else {
            return false;
        }
        if ($this->errors !== false ) {
           throw new Exception(implode("", $this->errors));
            return false;
        }
        return true;
    }
}