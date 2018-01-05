<?php

require "../src/utils/ICP.php";

print_r(json_decode(ICP::queryICP('www.chuangcache.com'), true));