<?php

namespace Managon;

use pocketmine\Player;
use pocketmine\Plugin\PluginBase;
use pocketmine\Server;
use pocketmine\item\Item;
use pocketmine\event\Listener;
use pocketmine\network\protocol\InteractPacket;
use pocketmine\entity\Entity;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\utils\Config;
use pocketmine\utils\UUID;

use pocketmine\network\protocol\AddEntityPacket;
use pocketmine\network\protocol\AddPlayerPacket;
use pocketmine\network\protocol\RemoveEntityPacket;
use pocketmine\network\protocol\RemovePlayerPacket;
use pocketmine\network\protocol\PlayerListPacket;

use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\event\player\PlayerMoveEvent;

class CustomNPC extends PluginBase implements Listener{
	
	public $ids = 
	[
	80,19,43,90,40,10,11,33,38,41,20,16,22,12,36,18,13,39,34,81,21,35,17,16,14,32,44
	];

	private $opts = ["msg", "name", "move", "top", "command", "item"];

	private $del;

	private $uuidFolder = [];

	private $opt = [];
	public function onEnable(){
		
		$this->server = Server::getInstance();
		$this->server->getPluginManager()->registerEvents($this,$this);
		if(!file_exists($this->getDataFolder())) @mkdir($this->getDataFolder(), 0744, true);

		$this->npc = new Config($this->getDataFolder()."NPC.json",Config::JSON,array());
		$this->eids = new Config($this->getDataFolder()."eid.yml",Config::YAML,array());
		
	}
	
	public function onReceive(DataPacketReceiveEvent $event){
		$pk = $event->getPacket();
		if($pk instanceof InteractPacket){
			if($this->npc->exists($pk->target))
			{
				$player = $event->getPlayer();

				if(in_array($player->getName(), (array) $this->del))
				{
					$target = $pk->target;

					if($this->npc->get($target)["type"] === "player")
					{
						$pk = new RemovePlayerPacket();

						$pk->eid = $target;

						$pk->clientId = $this->uuidFolder[$target];

						unset($this->uuidFolder[$eid]);

						$this->server->broadcastPacket($this->server->getOnlinePlayers(), $pk);

						$this->server->removePlayerListData($pk->clientId);

					    $this->npc->remove($target);

					    $this->npc->save();

					    $player->sendMessage("Removed!");

					    unset($this->del[$player->getName()]);

					    return;
					}

					$pk = new RemoveEntityPacket();

					$pk->eid = $target;

					$this->server->broadcastPacket($this->server->getOnlinePlayers(), $pk);

					$this->npc->remove($target);

					$this->npc->save();

					$player->sendMessage("Removed!");

					unset($this->del[$player->getName()]);
					
					return;
				}

				if(isset($this->opt[$player->getName()]))
				{
					$target = $pk->target;
					foreach ($this->opt[$player->getName()] as $type => $opt) 
					{
						switch ($type) {
							case 'name':
							case 'msg':
							case 'top':
							case 'item':
							    $this->setOpt($target, $type, $opt);
							    unset($this->opt[$player->getName()]);
							    break;


							case 'move':
								$this->setOpt_pos($target, ["x" => $opt["x"], "y" => $opt["y"], "z" => $opt["z"]]);
								unset($this->opt[$player->getName()]);
								break;

							case 'command':
							    foreach ($opt as $opt2 => $command) 
							    {
							    	$this->setOpt_Command($target, $opt2, $command);
							    }
							    unset($this->opt[$player->getName()]);
							    break;
						}
					}
					return;
				}
				$ef = $this->npc->get($pk->target);
				$ef["yaw"] = $player->getYaw() + 180;
				$this->add($pk->target, $ef, $player);

				$commandA = $ef["commands"];
				foreach ((array) $commandA as $command) 
				{
					$this->server->getCommandMap()->dispatch($player, $command);
				}
				$player->getInventory()->addItem(Item::get(intval($ef["item"]["id"]),0,$ef["item"]["amount"]));
				
				if($ef["msg"] !== null || $ef["msg"] !== "") $player->sendMessage($ef["msg"]);
			}
		}
	}

	public function onJoin(PlayerJoinEvent $event)
	{
		$player = $event->getPlayer();

		foreach ($this->npc->getAll() as $key => $data) 
		{
			$type = $data["type"];
			$x = $data["x"];
			$y = $data["y"];
			$z = $data["z"];
			$itemId = $data["item"]["id"];
			$amount = $data["item"]["amount"];
			$name = $data["name"];

			if($type === "player")
			{
				$pk = new AddPlayerPacket();
				$pk->eid = $key;
				//$pk->uuid = UUID::fromString(base64_decode($data["uuid"]));
				$pk->uuid = UUID::fromRandom();
				$pk->username = $name;
				$pk->x = $x;
				$pk->y = $y+1;
				$pk->z = $z;
				$pk->speedX = 0;
	        	$pk->speedY = 0;
	        	$pk->speedZ = 0;
	        	$pk->yaw = $data["yaw"];
	        	$pk->pitch = $data["pitch"];
	        	$pk->item = Item::get(intval($itemId),0,0);
	        	$pk->metadata = [Entity::DATA_NAMETAG => [Entity::DATA_TYPE_STRING,$name],
	        	        Entity::DATA_SHOW_NAMETAG => [Entity::DATA_TYPE_BYTE,1]];

				$player->dataPacket($pk);

				$this->server->updatePlayerListData($pk->uuid, $pk->eid, $name, $data["skin_name"], base64_decode($data["skin"]),$this->server->getOnlinePlayers());

				$this->uuidFolder[$key] = $pk->uuid;
			}else
			{
				$pk = new AddEntityPacket();

	        	        $pk->eid = $key;
	        	        $pk->type = $type;
	        	        $pk->x = $x+0.5;
	        	        $pk->y = $y+1;
	        	        $pk->z = $z+0.5;
	        	        $pk->yaw = $data["yaw"];
	        	        $pk->pitch = $data["pitch"];
	        	        $pk->metadata = [Entity::DATA_NAMETAG => [Entity::DATA_TYPE_STRING,$name],
						 Entity::DATA_SHOW_NAMETAG => [Entity::DATA_TYPE_BYTE,1 ],
					      	 Entity::DATA_NO_AI => [Entity::DATA_TYPE_BYTE,1]];

				$player->dataPacket($pk);
			}
		}
	}

	
	public function onCommand(CommandSender $sender, Command $command, $label, array $args)
	{
		if(strtolower($command->getName()) === "cn")
		{
			if($sender instanceof Player)
		    {
			    if($sender->isOp())
		        {
		    	    if(isset($args[0]))
		    	    {
		    	    	switch ($args[0]) 
		    	    	{
		    	    		case '':
		    	    		case 'help':
		    	    		    $sender->sendMessage("§6======CustomNPC=======");
		    	    		    $sender->sendMessage("§a/cn add <id|player> <item> <command> <message|name>");
		    	    		    $sender->sendMessage("§a/cn del §f: §bDelete NPC");
		    	    		    $sender->sendMessage("§6/cn opt §amove <x> <y> <z>\n        §aitem <id> <amount>\n        §acommand <add | del> <command>\n        §atop <message>\n        §amsg <message>\n        §aname <name>\n\n");
		    	    		    break;

		    	    		case 'add':
		    	    			if (count($args) < 5) 
		    	    			{
		    	    		        $sender->sendMessage("§a/cn add <id|player> <item> <command> <message|name>");
		    	    			}else
		    	    			{
		    	    				if(in_array(intval($args[1]), $this->ids) || $args[1] === "player")
		    	    				{
		    	    					if($args[1] === "player")
		    	    					{
		    	    						$id = $args[1];
		    	    						$item = $args[2];
		    	    						$c = str_replace("/", "", $args[3]);
		    	    						$m = $args[4];

		    	    						$this->tap[$sender->getName()] = ["player",$item,$c,$m];

		    	    						$sender->sendMessage("Tap!");
		    	    					}else
		    	    					{
		    	    						$id = $args[1];
		    	    						$item = $args[2];
		    	    						$c = str_replace("/", "", $args[3]);
		    	    						$m = $args[4];

		    	    						$this->tap[$sender->getName()] = [$id,$item,$c,$m];

		    	    						$sender->sendMessage("Tap!");
		    	    					}
		    	    				}
		    	    			}
		    	    			break;

		    	    		case 'del':
		    	    		    $this->del = [$sender->getName()];
		    	    		    $sender->sendMessage("Tap NPC");
		    	    		    break;

		    	    		case 'opt':
		    	    		if(isset($args[1]))
		    	    		{
		    	    		    if(in_array($args[1], $this->opts))
		    	    		    {
		    	    		    	switch ($args[1]) {
		    	    		    		case 'command':
		    	    		    		    if(isset($args[2]) && isset($args[3]))
		    	    		    		    {
		    	    		    		    	switch ($args[2]) {
		    	    		    		    		case 'add':
		    	    		    		    			$this->opt[$sender->getName()]["command"]["add"] = $args[3];
		    	    		    		    		    $sender->sendMessage("Tap NPC");
		    	    		    		    			break;
		    	    		    		    		
		    	    		    		    		case 'del':
		    	    		    		    		    $this->opt[$sender->getName()]["command"]["del"] = $args[3];
		    	    		    		    		    $sender->sendMessage("Tap NPC");
		    	    		    		    			break;
		    	    		    		    		default:
		    	    		    		    			$sender->sendMessage("§6Usage : /cn opt command <add | del> <command>");
		    	    		    		    			break;
		    	    		    		    	}
		    	    		    		    }else
		    	    		    		    {
		    	    		    		    	$sender->sendMessage("§6Usage : /cn opt command <add | del> <command>");
		    	    		    		    	return;
		    	    		    		    }
		    	    		    			break;
		    	    		    		
		    	    		    		case 'name':
		    	    		    		    if(isset($args[2]))
		    	    		    		    {
		    	    		    		    	$this->opt[$sender->getName()]["name"] = $args[2];
		    	    		    		    	$sender->sendMessage("Tap NPC");
		    	    		    		    }else
		    	    		    		    {
		    	    		    		    	$sender->sendMessage("§6Usage : /cn opt name <name>");
		    	    		    		    	return;
		    	    		    		    }
		    	    		    		    break;

		    	    		    		case 'msg':
		    	    		    		    if(isset($args[2]))
		    	    		    		    {
		    	    		    		    	$this->opt[$sender->getName()]["msg"] = $args[2];
		    	    		    		    	$sender->sendMessage("Tap NPC");
		    	    		    		    }else
		    	    		    		    {
		    	    		    		    	$sender->sendMessage("§6Usage : /cn opt msg <message>");
		    	    		    		    	return;
		    	    		    		    }
		    	    		    		    break;

		    	    		    		case 'top':
		    	    		    		    if(isset($args[2]))
		    	    		    		    {

		    	    		    		    	$this->opt[$sender->getName()]["top"] = $args[2];
		    	    		    		    	$sender->sendMessage("Tap NPC");
		    	    		    		    }else
		    	    		    		    {
		    	    		    		    	$sender->sendMessage("§6Usage : /cn opt top <message>");
		    	    		    		    	return;
		    	    		    		    }
		    	    		    		    break;

		    	    		    		case 'move':
		    	    		    			if (count($args) === 5) 
		    	    		    			{
		    	    		    				$optx = (int) $args[2];
		    	    		    				$opty = (int) $args[3];
		    	    		    				$optz = (int) $args[4];

		    	    		    				$this->opt[$sender->getName()]["move"] = 
		    	    		    				[
		    	    		    				"x" => $optx,
		    	    		    				"y" => $opty,
		    	    		    				"z" => $optz,
		    	    		    				];
		    	    		    				$sender->sendMessage("Tap NPC");
		    	    		    			}else
		    	    		    			{
		    	    		    				$sender->sendMessage("§6Usage : /cn opt move <x> <y> <z>");
		    	    		    		    	return;
		    	    		    			}

		    	    		    			break;		

		    	    		    		case 'item':
		    	    		    		    if(isset($args[2]) && isset($args[3]))
		    	    		    		    {
		    	    		    		    	$itemId = $args[2];
		    	    		    		    	$amount = $args[3];

		    	    		    		    	$this->opt[$sender->getName()]["item"] = 
		    	    		    		    	[
		    	    		    		    	"id" => $itemId,
		    	    		    		    	"amount" => $amount
		    	    		    		    	];
		    	    		    		    	$sender->sendMessage("Tap NPC");
		    	    		    		    }else
		    	    		    		    {
		    	    		    		    	$sender->sendMessage("§6Usage : /cn opt item <id> <amount>");
		    	    		    		    	return;
		    	    		    		    }
		    	    		    		    break;
		    	    		    		default:
		    	    		    			$sender->sendMessage("§6/cn opt §amove <x> <y> <z>\n        
		    	    		    				§aitem <id> <amount>\n        
		    	    		    				§acommand <add | del> <command>\n        
		    	    		    				§atop <message>\n        
		    	    		    				§amsg <message>\n        
		    	    		    				§aname <name>");
		    	    		    			break;
		    	    		    	}
		    	    		    }
		    	    		}else
		    	    		{
		    	    			$sender->sendMessage("§6/cn opt §amove <x> <y> <z>\n        §aitem <id> <amount>\n        §acommand <add | del> <command>\n        §atop <message>\n        §amsg <message>\n        §aname <name>");
		    	    			break;
		    	    		}
		    	    		break;
		    	    		default:
		    	    			$sender->sendMessage("§6======CustomNPC=======");
		    	    		        $sender->sendMessage("§a/cn add <name> <item> <command> §f: §bAdd NPC!");
		    	    		        $sender->sendMessage("§a/cn del §f: §bDelete NPC");
		    	    			break;
		    	    	}
		    	    }
		        }
		    }else{
		    	$sender->sendMessage("§6======CustomNPC=======");
		    	    		    $sender->sendMessage("§a/cn add <id|player> <item> <command> <message|name>");
		    	    		    $sender->sendMessage("§a/cn del §f: §bDelete NPC");
		    	    		    $sender->sendMessage("§6/cn opt §amove <x> <y> <z>\n        §aitem <id> <amount>\n        §acommand <add | del> <command>\n        §atop <message>\n        §amsg <message>\n        §aname <name>\n\n");
		    	$sender->sendMessage("§6Entity Id Lists\n§aArrow §f: §b80\n§aBat §f: §b19\n§aBlaze §f: §b43\n§aBoat §f: §b90\n§aCaveSpider §f: §b40\n§aChicken §f: §b10\n§aCow §f: §b 11\n§aCreeper §f: §b33\n§aEnderman §f: §b38\n§aGhast §f: §b41\n§aIronGolem §f: §b20\n§aMooshroom §f: §b16\n§aOcelot §f: §b22\n§aPig §f: §b12\n§aPigZombie §f: §b36\n§aRabbit §f: §b18\n§aSheep §f: §b13\n§aSilverFish §f: §b39\n§aSkelton §f: §b34\n§aSnowBall §f: §b81\n§aSnowGolem §f: §b21\n§aSpider §f: §b35\n§aSquid §f: §b17\n§aVillager §f: §b16\n§aWolf §f: §b14\n§aZombie §f: §b32\n§aZombieVillager §f: §b44");
		    }
		}
	}

	public function onTap(PlayerInteractEvent $event)
	{
		if(isset($this->tap[$event->getPlayer()->getName()]))
		{
			$player = $event->getPlayer();

			$name = $player->getName();

			$block = $event->getBlock();

			$x = $block->x;
			$y = $block->y;
			$z = $block->z;


			$id = $this->tap[$name][0];

	                $itemId = $this->tap[$name][1];

	                $command = $this->tap[$name][2];

	                $n = $this->tap[$name][3];

	                if($id === "player")
	                {
	        	    $pk = new AddPlayerPacket();

	        	    $pk->uuid = UUID::fromRandom();
	              	    $pk->username = $n;
	        	    $pk->eid = mt_rand(1000,100000);
	        	    $pk->x = $x+0.5;
	        	    $pk->y = $y+1;
	        	    $pk->z = $z+0.5;
	        	    $pk->speedX = 0;
	        	    $pk->speedY = 0;
	        	    $pk->speedZ = 0;
	        	    $pk->yaw = $player->getYaw() + 180;
	        	    $pk->pitch = -5;
	        	    $pk->item = Item::get(0,0,0);
	        	    $pk->metadata = [Entity::DATA_NAMETAG => [Entity::DATA_TYPE_STRING,$n],
					     Entity::DATA_SHOW_NAMETAG => [Entity::DATA_TYPE_BYTE,1]];

			    $this->server->broadcastPacket($player->getLevel()->getPlayers(), $pk);

			    $this->server->updatePlayerListData($pk->uuid, $pk->eid, $n, $player->getSkinName(), $player->getSkinData(),$this->server->getOnlinePlayers());
			    $this->uuidFolder[$pk->eid] = $pk->uuid;

			    $this->npc->set($pk->eid, [
			    	"type" => "player",
			    	"x"=>$x,
			    	"y"=>$y,
			    	"z"=>$z,
			    	"item"=>["id"=>$itemId,"amount"=>1],
			    	"commands"=>[$command],
			    	"name"=>$n,"uuid"=>base64_encode($pk->uuid),
			    	"skin" => base64_encode($player->getSkinData()), 
			    	"skin_name"=>$player->getSkinName(),
			    	"text-on-top" => "", 
			    	"msg" => "",
			    	"yaw" => $pk->yaw,
			    	"pitch" => $pk->pitch]);
			    $this->npc->save();
			    $this->eids->set(count($this->eids->getAll()), $pk->eid);
			    $this->eids->save();
			    unset($this->tap[$name]);
			
	                }else
	                {
	        	    $pk = new AddEntityPacket();

	        	    $pk->eid = mt_rand(1000,100000);
	        	    $pk->type = $id;
	        	    $pk->x = $x+0.5;
	        	    $pk->y = $y+1;
	        	    $pk->z = $z+0.5;
	        	    $pk->yaw = $player->getYaw() + 180;
	        	    $pk->pitch = $player->getPitch();
	        	    $pk->metadata = [Entity::DATA_NAMETAG => [Entity::DATA_TYPE_STRING,$n],
					     Entity::DATA_SHOW_NAMETAG => [Entity::DATA_TYPE_BYTE,1],
					     Entity::DATA_NO_AI => [Entity::DATA_TYPE_BYTE,1]];
					     
	        	    foreach ($this->server->getOnlinePlayers() as $p) 
			    {
				 $p->dataPacket($pk);
			    }
			    
	        	    $this->npc->set($pk->eid, [
	        	    	"type" => $id, 
	        	    	"x"=>$x,
	        	    	"y"=>$y,
	        	    	"z"=>$z,
	        	    	"item"=>["id"=>$itemId,"amount"=>1],
	        	    	"commands"=>["random" => true, $command],
	        	    	"name"=>$n,
	        	    	"text-on-top" => "", 
	        	    	"msg" => "",
	        	    	"yaw" => $pk->yaw,
			    	    "pitch" => $pk->pitch]);
			    $this->npc->save();
			    $this->eids->set(count($this->eids->getAll()), $pk->eid);
			    $this->eids->save();
			    unset($this->tap[$player->getName()]);
	                }
		}
	}

	public function setOpt($eid, $type, $args)
	{
		$data = $this->npc->get($eid);
		$data[$type] = $args;
		$this->npc->set($eid, $data);
		$this->npc->save();

		$this->add($eid, $data);
	}

	public function setOpt_pos($eid, array $xyz)
	{
		$data = $this->npc->get($eid);
		$x = $xyz["x"];
		$y = $xyz["y"];
		$z = $xyz["z"];
		$data["x"] = $x;
		$data["y"] = $y;
		$data["z"] = $z;
		$this->npc->set($eid, $data);
		$this->npc->save();

		$this->add($eid, $data);
	}

	public function setOpt_Command($eid, $type, $command)
	{
		$data = $this->npc->get($eid);
		if($type === 'add')
		{
			$s = str_replace("/", "", $command);
			$data["commands"][] = $s;
			$this->npc->set($eid, $data);
			$this->npc->save();
		}elseif ($type === "del") 
		{
			if(($key = array_search(str_replace("/", "", $command), $data["commands"])) !== false) 
		    {
		    	unset($data["commands"][$key]);
		    	$this->npc->set($eid, $data);
		    	$this->npc->save();
            } 
		}
	}

	public function add($eid, array $options, $player = null)
	{
		$type = $options["type"];
		$x = $options["x"];
		$y = $options["y"];
		$z = $options["z"];
		if($type == 64)
		{
			$pk = new AddEntityPacket();
			$pk->metadata = 
			[
			  Entity::DATA_FLAGS => [Entity::DATA_TYPE_BYTE, 1 << Entity::DATA_FLAG_INVISIBLE],
			  Entity::DATA_NAMETAG => [Entity::DATA_TYPE_STRING, $options["text"]],
			  Entity::DATA_SHOW_NAMETAG => [Entity::DATA_TYPE_BYTE, 1],
			  Entity::DATA_NO_AI => [Entity::DATA_TYPE_BYTE, 1]
		    ];
		    $pk->x = $x;
		    $pk->y = $y+4;
		    $pk->z = $z;
		    $pk->yaw = 0;
	        $pk->pitch = 0;
		    $pk->eid = mt_rand(10000,100000);
		    $pk->type = intval($type);
		    $player->dataPacket($pk);
		    return;
		}
		$name = $options["name"];
		if($type === "player")
	    {
	    	$removePlayerPacket = new RemovePlayerPacket();
			$removePlayerPacket->eid = $eid;
			$removePlayerPacket->clientId = $this->uuidFolder[$eid];

			$this->server->removePlayerListData($removePlayerPacket->clientId);

			unset($this->uuidFolder[$eid]);

			if($player !== null)
			{
				$player->dataPacket($removePlayerPacket);
			}else{
				$this->server->broadcastPacket($this->server->getOnlinePlayers(), $removePlayerPacket);
			}
	        $pk = new AddPlayerPacket();

	        $pk->uuid = UUID::fromRandom();
	        $pk->username = $name;
	        $pk->eid = mt_rand(1000,10000);
	        $pk->x = $x;
	        $pk->y = $y+1;
	        $pk->z = $z;
	        $pk->speedX = 0;
	        $pk->speedY = 0;
	        $pk->speedZ = 0;
	        $pk->yaw = $options["yaw"];
	        $pk->pitch = $options["pitch"];
	        $pk->item = Item::get(0,0,0);
	        $pk->metadata = [Entity::DATA_NAMETAG => [Entity::DATA_TYPE_STRING,$name],
					     Entity::DATA_SHOW_NAMETAG => [Entity::DATA_TYPE_BYTE,1]];

			if($player !== null)
					{
						$player->dataPacket($pk);
					}else{
						$this->server->broadcastPacket($this->server->getOnlinePlayers(), $pk);
					}
			$this->uuidFolder[$pk->eid] = $pk->uuid;
			$this->server->updatePlayerListData($pk->uuid, $pk->eid, $name, $options["skin_name"], base64_decode($options["skin"]),$this->server->getOnlinePlayers());

			$this->npc->set($pk->eid, $options);
			$this->npc->save();

			$this->npc->remove($eid);

			$this->npc->save();
	    }else
	    {
	    	$pk = new AddEntityPacket();

	        	    $pk->eid = mt_rand(1000,10000);
	        	    $pk->type = $type;
	        	    $pk->x = $x+0.5;
	        	    $pk->y = $y+1;
	        	    $pk->z = $z+0.5;
	        	    $pk->yaw = $options["yaw"];
	                $pk->pitch = $options["pitch"];
	        	    $pk->metadata = [Entity::DATA_NAMETAG => [Entity::DATA_TYPE_STRING,$name],
					     Entity::DATA_SHOW_NAMETAG => [Entity::DATA_TYPE_BYTE,1],
					     Entity::DATA_NO_AI => [Entity::DATA_TYPE_BYTE,1]];
					     
	        	    foreach ($this->server->getOnlinePlayers() as $p) 
			        {
				        $p->dataPacket($pk);
			        }
                    $this->npc->set($pk->eid, $options);
			        $this->npc->save();

			        $pk = new RemoveEntityPacket();

					$pk->eid = $eid;

					if($player !== null)
					{
						$player->dataPacket($pk);
					}else{
						$this->server->broadcastPacket($this->server->getOnlinePlayers(), $pk);
					}

					$this->npc->remove($eid);

					$this->npc->save();
	    }
	}
}