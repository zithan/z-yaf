<?php
/**
 * Created by zithan.
 * User: zithan <zithan@163.com>
 */

namespace Commons\Helper;

class Arr
{
    /**
     * 将数组生成树结构
     * @param $list
     * @param int $pid
     * @param string $parent
     * @return array
     * @author zithan
     */
    public static function generateTree($list, $pid = 0, $parent = 'pid')
    {
        $tree = array();
        foreach($list as $data) {
            if($data[$parent] == $pid) {
                $data['child'] = self::generateTree($list, $data['id']);
                $tree[] = $data;
            }
        }

        return $tree;
    }
}