<?php

namespace app\index\controller;

use think\Controller;
use think\Db;

class Index extends Controller
{
    public function index()
    {

        return view();
    }

//login
    public function login()
    {
        return view('login');
    }

    public function login_do()
    {
        $post_data = input('post.');
        $validate = validate('User');
        if (!$validate->check($post_data)) {
            $error = $validate->getError();
            $this->assign('post_data', $post_data);
            $this->assign('error', $error);
            return view('login');
        }
        $var = Db::name('user')->where('username', $post_data['username'])->where('password', $post_data['password'])->find();
        if (!is_null($var)) {
            session('user_info', $var);
            $this->success('登陆成功', 'personal');
        } else {
            $this->error('密码错误或者帐号不存在，请重新输入！');
        }

    }

//reg
    public function register()
    {

        return view('register');
    }

    public function register_do()
    {
        $post_data = input('post.');
        $post_data['created_at'] = time();
        //验证
        $validate = validate('User');
        if (!$validate->check($post_data)) {
            $error = $validate->getError();
            $this->assign('post_data', $post_data);
            $this->assign('error', $error);
            return view('register');
        }
        $rn = Db::name('user')->where('username', $post_data['username'])->find();
        if (is_null($rn)) {
            $var = Db::name('user')->insert($post_data);
        } else {
            $this->error('用户名已存在');
        }
        if ($var > 0) {
            $pdd = Db::name('user')->where('username', $post_data['username'])->find();
            $pd = array('id' => $pdd['id'], 'username' => $pdd['username'],);
            Db::name('user_information')->insert($pd);
            $this->success('注册成功', 'login');
        } else {
            $this->error('注册失败');
        }
    }

//per
    public function personal()
    {
        $post_data = session('user_info');
        $this->assign('username', $post_data['username']);
        return view('personal');
    }

    public function personal_information()
    {
        $post_data = session('user_info');
        $var = Db::name('user_information')->where('id', $post_data['id'])->find();
        $this->assign('information', $var);
        return $this->fetch('user/personal_information');
    }

//tell_all
    public function tell_stories()
    {

        return $this->fetch('user/tell_stories');
    }

    public function write_story()
    {
        $sel = input('select_name');
        $t_id = input('t_id');
        $id1 = input('id1');
        $kb = '';
        $xy = '';
        $yq = '';
        $qt = '';
        switch ($sel) {
            case '恐怖':
                $kb = 'selected';
                break;
            case '悬疑':
                $xy = 'selected';
                break;
            case '言情':
                $yq = 'selected';
                break;
            case '其他':
                $qt = 'selected';
                break;
        }
        if ($id1 == 1) {
            $art = Db::name('user_article')->where('article_id', $t_id)->find();
            $art_url = $art['article_url'];
            $art_url = iconv('UTF-8', 'GBK', $art_url);
            $fl = fopen($art_url, 'rb');
            $art_t = file($art_url);
            fclose($fl);
            $this->assign('tn', $art['article_title']);
            $this->assign('m_text1', $art_t['0']);
//            var_dump($art_t);exit();
        } elseif ($id1 == 2) {
            $art = Db::name('user_draft')->where('draft_id', $t_id)->find();
            $art_url = $art['draft_url'];
            $art_url = iconv('UTF-8', 'GBK', $art_url);
            $fl = fopen($art_url, 'rb');
            $art_t = file($art_url);
            fclose($fl);
            $this->assign('tn', $art['draft_title']);
            $this->assign('m_text1', $art_t['0']);
        }
        $this->assign('kb', $kb);
        $this->assign('xy', $xy);
        $this->assign('yq', $yq);
        $this->assign('qt', $qt);

        return $this->fetch('tell_a_story/write_story');
    }

    public function write_story_do()
    {
//        var_dump(date("Y-m-d H:i:s"));exit();
        $xxx = input('post.');
        $validate = validate('Write');
        if (!$validate->check($xxx)) {
            $error = $validate->getError();
            $this->assign('tn', $xxx['text']);
            $this->assign('m_text1', $xxx['m_text1']);
            $this->assign('error', $error);
            return view('tell_a_story/write_story');
        }
        $post_data = session('user_info');
        $style_name = $xxx['select_name'];
        $style_name1 = $style_name;
        $f_name = "temp1.txt";
        $d_name = "draft/$style_name/" . $xxx['text'] . ".txt";
        $d_name_url = $d_name;
        $u_name = "user_stories/$style_name/" . $xxx['text'] . ".txt";
        $u_name_url = $u_name;
        $d_name = iconv('UTF-8', 'GBK', $d_name);
        $u_name = iconv('UTF-8', 'GBK', $u_name);
        $style_name = iconv('UTF-8', 'GBK', $style_name);
        $this->assign('tn', $xxx['text']);
        $this->assign('m_text1', $xxx['m_text1']);
        session('write_info', $xxx);
        $kb = '';
        $xy = '';
        $yq = '';
        $qt = '';
        switch ($style_name1) {
            case '恐怖':
                $kb = 'selected';
                break;
            case '悬疑':
                $xy = 'selected';
                break;
            case '言情':
                $yq = 'selected';
                break;
            case '其他':
                $qt = 'selected';
                break;
        }
        $this->assign('kb', $kb);
        $this->assign('xy', $xy);
        $this->assign('yq', $yq);
        $this->assign('qt', $qt);
//        var_dump($kb);
//        var_dump($xy);
//        var_dump($yq);
//        var_dump($qt);
//        exit();
        if ($xxx['save'] == "保存草稿") {
            $fl = fopen($f_name, 'wb');
            fwrite($fl, $xxx['m_text1']);
            fclose($fl);
            $this->assign('tn', $xxx['text']);
            $this->assign('m_text1', $xxx['m_text1']);
//            $this->assign('select_name', $style_name);
            if (!is_dir("draft")) {
                mkdir("draft");
            }
            if (!is_dir("draft/" . $style_name)) {
                mkdir("draft/" . $style_name);
            }
            $d_n = fopen($d_name, 'wb');
            fwrite($d_n, $xxx['m_text1']);
            fclose($d_n);
            Db::name('user_draft')->insert(['style' => $style_name1, 'username' => $post_data['username'], 'draft_title' => $xxx['text'], 'draft_url' => $d_name_url, 'created_at' => date("Y-m-d H:i:s")]);
            return $this->fetch('tell_a_story/write_story');
        } elseif ($xxx['save'] == "发表") {
            if (!is_dir('user_stories')) {
                mkdir('user_stories');
            }
            if (!is_dir("user_stories/" . $style_name)) {
                mkdir("user_stories/" . $style_name);
            }
            $u_n = fopen($u_name, 'wb');
            fwrite($u_n, $xxx['m_text1']);
            fclose($u_n);
            $fl = fopen($f_name, 'wb');
            fwrite($fl, $xxx['m_text1']);
            fclose($fl);
            $art = Db::name('user_article')->insert(['style' => $style_name1, 'username' => $post_data['username'], 'article_title' => $xxx['text'], 'article_url' => $u_name_url, 'created_at' => date("Y-m-d H:i:s")]);
            if ($art) {
//                return $this->fetch('tell_a_story/next_s');
                $this->success('发表成功', 'next_s');
            } else {
                $this->error('错误');
            }
        }
    }

    public function listen_stories()
    {

        return $this->fetch('user/listen_stories');
    }

    public function wine_friends()
    {

        return $this->fetch('user/wine_friends');
    }

    public function my_stories()
    {
        $id1 = input('id1');
        $user_post = session('user_info');

        $art = Db::name('user_article')->where('username', $user_post['username'])->paginate('2');
        $dra = Db::name('user_draft')->where('username', $user_post['username'])->paginate('2');
        $page1 = $art->render();
        $page2 = $dra->render();
        $this->assign('id', $id1);
        if (is_null($id1) | $id1 == '1') {
//            $this->assign('id',$id);
            $this->assign('list', $art);
            $this->assign('page', $page1);
        } elseif ($id1 == '2') {
            $this->assign('list', $dra);
            $this->assign('page', $page2);
        }
        return $this->fetch('user/my_stories');
    }

    public function stories_wall()
    {
        $post_data = session('write_info');
        $user_post = session('user_info');
        $art = Db::name('user_article')->where('username', $user_post['username'])->paginate(2);
        $page = $art->render();
        $this->assign('tn', $post_data['text']);
        $this->assign('m_text1', $post_data['m_text1']);
        $this->assign('page', $page);
        $this->assign('list', $art);
        return $this->fetch('public_stories/stories_wall');
    }

    public function stories_wall2()
    {
        $user_post = session('user_info');
        $t_id_til = input('t_id');
//        var_dump($t_id_til);exit();
        $art = Db::name('user_article')->where('article_id', $t_id_til)->find();
        if (is_null($art)) {
            $art = Db::name('user_article')->where('article_title', $t_id_til)->find();
            if (is_null($art)) {
                $this->error('没有找到');
            }
        }

//        var_dump($art);exit();

        $art1 = Db::name('user_article')->where('username', $user_post['username'])->paginate(2);
        $un = $art['article_url'];
        $un = iconv('UTF-8', 'GBK', $un);
        $my_t = fopen($un, 'rb');
        $art2 = file($un);
        fclose($my_t);
        $c_art = '';
        foreach ($art2 as $v) {
            $c_art .= $v;
        }
        $page = $art1->render();
        $this->assign('tn', $art['article_title']);
        $this->assign('m_text1', $c_art);
        $this->assign('page', $page);
        $this->assign('list', $art1);
        return $this->fetch('public_stories/stories_wall2');
    }

    public function del()
    {
        $id = input('id');
        $id1 = input('id1');
        if ($id1 == '1') {
            $de = Db::name('user_article')->where('article_id', $id)->delete();
            if ($de) {
                $this->success('删除成功');
            } else {
                $this->error('删除失败');
            }
        } elseif ($id1 == '2') {
            $de = Db::name('user_draft')->where('draft_id', $id)->delete();
            if ($de) {
                $this->success('删除成功');
            } else {
                $this->error('删除失败');
            }
        }
    }

    public function next_s()
    {
        return $this->fetch('tell_a_story/next_s');
    }

    public function recharge()
    {
        return $this->fetch('user/recharge_food_stamps');
    }

    public function recharge_do()
    {
        $re = input('recharge_n');
        $u_info = session('user_info');
        $up_d = Db::name('user')->where('username', $u_info['username'])->find();
        $validate = validate('Pay');
        if (!$validate->check($re)) {
            $error = $validate->getError();
            $this->assign('post_data', $re);
            $this->assign('error', $error);
            return view('user/recharge_food_stamps');
        }
        $re = $re + $up_d['food'];
        $up_n = Db::name('user')->where('username', $u_info['username'])->update(['food' => $re]);

        session('user_info', $up_d);
        if ($up_n){
            $this->success('充值成功');
        }
    }
}
