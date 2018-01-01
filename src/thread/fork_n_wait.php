<?php
$bWaitFlag = false;
$intNum = 3;
while (1) {
    $pids = [];
    for ($i = 0; $i < $intNum; $i++) {
        $pids[$i] = pcntl_fork();
        //父进程
        if ($pids[$i]) {
            echo $pids[$i] . " - parent" . "$i -> " . time() . PHP_EOL;
        }

        if ($pids[$i] == -1) {
            echo "couldn't fork" . PHP_EOL;
        } else if (!$pids[$i]) {
            sleep(1);
            echo "第" . $i . "个进程 -> " . time() . PHP_EOL;
            //$url=" 抓取页面的例子
            //$content = file_get_contents($url);
            //file_put_contents('message.txt',$content);
            //echo "\n"."第".$i."个进程 -> " ."抓取页面".$i."-> " . time()."\n";
            exit(0);//子进程要exit否则会进行递归多进程，父进程不要exit否则终止多进程
        }
        if ($bWaitFlag) {
            pcntl_waitpid($pids[$i], $status, WUNTRACED);
            echo "wait $i -> " . time() . PHP_EOL;
        }
    }
    sleep(1);
}
?>