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
use pocketmine\network\protocol\PlayerListPacket;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\event\server\DataPacketSendEvent;
class CustomNPC extends PluginBase implements Listener{
	
	public $ids = 
	[
	80,19,43,90,40,10,11,33,38,41,20,16,22,12,36,18,13,39,34,81,21,35,17,16,14,32,44
	];
	private $del;
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
					$pk = new RemoveEntityPacket();
					$pk->eid = $target;
					$this->server->broadcastPacket($this->server->getOnlinePlayers(), $pk);
					$this->npc->remove($target);
					$this->npc->save();
					$player->sendMessage("Removed!");
					unset($this->del[$player->getName()]);
					
					return;
				}
				$ef = $this->npc->get($pk->target);
				$command = $ef["command"];
				$this->server->getCommandMap()->dispatch($player, $command);
				$player->getInventory()->addItem(Item::get(intval($ef["item"]["id"]),0,$ef["item"]["amount"]));
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
			$command = $data["command"];
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
	        	        $pk->yaw = mt_rand(0,360);
	        	        $pk->pitch = -5;
	        	        $pk->item = Item::get(intval($itemId),0,0);
	        	        $pk->metadata = [Entity::DATA_NAMETAG => [Entity::DATA_TYPE_STRING,$name],
	        	        
					         Entity::DATA_SHOW_NAMETAG => [Entity::DATA_TYPE_BYTE,1]];
				$player->dataPacket($pk);
				$this->server->updatePlayerListData($pk->uuid, $pk->eid, $name, $data["skin_name"], base64_decode($data["skin"]),$this->server->getOnlinePlayers());
			}else
			{
				$pk = new AddEntityPacket();
	        	        $pk->eid = $key;
	        	        $pk->type = $type;
	        	        $pk->x = $x+0.5;
	        	        $pk->y = $y+1;
	        	        $pk->z = $z+0.5;
	        	        $pk->yaw = mt_rand(0,360);
	        	        $pk->pitch = -5;
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
		    	    		
		    	    		default:
		    	    			$sender->sendMessage("§6======CustomNPC=======");
		    	    		        $sender->sendMessage("§a/cn add <name> <item> <command> §f: §bAdd NPC!");
		    	    		        $sender->sendMessage("§a/cn del §f: §bDelete NPC");
		    	    			break;
		    	    	}
		    	    }
		        }
		    }else{
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
			    $this->npc->set($pk->eid, ["type" => "player","x" =>$x,"y"=>$y,"z"=>$z,"item"=>["id"=>$itemId,"amount"=>1],"command"=>$command,"name"=>$n,"uuid"=>base64_encode($pk->uuid),"skin" => base64_encode($player->getSkinData()), "skin_name"=>$player->getSkinName()]);
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
			    
	        	    $this->npc->set($pk->eid, ["type" => $id, "x" =>$x,"y"=>$y,"z"=>$z,"item"=>["id"=>$itemId,"amount"=>1],"command"=>$command,"name"=>$n]);
			    $this->npc->save();
			    $this->eids->set(count($this->eids->getAll()), $pk->eid);
			    $this->eids->save();
			    unset($this->tap[$player->getName()]);
	                }
		}
	}
