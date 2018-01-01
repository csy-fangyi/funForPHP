<?php
while (1)//循环采用3个进程
{
    //declare(ticks=1);
    $bWaitFlag = false; // 是否等待进程结束
    //$bWaitFlag = TRUE; // 是否等待进程结束
    $intNum = 3; // 进程总数
    $pids = []; // 进程PID数组
    for ($i = 0; $i < $intNum; $i++) {
        $pids[$i] = pcntl_fork();// 产生子进程，而且从当前行之下开试运行代码，而且不继承父进程的数据信息
        /*if($pids[$i])//父进程
        {
        //echo $pids[$i]."parent"."$i -> " . time(). "\n";
        }
        */
        if ($pids[$i] == -1) {
            echo "couldn't fork" . "\n";
        } else if (!$pids[$i]) {
            sleep(1);
            echo "\n" . "第" . $i . "个进程 -> " . time() . "\n";
            //$url=" 抓取页面的例子
            //$content = file_get_contents($url);
            //file_put_contents('message.txt',$content);
            //echo "\n"."第".$i."个进程 -> " ."抓取页面".$i."-> " . time()."\n";
            exit(0);//子进程要exit否则会进行递归多进程，父进程不要exit否则终止多进程
        }
        if ($bWaitFlag) {
            pcntl_waitpid($pids[$i], $status, WUNTRACED);
            echo "wait $i -> " . time() . "\n";
        }
    }
    sleep(1);
}
?>