<?php
/**
 * Created By Rinto AR
 * Nanime API :D
 * https://www.facebook.com/Con7ext
 * http://khususme.chatango.com/
 * http://rintoisback.chatango.com/
 * --- RINTOD ---
 **/
date_default_timezone_set("Asia/Jakarta");
header("Content-type: application/json");
class GrabMeow{
	private $_selfURL = "https://meownime.com/";
	private $_Author = "Rinto AR";
	private $_MSG = [];
	private $_Params = array();
	private $_Arr = [];
	private $_Data = [];
	private $_Data2 = [];
	private $_Ch;
	private $_ERROR = [];
	private $_Options = array(
		CURLOPT_CONNECTTIMEOUT => 120,
		CURLOPT_TIMEOUT        => 120,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_SSL_VERIFYHOST => false,
		CURLOPT_SSL_VERIFYPEER => false,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_AUTOREFERER => true,
		CURLOPT_MAXREDIRS => 5,
		CURLOPT_USERAGENT => "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:40.0) Gecko/20100101 Firefox/40.0");
	public function __construct(){
		$this->_Params["page"] = @$_GET["page"];
		$this->_Params["info"] = @$_GET["info"];
		$this->_Params["download"] = @$_GET["download"];
		$this->_Params["episode"] = @$_GET["episode"];
		$this->_Params["type"] = @$_GET["type"];
		$this->_Params["search"] = @$_GET["search"];
		$this->_ERROR["error"] = "Something Wrong";
		$this->_ERROR["type"] = "Please put type parameter";
		$this->_ERROR["null"] = "Please put value :D";
		$this->_MSG[] = array(
			"author" => $this->_Author,
			"contact" => array(
				"facebook" => "fb.me/con7ext",
				"chatango" => array(
					"rintoisback.chatango.com",
					"khususme.chatango.com"
					),
				)
			);
	}
	public function getStr($str, $start, $end){
		$string = explode($start, $str);
		$string = explode($end, $string[1]);
		return $string[0];
	}
	public function MakeRequest($url){
		$this->_Ch = curl_init();
		curl_setopt($this->_Ch, CURLOPT_URL, $url);
		curl_setopt_array($this->_Ch, $this->_Options);
		$result = curl_exec($this->_Ch);
		return $result;
	}
	public function Pars($url, $reg){
		$req = $this->MakeRequest($url);
		preg_match_all("/$reg/", $req, $res);
		return $res;
	}
	public function Home(){
		if($this->_Params["page"]){
			if(empty($this->_Params["type"])){
				echo $this->_ERROR["type"] . " EX: file.php?page=1&type=ongoing / movie / completed :D";
			}
			else{
				$type = "https://meownime.com/tag/".$this->_Params["type"]."/page/".$this->_Params["page"]."/";
				$pars = $this->Pars($type, "<div class=\"featured-thumb.*?\">\s+<a href=\".*?>\s+<img class=\".*?\" src=\"(.*?)\".*?>\s+<div class=\"postedon\">(.*?)<\/div>\s+<div class=\"out-thumb\">\s+<h1 class=.*?href=\"(.*?)\".*?>(.*?)<");
				$pars2 = $this->Pars($type, "<div class=\"featured-thumb.*?\">\s+<a href=\"(.*?)\".*?>\s+<img.*?src=\"(.*?)\".*?>\s+<\/a>\s+<div.*?<\/i>(.*?)<\/div>\s+<div.*?>\s+<h1.*?><a href=\".*?>(.*?)<\/a>");
				$count = count($pars[1]);
				$count = $count-1;
				$count2 = count($pars2[1]);
				$count2 = $count2-1;
				if(preg_match("/completed|movie/", $type)){
					for($i = 0; $i<=$count2; $i++){
						$lnk = $pars2[1][$i];
						$img = $pars2[2][$i];
						$rat = $pars2[3][$i];
						$tit = $pars2[4][$i];
						$this->_Data[] = array(
							"title" => $tit,
							"link" => $lnk,
							"rating" => $rat,
							"image" => $img
							);
					}
					$this->_Arr[] = array(
						"page" => $this->_Params["page"],
						"type" => $this->_Params["type"],
						"result" => $this->_Data
						);
					echo json_encode($this->_Arr, JSON_PRETTY_PRINT);
				}
				else{
					for($i = 0; $i<=$count; $i++){
						$img = $pars[1][$i];
						$eps = preg_replace("/^Episode /i", "", $pars[2][$i]);
						$lnk = $pars[3][$i];
						$tit = $pars[4][$i];
						$this->_Data[] = array(
							"title" => $tit,
							"episode" => $eps,
							"link" => $lnk,
							"image" => $img
							);
					}
					$this->_Arr[] = array(
						"page" => $this->_Params["page"],
						"type" => $this->_Params["type"],
						"result" => $this->_Data
						);
					echo json_encode($this->_Arr, JSON_PRETTY_PRINT);
				}
			}
		}
		elseif($this->_Params["info"]){
			if(!empty($this->_Params["info"])){
				$pars = $this->MakeRequest($this->_Params["info"]);
				$jdl = $this->getStr($pars, "<b>Informasi Seputar ", "</b>");
				$alt = $this->getStr($pars, "<li><strong>Judul Alternatif : </strong>", "</li>");
				$eps = $this->getStr($pars, "<li class=\"Episodex\"><strong>Jumlah Episode : </strong>", "</li>");
				$rel = $this->getStr($pars, "<li><strong>Musim Rilis : </strong>", "</li>");
				$air = $this->getStr($pars, "<li><strong>Tanggal Tayang : </strong>", "</li>");
				$min = $this->getStr($pars, "<li><strong>Durasi per Episode : </strong>", "</li>");
				$stu = $this->getStr($pars, "<li class=\"Studiox\"><strong>Studio yang Memproduksi : </strong>", "</li>");
				$gen = $this->getStr($pars, "<li class=\"Genrex\"><strong>Genre : </strong>", "</li>");
				$rat = $this->getStr($pars, "<li class=\"Scorex\"><strong>Skor di MyAnimeList : </strong>", "</li>");
				$img = $this->getStr($pars, "<img width=\"750\" height=\"410\" src=\"", "\" class=\"single-featured wp-post-image\"");
				$this->_Arr[] = array(
					"message" => $this->_MSG,
					"result" => array(
						"title" => $jdl,
						"alternatif" => $alt,
						"episode" => $eps,
						"release" => $rel,
						"airing" => $air,
						"minute" => $min,
						"studio" => $stu,
						"genre" => $gen,
						"rating" => $rat,
						"img" => $img
						)
					);
				echo json_encode($this->_Arr, JSON_PRETTY_PRINT);
			}
			else{
				echo $this->_ERROR["null"] . " EX: file.php?info=LINK";
			}
		}
		elseif($this->_Params["search"]){
			$pars = $this->Pars($this->_selfURL."search/".$this->_Params["search"], "<a href=\"(.*?)\" title=\"\">\s+<img class.*?src=\"(.*?)\" alt=\"(.*?)\"");
			$count = count($pars[1]);
			$count = $count-1;
			for($i=0;$i<=$count;$i++){
				$link = $pars[1][$i];
				$gmbr = $pars[2][$i];
				$judl = $pars[3][$i];
				$this->_Data[] = array(
					"link" => $link,
					"image" => $gmbr,
					"title" => $judl
					);
			}
			$this->_Arr[] = array(
				"result" => $this->_Data
				);
			echo json_encode($this->_Arr, JSON_PRETTY_PRINT);
		}
		elseif($this->_Params["download"]){
			$pars = $this->Pars($this->_Params["download"], "<a .*?http:\/\/davinsurance.com\/?(.*?) .*?rel.*?>(.*?)<\/a>");
			$count = count($pars[1]);
			$count = $count-1;
			$req = $this->MakeRequest($this->_Params["download"]);
			$tit = $this->getStr($req, "<b>Informasi Seputar ", "</b>");
			$pars2 = $this->Pars($this->_Params["download"], "<center><span style=\"color: #FFFFFF;\"><b>(.*?)<");
			$count2 = count($pars2[1]);
			$count2 = $count2-1;
			$pars3 = $this->Pars($this->_Params["download"], "");
			for($i=0;$i<=$count;$i++){
				$link = $pars[1][$i];
				$prov = $pars[2][$i];
				$this->_Data[] = array(
					"link" => "http://davinsurance.com/".$link,
					"title" => $prov
					);
			}
			for($i=0;$i<=$count2;$i++){
				$mps = $pars2[1][$i];
				$this->_Data2[] = array(
					$mps => $this->_Data
					);
			}
			$this->_Arr[] = array(
				"title" => $tit,
				"result" => $this->_Data2
				);
			echo json_encode($this->_Arr, JSON_PRETTY_PRINT);
		}
		else{
			$pars = $this->Pars($this->_selfURL, "<div class=\"featured-thumb.*?\">\s+<a href=\".*?>\s+<img class=\".*?\" src=\"(.*?)\".*?>\s+<div class=\"postedon\">(.*?)<\/div>\s+<div class=\"out-thumb\">\s+<h1 class=.*?href=\"(.*?)\".*?>(.*?)<");
			$pars2 = $this->Pars($this->_selfURL, "<div class=\"featured-thumb.*?\">\s+<a href=\"(.*?)\".*?>\s+<img.*?src=\"(.*?)\".*?>\s+<\/a>\s+<div.*?<\/i>(.*?)<\/div>\s+<div.*?>\s+<h1.*?><a href=\".*?>(.*?)<\/a>");
			$count = count($pars[1]);
			$count = $count-1;
			$count2 = count($pars2[1]);
			$count2 = $count2-1;
			for($i = 0; $i<=$count; $i++){
				$img = $pars[1][$i];
				$eps = preg_replace("/^Episode /i", "", $pars[2][$i]);
				$lnk = $pars[3][$i];
				$tit = $pars[4][$i];
				$this->_Data[] = array(
					"title" => $tit,
					"episode" => $eps,
					"link" => $lnk,
					"image" => $img
					);
			}
			for($i = 0; $i<=$count2; $i++){
				$lnk = $pars2[1][$i];
				$img = $pars2[2][$i];
				$rat = $pars2[3][$i];
				$tit = $pars2[4][$i];
				$this->_Data2[] = array(
					"title" => $tit,
					"link" => $lnk,
					"rating" => $rat,
					"image" => $img
					);
			}
			$this->_Arr[] = array(
				"message" => $this->_MSG,
				"result" => array(
					"ongoing" => $this->_Data,
					"other" => $this->_Data2
					)
				);
			echo json_encode($this->_Arr, JSON_PRETTY_PRINT);
		}
	}
}
$lib = new GrabMeow();
$lib->Home();
