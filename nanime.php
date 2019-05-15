<?php
/**
 * Created By Rinto AR
 * Nanime API :D
 * https://www.facebook.com/Con7ext
 * http://khususme.chatango.com/
 * http://rintoisback.chatango.com/
 **/
date_default_timezone_set("Asia/Jakarta");
header("Content-type: application/json");
class Grab{
    private $_SelfUrl = "http://195.154.90.38:1111/"; // https://nanime.in // Kenapa pakek ip? biar gk kenak Cloudflare ;v
    private $ch;
    private $result;
    private $request;
    private $ret;
    private $_LINK = "https://nanime.in/";
    private $_Params = array();
    private $_Arr = [];
    private $_Dat = [];
    private $_Dat2 = [];
    private $options = array(
        CURLOPT_CONNECTTIMEOUT => 120,
        CURLOPT_TIMEOUT        => 120,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_AUTOREFERER => true,
        CURLOPT_MAXREDIRS => 5,
        CURLOPT_USERAGENT => "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:40.0) Gecko/20100101 Firefox/40.0"
        );
    public function __construct(){
        $this->Params["page"] = @$_GET["page"];
        $this->Params["mirror"] = @$_GET["mirror"];
        $this->Params["download"] = @$_GET["download"];
        $this->Params["info"] = @$_GET["info"];
        $this->Params["search"] = @$_GET["search"];
    }
    public function getStr($string, $start, $end){
        $str = explode($start, $string);
        $str = explode($end, $str[1]);
        return $str[0];
    }
    public function MakeRequest($url){
        $this->ch = curl_init();
        curl_setopt($this->ch, CURLOPT_URL, $url);
        curl_setopt_array($this->ch, $this->options);
        $this->result = curl_exec($this->ch);
        curl_close($this->ch);
        return $this->result;
    }
    public function Parse($url, $regex){
        $this->request = $this->MakeRequest($url);
        preg_match_all("/$regex/", $this->request, $this->ret);
        return $this->ret;
    }
    public function Home(){
        if($this->Params["page"]){
        	$meh = $this->Parse($this->_SelfUrl."?page=".$this->Params["page"], "<div class=\"col-md-3 content-item\">\s+<a.*?href=\"(.*?)\">\s+<div class=\"poster\">\s+<img.*?src=\"(.*?)\">\s+<\/div>\s+<\/a>\s+<div.*?>(.*?)<\/div>\s+<div.*?>(.*?)<\/div>\s+<div.*?>(.*?)<\/div>");
        	for($i = 0; $i<=11; $i++){
        	    $link = $meh[1][$i];
        	    $img = $meh[2][$i];
        	    $title = $meh[3][$i];
        	    $eps = $meh[4][$i];
        	    $epss = $eps ? str_replace("Episode", "", $eps) : $eps;
        	    $stat = $meh[5][$i];
        	    $this->_Dat[] = array(
        	        "Link" => $this->_LINK.$link,
        	        "Image" => $img,
        	        "Title" => $title,
        	        "Episode" => $epss,
        	        "Status" => $stat
        	        );
        	}
        	$this->_Arr[] = array(
        	    "result" => $this->_Dat
        	    );
        	echo json_encode($this->_Arr, JSON_PRETTY_PRINT);
        }
        elseif($this->Params["info"]){
            if(preg_match("/h.*?nanime.in/", $this->Params["info"])){
                $ngeod = preg_replace("/^http.*?nanime.in/i", "http://195.154.90.38:1111", $this->Params["info"]);
                $ngeods = $this->MakeRequest($ngeod);
                $lah = $this->Parse($ngeod, "<a class=\"inline-middle.*?\" href=\"(.*?)\">(.*?)<\/a>");
                $leh = $this->Parse($ngeod, "<a href=\"(.*?)\" class=\"list-group.*?>(.*?)<\/a>");
                $des = $this->getStr($ngeods, "<p>", "</p>");
                $tit = $this->getStr($ngeods, "<li><b>Judul</b> : ", "</li>");
                $alternatif = $this->getStr($ngeods, "<li><b>Judul Alternatif</b> : ", "</li>");
                $rating = $this->getStr($ngeods, "<li><b>Rating</b> : ", "</li>");
                $votes = $this->getStr($ngeods, "<li><b>Votes</b> : ", "</li>");
                $status = $this->getStr($ngeods, "<li><b>Status</b> : ", "</li");
                $toteps = $this->getStr($ngeods, "<li><b>Total Episode</b> : ", "</li>");
                $count = count($lah[1]);
                $count = $count-1;
                $count2 = count($leh[1]);
                $count2 = $count2-1;
                for($i = 0; $i<=$count; $i++){
                    $lnk = $lah[1][$i];
                    $nma = $lah[2][$i];
                    $this->_Dat[] = array(
                        $this->_LINK.$lnk => $nma
                        );
                }
                for($i = 0; $i<=$count2; $i++){
                    $lnkk = $leh[1][$i];
                    $nmaa = $leh[2][$i];
                    $this->_Dat2[] = array(
                        $this->_LINK.$lnkk => $nmaa
                        );
                }
                $this->_Arr[] = array(
                    "title" => $tit,
                    "alternatif" => $alternatif,
                    "rating" => $rating,
                    "voting" => $votes,
                    "status" => $status,
                    "totalEps" => $toteps,
                    "descript" => $des,
                    "genre" => $this->_Dat,
                    "episode" => $this->_Dat2
                    );
                echo json_encode($this->_Arr, JSON_PRETTY_PRINT);
            }
            else{
                $this->_Arr[] = array(
                    "status" => "Something Wrong"
                    );
                echo json_encode($this->_Arr, JSON_PRETTY_PRINT);
            }
        }
        elseif($this->Params["mirror"]){
            if(preg_match("/h.*?nanime.in/", $this->Params["mirror"])){
                $ngeod = preg_replace("/^http.*?nanime.in/i", "http://195.154.90.38:1111", $this->Params["mirror"]);
                $lah = $this->Parse($ngeod, "<option value=\"h(.*?)\">(.*?)<\/option>");
                $ngeods = $this->MakeRequest($ngeod);
                $tit = $this->getStr($ngeods, '<h1 class="judul-post">Nonton ', '</h1>');
                $count = count($lah[1]);
                $count = $count-1;
                for($i = 0; $i<=$count; $i++){
                    $link = $lah[1][$i];
                    $title = $lah[2][$i];
                    $this->_Dat[] = array(
                        $link => $title
                        );
                }
                $this->_Arr[] = array(
                    "title" => $tit,
                    "player" => $this->_Dat
                    );
                echo json_encode($this->_Arr, JSON_PRETTY_PRINT);
            }
            else{
                $this->_Arr[] = array(
                    "status" => "Something Wrong"
                    );
                echo json_encode($this->_Arr, JSON_PRETTY_PRINT);
            }
        }
        elseif($this->Params["download"]){
            if(preg_match("/h.*?nanime.in/", $this->Params["download"])){
                $ngeod = preg_replace("/^http.*?nanime.in/i", "http://195.154.90.38:1111", $this->Params["download"]);
                $lah = $this->Parse($ngeod, "<a href=\"(.*?)\" target=\".*?\" class=\"btn btn-success\"><i class=\".*?<\/i>(.*?)<\/a>");
                $ngeods = $this->MakeRequest($ngeod);
                $tit = $this->getStr($ngeods, '<h1 class="judul-post">Nonton ', '</h1>');
                $count = count($lah[1]);
                $count = $count-1;
                for($i = 0; $i<=$count; $i++){
                    $link = $lah[1][$i];
                    $nma = $lah[2][$i];
                    $this->_Dat[] = array(
                        $link => $nma
                        );
                }
                $this->_Arr[] = array(
                    "title" => $tit,
                    "download" => $this->_Dat
                    );
                echo json_encode($this->_Arr, JSON_PRETTY_PRINT);
            }
            else{
                $this->_Arr[] = array(
                    "status" => "Something Wrong"
                    );
                echo json_encode($this->_Arr, JSON_PRETTY_PRINT);
            }
        }
        elseif($this->Params["search"]){
            $meh = $this->Parse($this->_SelfUrl."?s=".$this->Params["search"], "<div class=\"col-md-3 content-item\">\s+<a.*?href=\"(.*?)\">\s+<div class=\"poster\">\s+<img.*?src=\"(.*?)\">\s+<\/div>\s+<\/a>\s+<div.*?>(.*?)<\/div>\s+<div.*?>(.*?)<\/div>\s+<div.*?>(.*?)<\/div>");
        	$count = count($meh[1]);
        	$count = $count-1;
        	for($i = 0; $i<=$count; $i++){
        	    $link = $meh[1][$i];
        	    $img = $meh[2][$i];
        	    $title = $meh[3][$i];
        	    $eps = $meh[4][$i];
        	    $epss = $eps ? str_replace("Episode", "", $eps) : $eps;
        	    $stat = $meh[5][$i];
        	    $this->_Dat[] = array(
        	        "Link" => $this->_LINK.$link,
        	        "Image" => $img,
        	        "Title" => $title,
        	        "Episode" => $epss,
        	        "Status" => $stat
        	        );
        	}
        	$this->_Arr[] = array(
        	    "result" => $this->_Dat
        	    );
        	echo json_encode($this->_Arr, JSON_PRETTY_PRINT);
        }
        else{
            $bc = basename(__FILE__);
            echo "SITE: ".$_SERVER['SERVER_NAME']."\n";
            echo "FILE: ".$bc."\n";
            echo "Request: \n";
            echo "
            ?page EX: /$bc?page=1\n
            ?info EX: /$bc?info=https://nanime.in/anime/boruto-naruto-next-generations\n
            ?mirror EX: /$bc?mirror=https://nanime.in/episode/boruto-naruto-next-generations-episode-106
            ?download EX: /$bc?download=https://nanime.in/episode/boruto-naruto-next-generations-episode-106
            ?search EX: /$bc?search=naruto";
        }
    }
}
$lib = new Grab();
$lib->Home();
