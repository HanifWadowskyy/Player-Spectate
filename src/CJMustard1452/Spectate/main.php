<?php


namespace CJMustard1452\Spectate;

use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\Config;
use pocketmine\player;

Class main extends PluginBase implements Listener{
	public function onEnable(): void{
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}
	public function onMovement(PlayerMoveEvent $event){
		$Username = $event->getPlayer()->getName();
		$usrfile = $this->myConfig = new Config($this->getDataFolder() . "$Username", Config::YAML);
		if ($usrfile->get("BeingWatched") == true){
			$watcher = $usrfile->get("Watcher");
			if ($this->getServer()->getPlayerExact($watcher) == true){
				$watchname = $this->getServer()->getPlayerExact($watcher);
				$X = $event->getPlayer()->getLocation()->getFloorX();
				$Y = $event->getPlayer()->getLocation()->getFloorY();
				$Z = $event->getPlayer()->getLocation()->getFloorZ();
				$HP = $event->getPlayer()->getHealth();
				$Speed = $event->getPlayer()->getMovementSpeed();
				if ($Speed == 0.13){$watchname->sendActionBarMessage("§7HP,§b $HP §7| Coords, §b$X, $Y, $Z §7| Speed§c Sprinting");
				}elseif($Speed == 0.1){$watchname->sendActionBarMessage("§7HP,§b $HP §7| Coords, §b$X, $Y, $Z §7| Speed§c Walking");
				}elseif($Speed == 0.14){$watchname->sendActionBarMessage("§7HP,§b $HP §7| Coords, §b$X, $Y, $Z §7| Speed§c Speed I");
				}elseif($Speed == 0.182){$watchname->sendActionBarMessage("§7HP,§b $HP §7| Coords, §b$X, $Y, $Z §7| Speed§c Speed I");
				}elseif($Speed == 0.16){$watchname->sendActionBarMessage("§7HP,§b $HP §7| Coords, §b$X, $Y, $Z §7| Speed§c Speed II");
				}elseif($Speed == 0.208){$watchname->sendActionBarMessage("§7HP,§b $HP §7| Coords, §b$X, $Y, $Z §7| Speed§c Speed II");
				}else{
					$RealSpeed = 10*($Speed);
					$watchname->sendActionBarMessage("§7HP,§b $HP §7| Coords, §b$X, $Y, $Z §7| Speed§c $RealSpeed");
				}
			}else{
				$usrfile->set("$watcher", null);
			}
		}
	}
	public function onLeave(PlayerQuitEvent $event){
		$Username = $event->getPlayer()->getName();
		$usrfile = $this->myConfig = new Config($this->getDataFolder() . "$Username", Config::YAML);
		$this->getServer()->getPlayerExact($usrfile->get("Watcher"))->sendMessage("§7(§cSS§7) §b $Username has logged off");
		$usrfile->set("Watcher", null);
	}
	public function onJoin(PlayerJoinEvent $event){
		$Username = $event->getPlayer()->getName();
		$usrfile = $this->myConfig = new Config($this->getDataFolder() . "$Username", Config::YAML);
		if ($usrfile->get("Watching" == false)){
			return true;
		}else{
			$usrfile->set("Watching", false);
			$usrfile->set("WatchedPlayer", null);
			$usrfile->save();
			$event->getPlayer()->respawn();
			$event->getPlayer()->setGamemode($this->getServer()->getGamemode());
			$event->getPlayer()->sendMessage("§7(§cSS§7) §bYou are no longer spectating");
		}
	}
public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
	$Username = $sender->getName();
	$Usrfile = $this->myConfig = new Config($this->getDataFolder() . "$Username", Config::YAML);
	if($Usrfile->get("Watching") == false){
		if(isset($args[0])){
			if($this->getServer()->getPlayerExact($args[0]) == true){
				if($this->getServer()->getPlayerExact($args[0]) == $sender){
					$sender->sendMessage("§7(§cSS§7) §bYou cant spectate yourself");
				}else{
					$Player = $this->getServer()->getPlayerExact($args[0])->getName();
					$Player2file = $this->myConfig = new Config($this->getDataFolder() . "$Player", Config::YAML);
					$Player2file->set("Watcher", "$Username");
					$Player2file->set("BeingWatched", true);
					$Player2file->save();
					$Usrfile->set("Watching", true);
					$Usrfile->set("WatchedPlayer", "$args[0]");
					$Usrfile->save();
					$sender->sendMessage("§7(§cSS§7) §bYou are now spectating $args[0]");
					$sender->setSpawn($this->getServer()->getPlayerExact($Username)->getLocation());
					$sender->setGamemode(player\GameMode::SPECTATOR());
					$sender->teleport($this->getServer()->getPlayerExact($Player)->getLocation());
				}
			}else{
				$sender->sendMessage("§7(§cSS§7) §bPlease enter the full username");
			}
		}else{
			$sender->sendMessage("§7(§cSS§7) §bPlease enter a username");
		}
	}else{
		$ChoosenPlayer = $Usrfile->get("WatchedPlayer");
		$ChoosenPlayerFile = $this->myConfig = new Config($this->getDataFolder() . "$ChoosenPlayer", Config::YAML);
		$Usrfile->set("Watching", false);
		$Usrfile->set("WatchedPlayer", null);
		$ChoosenPlayerFile->set("Watcher", null);
		$ChoosenPlayerFile->set("BeingWatched", false);
		$ChoosenPlayerFile->save();
		$Usrfile->save();
		$sender->sendMessage("§7(§cSS§7) §bYou are no longer spectating");
		$sender->setGamemode($this->getServer()->getGamemode());
		$sender->teleport($sender->getSpawn());
		$sender->setSpawn($this->getServer()->getWorldManager()->getDefaultWorld()->getSpawnLocation());
	}
	return true;
}
}