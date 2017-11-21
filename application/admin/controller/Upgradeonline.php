<?php
/**
 * Login.php
 * Niushop商城系统 - 团队十年电商经验汇集巨献!
 * =========================================================
 * Copy right 2015-2025 山西牛酷信息科技有限公司, 保留所有权利。
 * ----------------------------------------------
 * 官方网址: http://www.niushop.com.cn
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用。
 * 任何企业和个人不允许对程序代码以任何形式任何目的再发布。
 * =========================================================
 * @author : niuteam
 * @date : 2015.1.17
 * @version : v1.0.0.0
 */
/**
 * 后台登录控制器
 */
namespace app\admin\controller;

\think\Loader::addNamespace('data', 'data/');
use think\Controller;
use data\extend\upgrade\Upgrade as UpgradeExtend;

class Upgradeonline extends Controller
{
    /**
     * 下载压缩包
     */
    public function downloadPatchZip(){
        $download_url = request()->post('download_url', '');
        $patch_release = request()->post('version_no', '');
        $upgrade = new UpgradeExtend();
        $result=$upgrade->update_file_download($download_url, $patch_release);
        return $result;
    }
    /**
     * 更新包解压成功
     * @return multitype:number string
     */
    public function unzipPatchfile(){
        $download_zip_path = request()->post('download_zip_path', '');
        $download_file_path = request()->post('download_file_path', '');
        $download_update_file = request()->post('download_update_file', '');
        $upgrade = new UpgradeExtend();
        $result=$upgrade->update_file_unzip($download_zip_path, $download_file_path, $download_update_file);
        return $result;
    }
    /**
     * 检测升级文件的权限
     */
    public function detectUpdateFilePermission(){
        $file_start_path = request()->post('file_start_path', '');
        $upgrade = new UpgradeExtend();
        $result=$upgrade->update_file_deal($file_start_path);
        if($result["upgrade_code"]==0){
            $deal_array=json_decode($result["deal_file_array"], true);
            $persion_result=$upgrade->detect_file_permission($deal_array);
            if($persion_result["upgrade_code"]==0){
                return  $result;
            }else{
                $persion_result["deal_file_array"]=$result["deal_file_array"];
                return $persion_result;
            }
        }else{
            return $result;
        }
    }
    /**
     * 文件备份
     */
    public function filebackup(){
        $deal_file_array = request()->post('deal_file_array', '');
        $deal_file_array=json_decode($deal_file_array, true);
        $download_back_file = request()->post('download_back_file', '');
        $upgrade = new UpgradeExtend();
        $result=$upgrade->update_file_backup($deal_file_array, $download_back_file);
        return $result;
    }
    /**
     * 文件升级
     */
    public function updatefilecover(){
        $deal_file_array = request()->post('deal_file_array', '');
        $deal_file_array=json_decode($deal_file_array, true);
        $file_start_path = request()->post('file_start_path', '');
        $upgrade = new UpgradeExtend();
        $result=$upgrade->update_file_cover($deal_file_array, $file_start_path);
        return $result;
    }
    /**
     * 更新sql文件
     */
    public function updateSql(){
        $download_update_file = request()->post('download_update_file', '');
        $version_no = request()->post('version_no', '');
        $upgrade = new UpgradeExtend();
        $result=$upgrade->execute_upgrade_sql($download_update_file, $version_no);
        return $result;
    }
    /**
     * 更新失败后，还原备份文件
     * @return multitype:number string
     */
    public function upgradeRegain(){
        $download_back_file = request()->post('download_back_file', '');
        $upgrade = new UpgradeExtend();
        $result=$upgrade->update_file_regain($download_back_file);
        return $result;
    }
    
    
}
