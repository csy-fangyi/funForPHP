<?php

class Example
{
    /* config */
    const LISTEN = "tcp://192.168.2.15:5555";
    const MAXCONN = 100;
    private $pidfile;
    const uid = 80;
    const gid = 80;

    protected $pool = null;
    protected $zmq = null;

    public function __construct() {
        $this->pidfile = '/tmp/' . __CLASS__ . '.pid';
    }

    private function daemon() {
        if (file_exists($this->pidfile)) {
            echo "The file $this->pidfile exists.\n";
            exit();
        }

        $pid = pcntl_fork();
        if ($pid == -1) {
            die('could not fork');
        } else if ($pid) {
            // we are the parent
            //pcntl_wait($status); //Protect against Zombie children
            exit($pid);
        } else {
            // we are the child
            file_put_contents($this->pidfile, getmypid());
            posix_setuid(self::uid);
            posix_setgid(self::gid);
            return (getmypid());
        }
    }

    private function start() {
        $pid = $this->daemon();
    }

    private function stop() {
        if (file_exists($this->pidfile)) {
            $pid = file_get_contents($this->pidfile);
            posix_kill($pid, 9);
            unlink($this->pidfile);
        }
    }

    private function help($proc) {
        printf("%s start | stop | help \n", $proc);
    }

    public function main($argv) {
        if (count($argv) < 2) {
            printf("please input help parameter\n");
            exit();
        }
        if ($argv[1] === 'stop') {
            $this->stop();
        } else if ($argv[1] === 'start') {
            $this->start();
        } else {
            $this->help($argv[0]);
        }
    }
}

$example = new Example();
$example->main($argv);
