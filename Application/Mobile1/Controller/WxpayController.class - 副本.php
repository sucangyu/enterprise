<?php
/**
 * Created by PhpStorm.
 * User: Huyh
 * Date: 2017/10/26 0026
 * Time: 下午 2:16
 */
namespace Mobile\Controller;
use Think\Controller;
class WxpayController extends Controller
{
    //微信支付---领树时处理本机数据
    public function wxpay()
    {
        $pre_session = C('SESSION_PRE');
        $mid = $_SESSION[$pre_session.'user']['id'];
        $jurl = U('Index/index');
        if(!isset($_SESSION[$pre_session.'support_id'])||empty($_SESSION[$pre_session.'support_id']))
        {
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            echo "<script type='text/javascript'>alert('参数错误');window.location.href='".$jurl."';</script>";
            exit;
        }
        $support_id = $_SESSION[$pre_session.'support_id'];
        $supportM = M('Supports');
        $supportArr = $supportM->find($support_id);
        if (empty($supportArr))
        {
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            echo "<script type='text/javascript'>alert('您支付的项目不存在');window.location.href='".$jurl."';</script>";
            exit;
        }

        if ($supportArr['paystas']==1)
        {
            $jurl2 = U('User/supportlist');
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            echo "<script type='text/javascript'>alert('您要支付的项目已支付,不能重复支付');window.location.href='".$jurl2."';</script>";
            exit;
        }

        //他人代付时这里只要前面没有支付成功可以随时更新代付人id
        if(isset($_SESSION[$pre_session.'has_df_id'])&&$mid==$_SESSION[$pre_session.'has_df_id'])
        {
            if($supportArr['dfmid']!=$mid&&!M('Supports')->where('id='.$support_id)->setField('dfmid',$mid))
            {
                echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
                echo "<script type='text/javascript'>alert('代付过程中出现异常,请重新识别二维码或联系客服处理');window.location.href='".$jurl."';</script>";
                exit;
            }
        }

        //回调地址： 向数据库添加记录等等
        $payUrl = "http://sjapi.yiwo15.com/jsshamo/index.php/Mobile/Wxpay/doWxpay";
        vendor('Wxpay.JsApiPay');
        $tools = new \JsApiPay();
        $openId = $_SESSION[$pre_session.'openid'];
        $Out_trade_no = 'claim4_'.$support_id;

        $tje = $supportArr['tmoney']*100;
        //②、统一下单
        $input = new \WxPayUnifiedOrder();
        $input->SetBody("支付");
        $input->SetAttach("帮助支付信息");
        $input->SetOut_trade_no($Out_trade_no);
        $input->SetTotal_fee($tje);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        $input->SetGoods_tag("支付");
        $input->SetNotify_url($payUrl);
        $input->SetTrade_type("JSAPI");
        $input->SetOpenid($openId);
        $order = \WxPayApi::unifiedOrder($input);
        $jsApiParameters = $tools->GetJsApiParameters($order);
        $this->assign('jsApiParameters',$jsApiParameters);
        $this->assign('tmoney', $supportArr['tmoney']);
        $this->assign('sid', $supportArr['id']);
        $this->display();
    }

    //自己支付-微信支付回调后，领树后台数据处理
    public function doWxpay()
    {
        $tms = time();
        ini_set('date.timezone','Asia/Shanghai');
        error_reporting(E_ERROR);
        Vendor('Wxpay.Log1');
        Vendor('Wxpay.WxpayServerPub');
        Vendor('Wxpay.CommonUtilPub');
        Vendor('Wxpay.Config');
        //初始化日志
        $log = new \Log1();

        //使用通用通知接口
        $notify = new \WxpayServerPub();
        //存储微信的回调
        $xml = $GLOBALS['HTTP_RAW_POST_DATA'];
        $notify->saveData($xml);
        $logName='./logs/'.date('y_m_d').'.log';//log文件路径
        $log->log_result($logName,"【接收到的notify通知】:\n".$xml."\n");
        // print_r($logName);
        // die;
        //验证签名，并回应微信。
        //对后台通知交互时，如果微信收到商户的应答不是成功或超时，微信认为通知失败，
        //微信会通过一定的策略（如30分钟共8次）定期重新发起通知，
        //尽可能提高通知的成功率，但微信不保证通知最终能成功。
        if($notify->checkSign() == FALSE){
            $returnXml = $notify->returnXml();
            $log->log_result($logName,"【业务出错".$returnXml."】:\n".$xml."\n");
            $notify->setReturnParameter("return_code","FAIL");//返回状态码
            $notify->setReturnParameter("return_msg","签名失败");//返回信息
        }else{
            $notify->setReturnParameter("return_code","SUCCESS");//设置返回码
        }
        $returnXml = $notify->returnXml();
        echo $returnXml;
        if($notify->checkSign() == TRUE)
        {
            if ($notify->data["return_code"] == "FAIL") {
                //此处应该更新一下订单状态，商户自行增删操作
                $log->log_result($logName,"【通信出错】:\n".$xml."\n");
            }
            elseif($notify->data["result_code"] == "FAIL"){
                //此处应该更新一下订单状态，商户自行增删操作
                $log->log_result($logName,"【业务出错】:\n".$xml."\n");
            }
            else
            {
                $pre_session = C('SESSION_PRE');
                //建模
                $supportM = M('Supports');
                $projlistM = M('Projlist');
                $propertyM = M('Property');
                $propertylistM = M('Propertylist');
                $transM = M('Translog');
                $memberM = M('Member');
                $commentsM = M('Commentlist');

                //获取捐助id
                $support_id = intval(substr($notify->data["out_trade_no"],7));

                //校验支付信息
                $sussportArr = $supportM->find($support_id);
                if(empty($sussportArr))
                {
                    $log->log_result($logName,"【您支付的订单不存在】:\n".$xml."\n");
                    exit;
                }
                if($sussportArr['paystas']==1)
                {
                    $log->log_result($logName,"【您支付的订单已支付】:\n".$xml."\n");
                    exit;
                }
                if($sussportArr['tmoney']<=0)
                {
                    $log->log_result($logName,"【您支付的订单数额异常】:\n".$xml."\n");
                    exit;
                }

                //校验项目信息
                $proj_id = $sussportArr['proj_id'];
                if(!$proj_id)
                {
                    M()->rollback();
                    $log->log_result($logName,"【您支付的订单不存在项目id】:\n".$xml."\n");
                    exit;
                }
                $projArr = $projlistM->find($proj_id);
                if(empty($projArr)||$projArr['isapprove']!=2)
                {
                    M()->rollback();
                    $log->log_result($logName,"【您支付的订单项目不存在或已过期】:\n".$xml."\n");
                    exit;
                }
                $min_price = $projArr['salemoney'];//单个资产价格
                if($min_price<=0)
                {
                    M()->rollback();
                    $log->log_result($logName,"【您支付的订单数额异常2】:\n".$xml."\n");
                    exit;
                }

                //校验会员信息
                $memberArr = $memberM->find($sussportArr['mid']);
                if(empty($memberArr))
                {
                    M()->rollback();
                    $log->log_result($logName,"【您支付的爱心大使对象存在异常】:\n".$xml."\n");
                    exit;
                }
                //开启事务处理本地数据
                M()->startTrans();
                $lovelistM = M('Lovelist');
                //1.修改捐助表支付状态和信息
                if(!$supportM->where('id='.$support_id)->save(array('paystas' => 1,'paytime' => $tms)))
                {
                    M()->rollback();
                    $log->log_result($logName,"【您支付的订单修改支付状态信息失败】:\n".$xml."\n");
                    exit;
                }

                //2.资产流水表添加资产流水
                $saveData['kind'] = $sussportArr['paykind'];
                $saveData['support_id'] = $support_id;
                $saveData['proj_id'] = $proj_id;
                $saveData['mid'] = $sussportArr['mid'];
                $saveData['initmoney'] = $min_price;
                $saveData['inittotalmoney'] = $sussportArr['tmoney'];
                $saveData['treenum'] = $sussportArr['treenum'];
                $saveData['gettime'] = $tms;
                $saveData['regtime'] = $tms;
                if(!$propertylistM->add($saveData))
                {
                    M()->rollback();
                    $log->log_result($logName,"【您支付的订单资产流水添加时出现异常】:\n".$xml."\n");
                    exit;
                }

                //3.项目表更新总收入金额和总树木数量
                if(!$projlistM->where('id='.$proj_id)->setInc('totaltree',$sussportArr['treenum']))
                {
                    M()->rollback();
                    $log->log_result($logName,"【您支付的订单增加项目树木数量时出现异常】:\n".$xml."\n");
                    exit;
                }
                if(!$projlistM->where('id='.$proj_id)->setInc('totalmoney',$sussportArr['tmoney']))
                {
                    M()->rollback();
                    $log->log_result($logName,"【您支付的订单增加项目总收入金额时出现异常】:\n".$xml."\n");
                    exit;
                }

                //4.领树会员累加总树木数量、总捐助金额、爱心积分、爱心记录
                if(!$memberM->where('id='.$sussportArr['mid'])->setInc('totalnum',$sussportArr['treenum']))
                {
                    M()->rollback();
                    $log->log_result($logName,"【您支付的订单增加会员表累计树木数量时出现异常】:\n".$xml."\n");
                    exit;
                }

                if(!$memberM->where('id='.$sussportArr['mid'])->setInc('totalmoney',$sussportArr['tmoney']))
                {
                    M()->rollback();
                    $log->log_result($logName,"【您支付的订单增加会员表累计捐助金额时出现异常】:\n".$xml."\n");
                    exit;
                }

                $saveData3['mid'] = $sussportArr['mid'];
                $saveData3['kind'] = 1;
                $saveData3['coins'] = $sussportArr['tmoney'];
                $saveData3['precoins'] = $memberArr['coins'];
                $saveData3['memo'] = '您认领树治沙时增加了'.$sussportArr['tmoney'].'点爱心值';
                $saveData3['regtime'] = $tms;

                if(!$lovelistM->add($saveData3))
                {
                    M()->rollback();
                    $log->log_result($logName,"【增加爱心值时出错】:\n".$xml."\n");
                    exit;
                }
                unset($saveData3);

                if(!$memberM->where('id='.$sussportArr['mid'])->setInc('coins',$sussportArr['tmoney']))
                {
                    M()->rollback();
                    $log->log_result($logName,"【您支付的订单增加会员表累计爱心值时出现异常】:\n".$xml."\n");
                    exit;
                }

                //领树时还需判断该会员是否已属于会员
                if($sussportArr['kind']==4&&($memberArr['kind']==0||$memberArr['jointime']==0))
                {
                    $saveData2['kind'] = 1;
                    $saveData2['jointime'] = $tms;
                    if(!$memberM->where('id='.$sussportArr['mid'])->save($saveData2))
                    {
                        M()->rollback();
                        $log->log_result($logName,"【您支付的订单更新爱心大使等级时出现异常】:\n".$xml."\n");
                        exit;
                    }
                }
                

                //5.若当前会员存在pid 则跟新pid会员的积分---以及添加爱心记录表
                if($memberArr['pid']!=0)
                {
                    $pidMemberArr = $memberM->find($memberArr['pid']);
                    if(!empty($pidMemberArr))
                    {
                        $saveData3['mid'] = $pidMemberArr['id'];
                        $saveData3['chlid_id'] = $sussportArr['mid'];
                        $saveData3['kind'] = 3;
                        $saveData3['coins'] = $sussportArr['tmoney'];
                        $saveData3['precoins'] = $pidMemberArr['coins'];
                        $saveData3['memo'] = '您推荐的'.$memberArr['nickname'].'认领树治沙时给您增加了'.$sussportArr['tmoney'].'点爱心值';
                        $saveData3['regtime'] = $tms;

                        if(!$lovelistM->add($saveData3))
                        {
                            M()->rollback();
                            $log->log_result($logName,"【给您的推荐人增加爱心值时出错】:\n".$xml."\n");
                            exit;
                        }
                        unset($saveData3);

                        if(!$memberM->where('id='.$pidMemberArr['id'])->setInc('coins',$sussportArr['tmoney']))
                        {
                            M()->rollback();
                            $log->log_result($logName,"【您支付的订单更新推荐人总捐助金额时出现异常】:\n".$xml."\n");
                            exit;
                        }
                    }
                }

                //6.资产包增加或修改
                unset($saveData);
                $batchnum = strtotime(date('Y').'-01-01');
                $P_w['batchnum'] = $batchnum;
                $P_w['mid'] = $memberArr['id'];
				$P_w['proj_id'] = $sussportArr['proj_id'];
                $propertyArr = $propertyM->where($P_w)->select();
                if(empty($propertyArr))
                {
                    $saveData['mid'] = $memberArr['id'];
                    $saveData['batchnum'] = $batchnum;
                    $saveData['totaltree'] = $sussportArr['treenum'];
                    $saveData['regtime'] = $tms;
					$saveData['proj_id'] =$sussportArr['proj_id'];
                    if(!$propertyM->add($saveData))
                    {
                        M()->rollback();
                        $log->log_result($logName,"【您支付的订单新资产包添加时出现异常】:\n".$xml."\n");
                        exit;
                    }
                }else
                {
                    if(!$propertyM->where('id='.$propertyArr[0]['id'])->setInc('totaltree',$sussportArr['treenum']))
                    {
                        M()->rollback();
                        $log->log_result($logName,"【您支付的订单资产包更新数据时出现异常】:\n".$xml."\n");
                        exit;
                    }
                }

                //7.交易记录表添加数据
                unset($saveData);
                $saveData['mid'] = $memberArr['id'];
                $saveData['typeid'] = $sussportArr['kind'];
                $saveData['tagid'] = $sussportArr['id'];
                $saveData['paykind'] = 1;
                $saveData['tmoney'] = $sussportArr['tmoney'];
                $saveData['memo'] = '微信支付领取'.$sussportArr['treenum'].'棵葡萄树';
                if(isset($_SESSION[$pre_session.'has_df_id'])&&!empty($_SESSION[$pre_session.'has_df_id']))
                {//存在代付人时则备注信息
                    $saveData['memo'] = $_SESSION[$pre_session.'user']['nickname'].'帮您微信代付认领了'.$sussportArr['treenum'].'棵葡萄树';
                }
                if(isset($sussportArr['dlmid'])&&!empty($sussportArr['dlmid']))
                {//存在帮领人时则优先备注信息
                    $dlArr = $memberM->find($sussportArr['dlmid']);
                    $saveData['memo'] = $dlArr['nickname'].'通过'.$_SESSION[$pre_session.'user']['nickname'].'微信支付帮您认领了'.$sussportArr['treenum'].'棵葡萄树';
                }

                $saveData['regtime'] = $tms;
                if(!$transM->add($saveData))
                {
                    M()->rollback();
                    $log->log_result($logName,"【您支付的订单交易记录表添加时出现异常】:\n".$xml."\n");
                    exit;
                }


                //8.留言记录表添加数据
                unset($saveData);
                $saveData['kind'] = 1;
                $saveData['support_id'] = $support_id;
                $saveData['proj_id'] = $sussportArr['proj_id'];
                $saveData['mid'] = $sussportArr['mid'];
                $saveData['questions'] = '已成功认领'.$sussportArr['treenum'].'棵葡萄树,'.C('COMMENT_CONTENT');
                $saveData['regtime'] = $tms;
                if(!$commentsM->add($saveData))
                {
                    M()->rollback();
                    $log->log_result($logName,"【您支付的订单留言表添加时出现异常】:\n".$xml."\n");
                    exit;
                }

                unset($_SESSION[$pre_session.'support_id']);
                unset($_SESSION[$pre_session.'has_df_id']);

                M()->commit();
                $log->log_result($logName,"【支付成功】:\n".$xml."\n");
            }
        }
    }

    //微信支付---打赏时处理本机数据
    public function rewardPay()
    {
        $pre_session = C('SESSION_PRE');
        $jurl = U('Index/index');
        if(!isset($_SESSION[$pre_session.'support_id'])||empty($_SESSION[$pre_session.'support_id']))
        {
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            echo "<script type='text/javascript'>alert('参数错误');window.location.href='".$jurl."';</script>";
            exit;
        }
        $support_id = $_SESSION[$pre_session.'support_id'];
        $supportM = M('Supports');
        $supportArr = $supportM->find($support_id);
        if (empty($supportArr))
        {
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            echo "<script type='text/javascript'>alert('您打赏的项目不存在');window.location.href='".$jurl."';</script>";
            exit;
        }

        if ($supportArr['paystas']==1)
        {
            $jurl2 = U('User/supportlist');
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            echo "<script type='text/javascript'>alert('您要打赏的项目已支付,不能重复支付');window.location.href='".$jurl2."';</script>";
            exit;
        }

        //回调地址： 向数据库添加记录等等
        $payUrl = "http://sjapi.yiwo15.com/jsshamo/index.php/Mobile/Wxpay/doRewardpay";
        vendor('Wxpay.JsApiPay');
        $tools = new \JsApiPay();
        $openId = $_SESSION[$pre_session.'openid'];
        $Out_trade_no = 'claim3_'.$support_id;

        $tje = $supportArr['tmoney']*100;
        //②、统一下单
        $input = new \WxPayUnifiedOrder();
        $input->SetBody("支付");
        $input->SetAttach("帮助支付信息");
        $input->SetOut_trade_no($Out_trade_no);
        $input->SetTotal_fee($tje);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        $input->SetGoods_tag("支付");
        $input->SetNotify_url($payUrl);
        $input->SetTrade_type("JSAPI");
        $input->SetOpenid($openId);
        $order = \WxPayApi::unifiedOrder($input);
        $jsApiParameters = $tools->GetJsApiParameters($order);
        $this->assign('jsApiParameters',$jsApiParameters);
        $this->assign('tmoney', $supportArr['tmoney']);
        $this->assign('sid', $supportArr['id']);
        $this->display('wxpay');
    }

    //自己支付-微信支付回调后，打赏后台数据处理
    public function doRewardpay()
    {
        $tms = time();
        ini_set('date.timezone','Asia/Shanghai');
        error_reporting(E_ERROR);
        Vendor('Wxpay.Log1');
        Vendor('Wxpay.WxpayServerPub');
        Vendor('Wxpay.CommonUtilPub');
        Vendor('Wxpay.Config');
        //初始化日志
        $log = new \Log1();

        //使用通用通知接口
        $notify = new \WxpayServerPub();
        //存储微信的回调
        $xml = $GLOBALS['HTTP_RAW_POST_DATA'];
        $notify->saveData($xml);
        $logName='./logs/'.date('y_m_d').'.log';//log文件路径
        $log->log_result($logName,"【接收到的notify通知】:\n".$xml."\n");
        // print_r($logName);
        // die;
        //验证签名，并回应微信。
        //对后台通知交互时，如果微信收到商户的应答不是成功或超时，微信认为通知失败，
        //微信会通过一定的策略（如30分钟共8次）定期重新发起通知，
        //尽可能提高通知的成功率，但微信不保证通知最终能成功。
        if($notify->checkSign() == FALSE){
            $returnXml = $notify->returnXml();
            $log->log_result($logName,"【业务出错".$returnXml."】:\n".$xml."\n");
            $notify->setReturnParameter("return_code","FAIL");//返回状态码
            $notify->setReturnParameter("return_msg","签名失败");//返回信息
        }else{
            $notify->setReturnParameter("return_code","SUCCESS");//设置返回码
        }
        $returnXml = $notify->returnXml();
        echo $returnXml;
        if($notify->checkSign() == TRUE)
        {
            if ($notify->data["return_code"] == "FAIL") {
                //此处应该更新一下订单状态，商户自行增删操作
                $log->log_result($logName,"【通信出错】:\n".$xml."\n");
            }
            elseif($notify->data["result_code"] == "FAIL"){
                //此处应该更新一下订单状态，商户自行增删操作
                $log->log_result($logName,"【业务出错】:\n".$xml."\n");
            }
            else
            {
                $pre_session = C('SESSION_PRE');
                //建模
                $supportM = M('Supports');
                $projlistM = M('Projlist');
                $transM = M('Translog');
                $memberM = M('Member');

                //获取捐助id
                $support_id = intval(substr($notify->data["out_trade_no"],7));

                //校验支付信息
                $sussportArr = $supportM->find($support_id);
                if(empty($sussportArr))
                {
                    $log->log_result($logName,"【您支付的订单不存在】:\n".$xml."\n");
                    exit;
                }
                if($sussportArr['paystas']==1)
                {
                    $log->log_result($logName,"【您支付的订单已支付】:\n".$xml."\n");
                    exit;
                }
                if($sussportArr['tmoney']<=0)
                {
                    $log->log_result($logName,"【您支付的订单数额异常】:\n".$xml."\n");
                    exit;
                }

                //校验项目信息
                $proj_id = $sussportArr['proj_id'];
                if(!$proj_id)
                {
                    M()->rollback();
                    $log->log_result($logName,"【您支付的订单不存在项目id】:\n".$xml."\n");
                    exit;
                }
                $projArr = $projlistM->find($proj_id);
                if(empty($projArr)||$projArr['isapprove']!=2)
                {
                    M()->rollback();
                    $log->log_result($logName,"【您支付的订单项目不存在或已过期】:\n".$xml."\n");
                    exit;
                }
                //校验会员信息
                $memberArr = $memberM->find($sussportArr['mid']);
                if(empty($memberArr))
                {
                    M()->rollback();
                    $log->log_result($logName,"【您支付的爱心大使对象存在异常】:\n".$xml."\n");
                    exit;
                }

                //开启事务处理本地数据
                M()->startTrans();

                //1.修改捐助表支付状态和信息
                if(!$supportM->where('id='.$support_id)->save(array('paystas' => 1,'paytime' => $tms)))
                {
                    M()->rollback();
                    $log->log_result($logName,"【您支付的订单修改支付状态信息失败】:\n".$xml."\n");
                    exit;
                }

                //2.项目表更新总收入金额
                if(!$projlistM->where('id='.$proj_id)->setInc('totalmoney',$sussportArr['tmoney']))
                {
                    M()->rollback();
                    $log->log_result($logName,"【您支付的订单增加项目总收入金额时出现异常】:\n".$xml."\n");
                    exit;
                }

                //3.打赏会员累加总捐助金额、爱心积分
                if(!$memberM->where('id='.$sussportArr['mid'])->setInc('totalmoney',$sussportArr['tmoney']))
                {
                    M()->rollback();
                    $log->log_result($logName,"【您支付的订单增加会员表累计捐助金额时出现异常】:\n".$xml."\n");
                    exit;
                }

                $lovelistM = M('Lovelist');
                $saveData3['mid'] = $sussportArr['mid'];
                $saveData3['kind'] = 0;
                $saveData3['coins'] = $sussportArr['tmoney'];
                $saveData3['precoins'] = $memberArr['coins'];
                $saveData3['memo'] = '您打赏治沙时增加了'.$sussportArr['tmoney'].'点爱心值';
                $saveData3['regtime'] = $tms;

                if(!$lovelistM->add($saveData3))
                {
                    M()->rollback();
                    $log->log_result($logName,"【增加爱心值时出错】:\n".$xml."\n");
                    exit;
                }
                unset($saveData3);

                if(!$memberM->where('id='.$sussportArr['mid'])->setInc('coins',$sussportArr['tmoney']))
                {
                    M()->rollback();
                    $log->log_result($logName,"【您支付的订单增加会员表累计爱心值时出现异常】:\n".$xml."\n");
                    exit;
                }

                //4.若当前会员存在pid 则跟新pid会员的积分
                if($memberArr['pid']!=0)
                {
                    $pidMemberArr = $memberM->find($memberArr['pid']);
                    if(!empty($pidMemberArr))
                    {
                        $saveData3['mid'] = $pidMemberArr['id'];
                        $saveData3['chlid_id'] = $sussportArr['mid'];
                        $saveData3['kind'] = 2;
                        $saveData3['coins'] = $sussportArr['tmoney'];
                        $saveData3['precoins'] = $pidMemberArr['coins'];
                        $saveData3['memo'] = '您推荐的'.$memberArr['nickname'].'打赏治沙时给您增加了'.$sussportArr['tmoney'].'点爱心值';
                        $saveData3['regtime'] = $tms;

                        if(!$lovelistM->add($saveData3))
                        {
                            M()->rollback();
                            $log->log_result($logName,"【给您的推荐人增加爱心值时出错】:\n".$xml."\n");
                            exit;
                        }
                        unset($saveData3);
                        if(!$memberM->where('id='.$pidMemberArr['id'])->setInc('coins',$sussportArr['tmoney']))
                        {
                            M()->rollback();
                            $log->log_result($logName,"【您支付的订单更新推荐人总捐助金额时出现异常】:\n".$xml."\n");
                            exit;
                        }
                    }
                }

                //5.交易记录表添加数据
                unset($saveData);
                $saveData['mid'] = $memberArr['id'];
                $saveData['typeid'] = $sussportArr['kind'];
                $saveData['tagid'] = $sussportArr['id'];
                $saveData['paykind'] = 1;
                $saveData['tmoney'] = $sussportArr['tmoney'];
                $saveData['memo'] = '微信支付打赏'.$sussportArr['tmoney'].'元用于治沙';
                $saveData['regtime'] = $tms;
                if(!$transM->add($saveData))
                {
                    M()->rollback();
                    $log->log_result($logName,"【您支付的订单交易记录表添加时出现异常】:\n".$xml."\n");
                    exit;
                }
                unset($_SESSION[$pre_session.'support_id']);

                M()->commit();
                $log->log_result($logName,"【支付成功】:\n".$xml."\n");
            }
        }
    }

    //取消支付
    public function cancelPay()
    {
        $jurl = U('Index/index');
        if(isset($_REQUEST['jj'])&&$_REQUEST['jj']==1)
        {
            $jurl = U('User/supportList');
        }
        if(!isset($_REQUEST['id'])||empty($_REQUEST['id']))
        {
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            echo "<script type='text/javascript'>alert('参数错误');window.location.href='".$jurl."';</script>";
            exit;
        }

        $id = $_REQUEST['id'];
        $supportM = M('Supports');
        $supportArr = $supportM->find($id);
        if(!empty($supportArr))
        {
            M()->startTrans();
            $commentArr = M('Commentlist')->where('support_id='.$id)->select();
            $res1 = 1;
            if(!empty($commentArr))
            {
                $res1 = M('Commentlist')->where('support_id='.$id)->delete();
            }
            if($supportM->delete($id)&&$res1)
            {
                M()->commit();
                echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
                echo "<script type='text/javascript'>alert('取消成功');window.location.href='".$jurl."';</script>";
                exit;
            }else
            {
                M()->rollback();
                $jurl = U('User/supportlist');
                echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
                echo "<script type='text/javascript'>alert('取消失败,稍后重试');window.location.href='".$jurl."';</script>";
                exit;
            }
        }else
        {
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            echo "<script type='text/javascript'>alert('该支付项已取消');window.location.href='".$jurl."';</script>";
            exit;
        }
    }

    //继续微信支付
    public function wxpayAgain()
    {
        if(!isset($_GET['support_id'])||empty($_GET['support_id']))
        {
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            echo "<script type='text/javascript'> alert('非法访问,缺少必要参数');window.location.href='".U('User/supportList')."';</script>";
            exit;
        }
        $support_id = intval($_GET['support_id']);
        $supportM = M('Supports');
        $supportArr = $supportM->find($support_id);
        if (empty($supportArr)||$supportArr['isdel']==1||$supportArr['paystas']==1)
        {
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            echo "<script type='text/javascript'>alert('您支付的项目不需要再支付了。。。');window.location.href='".U('User/supportList')."';</script>";
            exit;
        }
        $pre_session = C('SESSION_PRE');
        $jurl = "http://sjapi.yiwo15.com/jsshamo/index.php/Mobile/Wxpay/wxpay";
        session($pre_session.'support_id',$support_id);
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
        echo "<script type='text/javascript'>window.location.href='".$jurl."';</script>";
        exit;
    }

    //微信支付---粮仓商品/收成领取支付时本机数据
    public function goodsPay()
    {
        $pre_session = C('SESSION_PRE');
        $mid = $_SESSION[$pre_session.'user']['id'];
        $jurl = U('Index/index');
        if(!isset($_SESSION[$pre_session])||empty($_SESSION[$pre_session]))
        {
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            echo "<script type='text/javascript'>alert('参数错误');window.location.href='".$jurl."';</script>";
            exit;
        }
        $order_id = $_SESSION[$pre_session];
        $orderlistM = M('Orderlist');
        $orderArr = $orderlistM->find($order_id);
        if (empty($orderArr))
        {
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            echo "<script type='text/javascript'>alert('您支付的订单不存在');window.location.href='".$jurl."';</script>";
            exit;
        }

        if ($orderArr['pay_status']==1)
        {
            $jurl2 = U('User/supportlist');
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            echo "<script type='text/javascript'>alert('您要支付的订单已支付,不能重复支付');window.location.href='".$jurl2."';</script>";
            exit;
        }

        // //他人代付时这里只要前面没有支付成功可以随时更新代付人id
        // if(isset($_SESSION[$pre_session.'has_df_id'])&&$mid==$_SESSION[$pre_session.'has_df_id'])
        // {
        //     if($supportArr['dfmid']!=$mid&&!M('Supports')->where('id='.$support_id)->setField('dfmid',$mid))
        //     {
        //         echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
        //         echo "<script type='text/javascript'>alert('代付过程中出现异常,请重新识别二维码或联系客服处理');window.location.href='".$jurl."';</script>";
        //         exit;
        //     }
        // }

        //回调地址： 向数据库添加记录等等
        $payUrl = "http://sjapi.yiwo15.com/jsshamo/index.php/Mobile/Wxpay/doGoodsPay";
        vendor('Wxpay.JsApiPay');
        $tools = new \JsApiPay();
        $openId = $_SESSION[$pre_session.'openid'];
        $Out_trade_no = 'c4'.$order_id;
//        $Out_trade_no = $orderArr['order_sn'];
        $tje = $orderArr['total_amount']*100;
        //②、统一下单
        $input = new \WxPayUnifiedOrder();
        $input->SetBody("pay");
        $input->SetAttach("payinfo");
        $input->SetOut_trade_no($Out_trade_no);
        $input->SetTotal_fee($tje);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        $input->SetGoods_tag("pay");
        $input->SetNotify_url($payUrl);
        $input->SetTrade_type("JSAPI");
        $input->SetOpenid($openId);
        $order = \WxPayApi::unifiedOrder($input);
        $jsApiParameters = $tools->GetJsApiParameters($order);

        $this->assign('jsApiParameters',$jsApiParameters);
        $this->assign('tmoney', $orderArr['order_amount']);
        $this->assign('sid', $orderArr['id']);
        $this->display();
    }

    //立即支付-微信支付回调后，商品后台数据处理
    public function doGoodsPay()
    {
        $tms = time();
        ini_set('date.timezone','Asia/Shanghai');
        error_reporting(E_ERROR);
        Vendor('Wxpay.Log1');
        Vendor('Wxpay.WxpayServerPub');
        Vendor('Wxpay.CommonUtilPub');
        Vendor('Wxpay.Config');
        //初始化日志
        $log = new \Log1();

        //使用通用通知接口
        $notify = new \WxpayServerPub();
        //存储微信的回调
        $xml = $GLOBALS['HTTP_RAW_POST_DATA'];
        $notify->saveData($xml);
        $logName='./logs/'.date('y_m_d').'.log';//log文件路径
        $log->log_result($logName,"【接收到的notify通知】:\n".$xml."\n");
        // print_r($logName);
        // die;
        //验证签名，并回应微信。
        //对后台通知交互时，如果微信收到商户的应答不是成功或超时，微信认为通知失败，
        //微信会通过一定的策略（如30分钟共8次）定期重新发起通知，
        //尽可能提高通知的成功率，但微信不保证通知最终能成功。
        if($notify->checkSign() == FALSE){
            $returnXml = $notify->returnXml();
            $log->log_result($logName,"【业务出错".$returnXml."】:\n".$xml."\n");
            $notify->setReturnParameter("return_code","FAIL");//返回状态码
            $notify->setReturnParameter("return_msg","签名失败");//返回信息
        }else{
            $notify->setReturnParameter("return_code","SUCCESS");//设置返回码
        }
        $returnXml = $notify->returnXml();
        echo $returnXml;
        if($notify->checkSign() == TRUE)
        {
            if ($notify->data["return_code"] == "FAIL") {
                //此处应该更新一下订单状态，商户自行增删操作
                $log->log_result($logName,"【通信出错】:\n".$xml."\n");
            }
            elseif($notify->data["result_code"] == "FAIL"){
                //此处应该更新一下订单状态，商户自行增删操作
                $log->log_result($logName,"【业务出错】:\n".$xml."\n");
            }
            else
            {
                $pre_session = C('SESSION_PRE');
                //建模
                $orderlistM = M('Orderlist');
                $ordergoodsM = M('Ordergoods');
                $goodsM = M('Goods');
                $propertyM = M('Property');
                $propertytakeM = M('Propertytake');
                $transM = M('Translog');

                //获取订单id
                $order_id = intval(substr($notify->data["out_trade_no"],2));
//                $w['order_sn'] = $notify->data["out_trade_no"];
//                $order_id = $notify->data["out_trade_no"];
//                $order_id = $orderlistM->where($w)->getField('id');

                //$order_id = 102;
                //校验支付信息
                $orderArr = $orderlistM->find($order_id);
                //var_dump($orderArr);
                if(empty($orderArr))
                {
                    $log->log_result($logName,"【您支付的订单不存在】:\n".$xml."\n");
                    exit;
                }
                if($orderArr['pay_status']==1)
                {
                    $log->log_result($logName,"【您支付的订单已支付】:\n".$xml."\n");
                    exit;
                }
                if($orderArr['total_amount']<=0)
                {
                    $log->log_result($logName,"【您支付的订单数额异常】:\n".$xml."\n");
                    exit;
                }
                $order_status = 1;//订单状态
                if ($orderArr['shipping_name']=='自提') {
                    $order_status = 4;
                }
                //开启事务处理本地数据
                M()->startTrans();
                //1.修改订单支付支付状态和信息
                if(!$orderlistM->where('id='.$order_id)->save(array('order_status' => $order_status,'paykind' => 1,'pay_status' => 1,'pay_time' => $tms)))
                {
                    M()->rollback();
                    $log->log_result($logName,"【您支付的订单修改支付状态信息失败】:\n".$xml."\n");
                    exit;
                }
                //2.当订单为普通商品时修改商品库存和销量和信息
                if ($orderArr['kind']==1) {
                    $ogArr = $ordergoodsM->where(array('order_id'=>$order_id))->select();
                    //var_dump($ogArr);
                    for ($i=0; $i < count($ogArr); $i++) { 
                        $goodsArr = $goodsM->find($ogArr[$i]['goods_id']);
                        if($goodsArr['nums']<$ogArr[$i]['goods_num'])
                        {
                            echo 'sl';
                            $log->log_result($logName,"【您支付的订单商品购买数量异常】:\n".$xml."\n");
                            exit;
                        }
                        $nums = $goodsArr['nums'] - $ogArr[$i]['goods_num'];
                        $salenum = $goodsArr['salenum'] + $ogArr[$i]['goods_num'];
                        //var_dump($ogArr[$i]['goods_id']);
                        if(!$goodsM->where('id='.$ogArr[$i]['goods_id'])->save(array('nums' => $nums,'salenum' => $salenum)))
                        {
                            M()->rollback();
                            $log->log_result($logName,"【您支付的订单修改支付状态信息失败】:\n".$xml."\n");
                            exit;
                        }
                    }
                }
                //3.当订单为收成领取时添加资产变更表
                if ($orderArr['kind']==2) {
                    $ogArr = $ordergoodsM->where(array('order_id'=>$order_id))->select();
                    //var_dump($ogArr);
                    for ($i=0; $i < count($ogArr); $i++) { 
                        $propertyArr = $propertyM->find($ogArr[$i]['property_id']);
                        if(empty($propertyArr))
                        {
                            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
                            echo "<script type='text/javascript'>alert('您没有可领取的收成');window.location.href='".U('Index/index')."';</script>";
                            exit;
                        }
                        unset($protakeData);
                        $protakeData['isself'] = 1;
                        $protakeData['mid'] = $orderArr['mid'];
                        $protakeData['property_id'] = $ogArr[$i]['property_id'];
                        $protakeData['batchnum'] = $ogArr[$i]['batchnum'];
                        $protakeData['goods_id'] = $ogArr[$i]['goods_id'];
                        $protakeData['order_id'] = $order_id;
                        $protakeData['nums'] = $ogArr[$i]['needtreenum'];
                        $protakeData['regtime'] = $tms;
                        //var_dump($ogArr[$i]['goods_id']);
                        if(!$propertytakeM->add($protakeData))
                        {
                            M()->rollback();
                            $log->log_result($logName,"【您支付的订单修改支付状态信息失败】:\n".$xml."\n");
                            exit;
                        }
                    }
                }
                //4.交易记录表添加数据
                unset($saveData);
                $saveData['mid'] = $orderArr['mid'];
                $saveData['typeid'] = $orderArr['kind'];
                $saveData['tagid'] = $orderArr['id'];
                $saveData['paykind'] = 1;
                $saveData['tmoney'] = $orderArr['total_amount'];
                $saveData['memo'] = $orderArr['user_note'];
                $saveData['regtime'] = $tms;
                if(!$transM->add($saveData))
                {
                    M()->rollback();
                    $log->log_result($logName,"【您支付的订单交易记录表添加时出现异常】:\n".$xml."\n");
                    exit;
                }
                unset($_SESSION[$pre_session]);
                unset($_SESSION[$pre_session.'has_df_id']);

                M()->commit();
                $log->log_result($logName,"【支付成功】:\n".$xml."\n");
            }
        }
    }



    //取消商品支付
    public function delPay()
    {
        $jurl = U('Index/index');
        if(!isset($_REQUEST['id'])||empty($_REQUEST['id']))
        {
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            echo "<script type='text/javascript'>alert('参数错误');window.location.href='".$jurl."';</script>";
            exit;
        }

        $id = $_REQUEST['id'];
        $orderlistM = M('Orderlist');
        $orderlistArr = $orderlistM->find($id);
        if($orderlistArr['order_status']!=5)
        {
            M()->startTrans();
            
            if($orderlistM->where('id='.$id)->save(array('order_status' => 5)))
            {
                M()->commit();
                echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
                echo "<script type='text/javascript'>alert('取消成功');window.location.href='".$jurl."';</script>";
                exit;
            }else
            {
                M()->rollback();
                $jurl = U('User/supportlist');
                echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
                echo "<script type='text/javascript'>alert('取消失败,稍后重试');window.location.href='".$jurl."';</script>";
                exit;
            }
        }else
        {
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            echo "<script type='text/javascript'>alert('该支付项已取消');window.location.href='".$jurl."';</script>";
            exit;
        }
    }

    //粮仓商品继续微信支付
    public function goodsPayAgain()
    {
        if(!isset($_GET['order_id'])||empty($_GET['order_id']))
        {
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            echo "<script type='text/javascript'> alert('非法访问,缺少必要参数11');window.location.href='".U('Orderlist/index')."';</script>";
            exit;
        }
        $order_id = intval($_GET['order_id']);
        $orderlistM = M('Orderlist');
        $orderlistArr = $orderlistM->find($order_id);
        if (empty($orderlistArr)||$orderlistArr['order_status']==5||$orderlistArr['paykind']==1)
        {
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            echo "<script type='text/javascript'>alert('您支付的商品不需要再支付了。。。');window.location.href='".U('Orderlist/index')."';</script>";
            exit;
        }
        $pre_session = C('SESSION_PRE');
        $jurl = "http://sjapi.yiwo15.com/jsshamo/index.php/Mobile/Wxpay/goodsPay";
        //$jurl = "http://localhost/jsshamo/index.php/Mobile/Goodspay/wxpay";
        session($pre_session,$order_id);
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
        echo "<script type='text/javascript'>window.location.href='".$jurl."';</script>";
        exit;
    }

    //他人代付入口--使用中
    public function dfpay()
    {
        $pre_session = C('SESSION_PRE');
        if(!isset($_SESSION[$pre_session.'openid'])||empty($_SESSION[$pre_session.'openid']))
        {
            vendor('Wxpay.JsApiPay');
            //①、获取用户openid
            $tools = new \JsApiPay();
            $openIdArr = $tools->GetOpenid();
            var_dump($openIdArr);exit;
            $openId = $openIdArr['openid'];
            session($pre_session.'openid',$openIdArr['openid']);
        }else
        {
            $openId = $_SESSION[$pre_session.'openid'];
            //var_dump($openId);
        }
        $w['openid'] = $openId;
        $payMember = M('Member')->where($w)->find();
        if(empty($payMember))
        {
            $data = array(
                'openid'=>$openId,//支付宝用户号
                'regtime'=>time(),
            );
            $user_id = M('Member')->add($data);
        }

        $jurl = U('Index/index');
        if(!isset($_REQUEST['support_id'])||empty($_REQUEST['support_id']))
        {
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            echo "<script type='text/javascript'>alert('参数错误');window.location.href='".$jurl."';</script>";
            exit;
        }
        $support_id = $_REQUEST['support_id'];
        $supportM = D('Supports');
        $supportArr = $supportM->relation(true)->find($support_id);
        if (empty($supportArr))
        {
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            echo "<script type='text/javascript'>alert('您要支付的项目不存在');window.location.href='".$jurl."';</script>";
            exit;
        }

        if ($supportArr['paystas']==1)
        {
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            echo "<script type='text/javascript'>alert('您要支付的项目已支付,不能重复支付');window.location.href='".$jurl."';</script>";
            exit;
        }

        if(IS_POST)
        {
            if((!isset($_POST['support_id'])||empty($_POST['support_id'])))
            {
                echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
                echo "<script type='text/javascript'> alert('非法请求');window.location.href='".$jurl."';</script>";
                exit;
            }
            $jurl = "http://sjapi.yiwo15.com/jsshamo/index.php/Mobile/Wxpay/wxpay";
            session($pre_session.'support_id',$_POST['support_id']);
            session($pre_session.'has_df_id',$_SESSION[$pre_session.'user']['id']);
            unset($_POST);
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            echo "<script type='text/javascript'>window.location.href='".$jurl."';</script>";
            exit;
        }else
        {
            //代付申请人信息
            $dfMember  = M('Member')->find($supportArr['mid']);
            if($supportArr['dlmid'])
            {//以dlmid存在的人优先显示
                $dfMember  = M('Member')->find($supportArr['dlmid']);
            }
            if(empty($dfMember))
            {
                echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
                echo "<script type='text/javascript'>alert('代付申请人信息不存在,请确认后重试');window.location.href='".$jurl."';</script>";
                exit;
            }

            if($supportArr['mid']==$_SESSION[$pre_session.'user']['id'])
            {
                echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
                echo "<script type='text/javascript'> alert('您正在给您自己代付');</script>";
            }
            $this->dfMember = $dfMember;

            if(!isset($supportArr['Projlist']['projimages'])&&!empty($supportArr['Projlist']['projimages']))
            {
                $supportArr['Projlist']['projimages'] = json_decode($supportArr['Projlist']['projimages']);
                $supportArr['Projlist']['projimages'] = $supportArr['Projlist']['projimages'][0];
            }
            $this->supportArr = $supportArr;
            $this->display();
        }
    }


}
