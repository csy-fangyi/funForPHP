<?php
require 'Scheduler.php';
require 'Task.php';
require 'SystemCall.php';

function task($max) {
    $tid = (yield getTaskId()); // <-- here's the syscall!
    for ($i = 1; $i <= $max; ++$i) {
        echo "This is task $tid iteration $i.\n";
        yield;
    }
}

function getTaskId() {
    return new SystemCall(function (Task $task, Scheduler $scheduler) {
        $task->setSendValue($task->getTaskId());
        $scheduler->schedule($task);
    });
}

$scheduler = new Scheduler;

$scheduler->newTask(task(10));
$scheduler->newTask(task(5));

$scheduler->run();
