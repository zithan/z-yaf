<?php

class SettingModel extends BaseModel
{
    public function getSetting($name)
    {
        /*$sql = "select `value` from ".$this->table('setting')." where `name` = '".trim($name)."'";
        return $this->db->getOne($sql);*/

        return Db::name('setting')->where('name', trim($name))->value('value');
    }

    public function addOns($content)
    {
        $data = array(
            'content' => $content,
            'time' => date('Y-m-d H:i:s'),
        );

        //$this->db->insert($this->table('ons'), $data);
        return Db::name('ons')->insert($data);
    }
}
