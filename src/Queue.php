<?php
namespace Ysandreew\Queue;

use Predis\Client;

class Queue{

    public $connection;

    public $sleepTime=2;

    public $team;

    public function __construct(string $name='default',Client $connection=null)
    {
        $this->team=$name;
        $this->connection=is_null($connection)?new Client('tcp://127.0.0.1:6379'):$connection;
    }

    public function finish(Job $job)
    {
        $this->connection->lpush($this->team.'finish',serialize($job));
    }

    public function dispatch(Job $job)
    {
        $this->connection->lpush($this->team,serialize($job));
    }


    public function start()
    {
        while (true){
            $job=$this->connection->rpop($this->team);
            if(!is_null($job)){
                unserialize($job)->run();
                $this->finish($job);
            }
            else{
                sleep($this->sleepTime);
            }
        }
    }

    public function setConnection(Client $connection)
    {
        $this->connection=$connection;
        return $this;
    }

    public function setTeamName(string $name)
    {
        $this->team=$name;
        return $this;
    }


    public function setSleepTime(int $time)
    {
        $this->sleepTime=$time;
        return $this;
    }



}