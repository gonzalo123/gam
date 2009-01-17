<?php
class Gam_Comet
{
    const COMET_OK      = 0;
    const COMET_CHANGED = 1;
    const COMET_TRIES   = 20;
    const COMMET_SLEEP  = 2;

    static private function _getFilePath($id)
    {
        return GamBASEPATH . "/db/comet/{$id}.comet";
    }

    static private function _get($id)
    {
        $cometFile = self::_getFilePath($id);
        return (is_file($cometFile)) ? filemtime($cometFile) : 0;
    }

    static function run($ids, $output=false)
    {
        $_timestamp = time();
        $out = array();
        for($i=0; $i<self::COMET_TRIES; $i++) {
            foreach ($ids as $id => $timestamp) {
                if ((integer) $timestamp == 0) {
                    $timestamp = $_timestamp;
                }
                $fileTimestamp = self::_get($id);
                if ($fileTimestamp > $timestamp){
                    $out[$id] = ($output) ? $fileTimestamp : '';
                }
                clearstatcache();
            }
            if (count($out)>0) {
                return array('s' => self::COMET_CHANGED, 'k' => $out);
            }
            sleep(self::COMET_CHANGED);
        }
        return array('s' => self::COMET_OK);
    }
}