<?php

namespace App\Service;

use Symfony\Component\Process\Process;

class RemoteServerIpService
{
    public function get(): string
    {
        $process = Process::fromShellCommandline('/sbin/ifconfig |grep -B1 "inet addr" |awk \'{ if ( $1 == "inet" ) { print $2 } else if ( $2 == "Link" ) { printf "%s:" ,$1 } }\' |awk -F: \'{ if ( $1 == "eth0" ) print $3 }\'');

        $process->run();

        return str_replace("\n", '',$process->getOutput());
    }
}
