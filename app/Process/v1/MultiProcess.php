<?php

namespace App\Process\v1;

class MultiProcess
{
    /**
     * run 打开多个进程运行系统命令
     * @param array $arr 系统命令
     *
     * @author KuanZhang
     * @date 2019/9/25 16:50
     */
    public static function run(array $arr)
    {
        $arrResult = array();
        $arrFp = array();
        foreach ($arr as $k => $cmd) {
            $arrFp[$k] = popen($cmd, "r");
            if (!$arrFp[$k]) {
                $arrResult[$k] = null;
                unset($arrFp[$k]);
                continue;
            }
            stream_set_blocking($arrFp[$k], false);
        }
        $write = null;
        $expect = null;
        while (count($arrFp) > 0) {
            $arrRead = array_values($arrFp);
            $ret = stream_select($arrRead, $write, $expect, 0, 200000);
            if ($ret === false) break;
            if ($ret === 0) continue;
            foreach ($arrFp as $k => $fp) {
                while (!feof($fp)) {
                    $r = fread($fp, 1024);
                    if ($r === false) break;
                    $arrResult[$k]['cmd'] = $arr[$k];
                    if (!isset($arrResult[$k]['stdout'])) $arrResult[$k]['stdout'] = $r;
                    $arrResult[$k]['stdout'] .= $r;
                    if (feof($fp)) unset($arrFp[$k]);
                }
            }
        }
    }
}