<?php
if(!defined('DEDEINC'))
{
	exit("Request Error!");
}

require_once(DEDEROOT."/data/cache/inc_catalog_base.inc");

class TypeUnit
{
	var $dsql;
	var $aChannels;
	var $isAdminAll;

	//php5构造函数
	function __construct($catlogs='')
	{
		global $cfg_Cs;
		$this->dsql = $GLOBALS['dsql'];
		$this->aChannels = Array();
		$this->isAdminAll = false;
		if(!empty($catlogs) && $catlogs!='-1')
		{
			$this->aChannels = explode(',',$catlogs);
			foreach($this->aChannels as $cid)
			{
				if($cfg_Cs[$cid][0]==0)
				{
					$this->dsql->SetQuery("Select id,ispart From `#@__arctype` where reid=$cid");
					$this->dsql->Execute();
					while($row = $this->dsql->GetObject())
					{
						//if($row->ispart==1)
						$this->aChannels[] = $row->id;
					}
				}
			}
		}
		else
		{
			$this->isAdminAll = true;
		}
	}

	function TypeUnit($catlogs='')
	{
		$this->__construct($catlogs);
	}

	//清理类
	function Close()
	{
	}

	//----读出所有分类,在类目管理页(list_type)中使用----------
	function ListAllType($channel=0,$nowdir=0)
	{

		global $cfg_admin_channel, $admin_catalogs;
		
		//检测用户有权限的顶级栏目
		if($cfg_admin_channel=='array')
		{
			$admin_catalog = join(',', $admin_catalogs);
			$this->dsql->SetQuery("Select reid From `#@__arctype` where id in($admin_catalog) group by reid ");
			$this->dsql->Execute();
			$topidstr = '';
			while($row = $this->dsql->GetObject())
			{
				if($row->reid==0) continue;
				$topidstr .= ($topidstr=='' ? $row->reid : ','.$row->reid);
			}
			$admin_catalog .= ','.$topidstr;
			$admin_catalogs = explode(',', $admin_catalog);
			$admin_catalogs = array_unique($admin_catalogs);
		}
		
		$this->dsql->SetQuery("Select id,typedir,typename,ispart,channeltype From `#@__arctype` where reid=0 order by sortrank");
		
		$this->dsql->Execute(0);
		$lastid = GetCookie('lastCidMenu');
		while($row=$this->dsql->GetObject(0))
		{
			if( $cfg_admin_channel=='array' && !in_array($row->id, $admin_catalogs) )
			{
				continue;
			}
			
			$typeDir = $row->typedir;
			$typeName = $row->typename;
			$ispart = $row->ispart;
			$id = $row->id;
			$channeltype = $row->channeltype;

			//普通栏目
			if($ispart==0)
			{
					$smenu = " oncontextmenu=\"CommonMenu(event,this,$id,'".urlencode($typeName)."')\"";
			}
			//封面频道
			else if($ispart==1)
			{
					$smenu = " oncontextmenu=\"CommonMenuPart(event,this,$id,'".urlencode($typeName)."')\"";
			}
			//独立页面
			//else if($ispart==2)
			//{
					//$smenu = " oncontextmenu=\"SingleMenu(event,this,$id,'".urlencode($typeName)."')\"";
			//}
			//跳转网址
			else
			{
					continue;
					$smenu = " oncontextmenu=\"JumpMenu(event,this,$id,'".urlencode($typeName)."')\" ";
			}
			echo "<dl class='topcc'>\r\n";
			echo "  <dd class='dlf'><img style='cursor:pointer' onClick=\"LoadSuns('suns{$id}',{$id});\" src='img/tree_explode.gif' width='11' height='11'></dd>\r\n";
			echo "  <dd class='dlr'><a href='catalog_do.php?cid=".$id."&dopost=listArchives'{$smenu}>".$typeName."</a></dd>\r\n";
			echo "</dl>\r\n";
			echo "<div id='suns".$id."' class='sunct'>";
			if($lastid==$id || $cfg_admin_channel=='array')
			{
					$this->LogicListAllSunType($id, "　");
			}
			echo "</div>\r\n";
		}
	}

	//获得子类目的递归调用
	function LogicListAllSunType($id,$step,$needcheck=true)
	{
		global $cfg_admin_channel, $admin_catalogs;
		$fid = $id;
		$this->dsql->SetQuery("Select id,reid,typedir,typename,ispart,channeltype From `#@__arctype` where reid='".$id."' order by sortrank");
		$this->dsql->Execute($fid);
		if($this->dsql->GetTotalRow($fid)>0)
		{
			while($row=$this->dsql->GetObject($fid))
			{
				if($cfg_admin_channel=='array' && !in_array($row->id, $admin_catalogs) )
				{
					continue;
				}
				$typeDir = $row->typedir;
				$typeName = $row->typename;
				$reid = $row->reid;
				$id = $row->id;
				$ispart = $row->ispart;
				$channeltype = $row->channeltype;
				if($step=="　")
				{
					$stepdd = 2;
				}
				else
				{
					$stepdd = 3;
				}

				//有权限栏目
				if(in_array($id,$this->aChannels) || $needcheck===false || $this->isAdminAll===true)
				{
					//普通列表
					if($ispart==0||empty($ispart))
					{
						$smenu = " oncontextmenu=\"CommonMenu(event,this,$id,'".urlencode($typeName)."')\"";
						$timg = " <img src='img/tree_page.gif'> ";
					}

					//封面频道
					else if($ispart==1)
					{
						$smenu = " oncontextmenu=\"CommonMenuPart(event,this,$id,'".urlencode($typeName)."')\"";
						$timg = " <img src='img/tree_part.gif'> ";
					}

					//独立页面
					//else if($ispart==2)
					//{
						//$timg = " <img src='img/tree_page.gif'> ";
						//$smenu = " oncontextmenu=\"SingleMenu(event,this,$id,'".urlencode($typeName)."')\" ";
					//}

					//跳转网址
					else
					{
						continue;
						$timg = " <img src='img/tree_page.gif'> ";
						$smenu = " oncontextmenu=\"JumpMenu(event,this,$id,'".urlencode($typeName)."')\" ";
					}
					echo "  <table class='sunlist'>\r\n";
					echo "   <tr>\r\n";
					echo "     <td>".$step.$timg."<a href='catalog_do.php?cid=".$id."&dopost=listArchives'{$smenu}>".$typeName."</a></td>\r\n";
					echo "   </tr>\r\n";
					echo "  </table>\r\n";
					$this->LogicListAllSunType($id,$step."　",false);
				}
			}
		}
	}
}
?>