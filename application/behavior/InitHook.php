<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
namespace app\behavior;
// 注意应用或模块的不同命名空间
\think\Loader::addNamespace('data', 'data/');
use data\model\SysAddonsModel;
use data\model\SysHooksModel;
use think\cache;
use think\hook;

class InitHook
{

    public function run(&$param = [])
    {
        if (defined('BIND_MODULE') && BIND_MODULE === 'Install')
            return;
            // 动态加入命名空间
        \think\Loader::addNamespace('addons', 'addons');
        // 获取钩子数据
        $data = cache('hooks');
        if (! $data) {
            $addons_model = new SysAddonsModel();
            $hooks_model = new SysHooksModel();
            $hooks = $hooks_model->column('addons', 'name');
            // 获取钩子的实现插件信息
            foreach ($hooks as $key => $value) {
                if ($value) {
                    $map['status'] = 1;
                    $names = explode(',', $value);
                    $map['name'] = [
                        'IN',
                        $names
                    ];
                    $data = $addons_model->where($map)->column('name', 'id');
                    if ($data) {
                        $addons = array_intersect($names, $data);
                        Hook::add($key, array_map('get_addon_class', $addons));
                    }
                }
            }
            cache('hooks', Hook::get());
        } else {
            Hook::import($data, false);
        }
    }
}
