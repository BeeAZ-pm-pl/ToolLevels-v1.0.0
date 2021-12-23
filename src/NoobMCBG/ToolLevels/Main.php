<?php

namespace NoobMCBG\ToolLevels;

use pocketmine\Server;
use pocketmine\Player;
use pocketmine\command\{Command, CommandSender, ConsoleCommandSender};
use pocketmine\event\Listener as L;
use pocketmine\plugin\PluginBase as PB;
use pocketmine\event\player\{PlayerJoinEvent, PlayerChatEvent, PlayerQuitEvent, PlayerInteractEvent, PlayerDeathEvent, PlayerItemHeldEvent};
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\utils\Config;
use pocketmine\item\Item;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\enchantment\{Enchantment, EnchantmentInstance};
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use libs\muqsit\invmenu\InvMenu;
use libs\muqsit\invmenu\InvMenuHandler;
use jojoe77777\FormAPI\SimpleForm;
use NoobMCBG\ToolLevels\PopupTask;

class Main extends PB implements L {

	public function onEnable(){
		@mkdir($this->getDataFolder());
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->money = $this->getServer()->getPluginManager()->getPlugin("EconomyAPI");
		$this->token = $this->getServer()->getPluginManager()->getPlugin("TokenAPI");
		$this->credits = $this->getServer()->getPluginManager()->getPlugin("CreditsAPI");
        $this->player = new Config($this->getDataFolder() . "player.yml", Config::YAML);
        $this->level = new Config($this->getDataFolder() . "level.yml", Config::YAML);
        $this->getLogger()->info("\n\n\n E la bồ pờ lúc gin :>>");
        $this->menutool = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
        if(!InvMenuHandler::isRegistered()){
            InvMenuHandler::register($this);
        }
	}

	public function onJoin(PlayerJoinEvent $ev){
		$player = $ev->getPlayer();
		$inv = $player->getInventory();
		if(!$this->player->get(strtolower($player->getName()))){
			$this->player->set(strtolower($player->getName()), ["Level" => 1, "exp" => 0, "nextexp" => 100]);
			$this->player->save();
			$item = Item::get(278, 0, 1);
			$name = $player->getName();
			$level = $this->getLevel($player);
			$item->setCustomName("§l§c•§e Dụng Cụ Của§b $name §c|§e Cấp Độ:§a $level §c•");
		}
		if(!$this->level->get(strtolower($player->getName()))){
			$this->level->set(strtolower($player->getName()), 1);
			$this->level->save();
		}
	}

	public function onQuit(PlayerQuitEvent $ev){
		$this->player->save();
		$this->level->save();
	}

	public function onDisable(){
		$this->player->save();
		$this->level->save();
	}

	public function onBreak(BlockBreakEvent $ev){
        $block = $ev->getBlock();
        $player = $ev->getPlayer();
        $level = $this->player->get(strtolower($player->getName()))["Level"];
        $exp = $this->player->get(strtolower($player->getName()))["exp"];
        $nextexp = $this->player->get(strtolower($player->getName()))["nextexp"];
        $name = $player->getName();
        if($player->getInventory()->getItemInHand()->getCustomName() == "§l§c•§e Dụng Cụ Của§b $name §c|§e Cấp Độ:§a $level §c•"){
            if($this->pickaxeleveling->get(strtolower($player->getName())) == "on"){
            	$pl = 5;
            }else{
            	$pl = 1;
            }
            switch($block->getId()){
            	case 56:// Kim Cương Ore
                    $this->player->set(strtolower($player->getName()), ["Level" => $level, "exp" => $exp+5*$pl, "nextexp" => $nextexp]);
                    $this->player->save();
                break;
                case 14:// Vàng Ore
                    $this->player->set(strtolower($player->getName()), ["Level" => $level, "exp" => $exp+4*$pl, "nextexp" => $nextexp]);
                    $this->player->save();
                break;
                case 15:// Sắt Ore
                    $this->player->set(strtolower($player->getName()), ["Level" => $level, "exp" => $exp+4*$pl, "nextexp" => $nextexp]);
                    $this->player->save();
                break;
                case 16:// Than Ore
                    $this->player->set(strtolower($player->getName()), ["Level" => $level, "exp" => $exp+4*$pl, "nextexp" => $nextexp]);
                    $this->player->save();
                break;
                case 129:// Emerald Ore
                    $this->player->set(strtolower($player->getName()), ["Level" => $level, "exp" => $exp+5*$pl, "nextexp" => $nextexp]);
                    $this->player->save();
                break;
                case 21:// Lapis Lazuli Ore
                    $this->player->set(strtolower($player->getName()), ["Level" => $level, "exp" => $exp+3*$pl, "nextexp" => $nextexp]);
                    $this->player->save();
                break;
                case 22:// Lapis Lazuli Block
                    $this->player->set(strtolower($player->getName()), ["Level" => $level, "exp" => $exp+6*$pl, "nextexp" => $nextexp]);
                    $this->player->save();
                break;
                case 133:// Emerald Block
                    $this->player->set(strtolower($player->getName()), ["Level" => $level, "exp" => $exp+8*$pl, "nextexp" => $nextexp]);
                    $this->player->save();
                    break;
                case 57:// Kim Cương Block
                    $this->player->set(strtolower($player->getName()), ["Level" => $level, "exp" => $exp+8*$pl, "nextexp" => $nextexp]);
                    $this->player->save();
                break;
                case 42:// Sắt Block
                    $this->player->set(strtolower($player->getName()), ["Level" => $level, "exp" => $exp+7*$pl, "nextexp" => $nextexp]);
                    $this->player->save();
                break;
                case 41:// Vàng Block
                    $this->player->set(strtolower($player->getName()), ["Level" => $level, "exp" => $exp+7*$pl, "nextexp" => $nextexp]);
                            $this->player->save();
                        break;
                default:// All Khối
                    $this->player->set(strtolower($player->getName()), ["Level" => $level, "exp" => $exp+2*$pl, "nextexp" => $nextexp]);
                    $this->player->save();
                break;
            }
            if($exp >= $nextexp){
			    $this->player->set(strtolower($player->getName()), ["Level" => $level+1, "exp" => 0, "nextexp" => $nextexp+100]);
                $this->player->save();
                $this->level->set(strtolower($player->getName()), $level+1);
                $this->level->save();
			    $money = $level * 1000;
			    $this->money->addMoney($player, $money);
			    $token = 1;
			    $this->token->addToken($player, $token);
			    if(in_array($level, array(100, 200, 300, 400, 500, 600, 700, 800, 900, 1000, 1100, 1200, 1300, 1400, 1500, 1600, 1700, 1800, 1900, 2000, 2100, 2200, 2300, 2400, 2500, 2600, 2700, 2800, 2900, 3000, 3100, 3200, 3300, 3400, 3500, 3600, 3700, 3800, 3900, 4000, 4100, 4200, 4300, 4400, 45000, 4600, 4700, 4800, 4900, 5000, 5100, 5200, 5300, 5400, 5500, 5600, 5700, 5800, 5900, 6000, 6100, 6200, 6300, 6400, 6500, 6600, 6700, 6800, 6900, 7000, 7100, 7200, 7300, 7400, 7500, 7600, 7700, 7800, 7900, 8000, 8100, 8200, 8300, 8400, 8500, 8600, 8700, 8800, 8900, 9000, 9100, 9200, 9300, 9400, 9500, 9600, 9700, 9800, 9900, 10000))){
                    $credits = 1;
                    $this->credits->addCredits($player, $credits);
			    }
			    $this->getServer()->broadcastMessage("§l§c•§e Dụng Cụ Của Người Chơi§b $name §eVừa Lên Cấp§a $level");
			    $player->sendMessage("§l§c•§e Chúc Mừng Dụng Cụ Của Bạn Đã Đạt Cấp§a $level");
			    $player->addTitle("§l§c•§e Dụng Cụ Cấp:§b $level §c•", "§l§9•§a Chúc Mừng Bạn Đã Lên Level §9•");
			}
            if($level == 50){
                $player->sendMessage("§l§c•§e Dụng Cụ Của Bạn Đã Được Cường Hóa Thành §bNetherite");
            }
        }
	}

	public function onUse(PlayerInteractEvent $ev){
		$player = $ev->getPlayer();
		$inv = $player->getInventory();
		$block = $ev->getBlock();
		$level = $this->player->get(strtolower($player->getName()))["Level"];
		$name = $player->getName();
		if($player->getInventory()->setItemInHand()->getCustomName() == "§l§c•§e Dụng Cụ Của§b $name §c|§e Cấp Độ:§a $level §c•"){
		    switch($block->getId()){
                case 17:
                    if($level >= 50){
                    	$item = Item::get(746, 0, 1);
                        $lv = $this->getLevel($player)/2.5;
                        $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(15), $lv));
                        $lv = $this->getLevel($player)/2.5;
                        $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(17), $lv));
                        $lv = $this->getLevel($player)/2.5;
                        $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(18), $lv));
                        $item->setCustomName("§l§c•§e Dụng Cụ Của§b $name §c|§e Cấp Độ:§a $level §c•");
                        $item->setDamage(0);
		    	        $player->getInventory()->setItemInHand($item);
                    }else{
                        $item = Item::get(279, 0, 1);
                        $lv = $this->getLevel($player)/2.5;
                        $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(15), $lv));
                        $lv = $this->getLevel($player)/2.5;
                        $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(17), $lv));
                        $lv = $this->getLevel($player)/2.5;
                        $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(18), $lv));
                        $item->setCustomName("§l§c•§e Dụng Cụ Của§b $name §c|§e Cấp Độ:§a $level §c•");
                        $item->setDamage(0);
		    	        $player->getInventory()->setItemInHand($item);
                    }
		    	break;
		    	case 3:
		    	    if($level >= 50){
                    	$item = Item::get(744, 0, 1);
                        $lv = $this->getLevel($player)/2.5;
                        $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(15), $lv));
                        $lv = $this->getLevel($player)/2.5;
                        $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(17), $lv));
                        $lv = $this->getLevel($player)/2.5;
                        $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(18), $lv));
                        $item->setCustomName("§l§c•§e Dụng Cụ Của§b $name §c|§e Cấp Độ:§a $level §c•");
                        $item->setDamage(0);
		    	        $player->getInventory()->setItemInHand($item);
                    }else{
                        $item = Item::get(277, 0, 1);
                        $lv = $this->getLevel($player)/2.5;
                        $i->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(15), $lv));
                        $lv = $this->getLevel($player)/2.5;
                        $i->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(17), $lv));
                        $lv = $this->getLevel($player)/2.5;
                        $i->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(18), $lv));
                        $item->setCustomName("§l§c•§e Dụng Cụ Của§b $name §c|§e Cấp Độ:§a $level §c•");
                        $item->setDamage(0);
		    	        $player->getInventory()->setItemInHand($item);
                    }
                break;
                case 2:
		    	    if($level >= 50){
                    	$item = Item::get(744, 0, 1);
                        $lv = $this->getLevel($player)/2.5;
                        $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(15), $lv));
                        $lv = $this->getLevel($player)/2.5;
                        $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(17), $lv));
                        $lv = $this->getLevel($player)/2.5;
                        $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(18), $lv));
                        $item->setCustomName("§l§c•§e Dụng Cụ Của§b $name §c|§e Cấp Độ:§a $level §c•");
                        $item->setDamage(0);
		    	        $player->getInventory()->setItemInHand($item);
                    }else{
                        $item = Item::get(277, 0, 1);
                        $lv = $this->getLevel($player)/2.5;
                        $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(15), $lv));
                        $lv = $this->getLevel($player)/2.5;
                        $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(17), $lv));
                        $lv = $this->getLevel($player)/2.5;
                        $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(18), $lv));
                        $item->setCustomName("§l§c•§e Dụng Cụ Của§b $name §c|§e Cấp Độ:§a $level §c•");
                        $item->setDamage(0);
		    	        $player->getInventory()->setItemInHand($item);
                    }
                break;
                case 3:
		    	    if($level >= 50){
                    	$item = Item::get(744, 0, 1);
                        $lv = $this->getLevel($player)/2.5;
                        $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(15), $lv));
                        $lv = $this->getLevel($player)/2.5;
                        $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(17), $lv));
                        $lv = $this->getLevel($player)/2.5;
                        $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(18), $lv));
                        $item->setCustomName("§l§c•§e Dụng Cụ Của§b $name §c|§e Cấp Độ:§a $level §c•");
                        $item->setDamage(0);
		    	        $player->getInventory()->setItemInHand($item);
                    }else{
                        $item = Item::get(277, 0, 1);
                        $lv = $this->getLevel($player)/2.5;
                        $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(15), $lv));
                        $lv = $this->getLevel($player)/2.5;
                        $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(17), $lv));
                        $lv = $this->getLevel($player)/2.5;
                        $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(18), $lv));
                        $item->setCustomName("§l§c•§e Dụng Cụ Của§b $name §c|§e Cấp Độ:§a $level §c•");
                        $item->setDamage(0);
		    	        $player->getInventory()->setItemInHand($item);
                    }
                break;
                default:
		    	    if($level >= 50){
                    	$item = Item::get(745, 0, 1);
                        $lv = $this->getLevel($player)/2.5;
                        $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(15), $lv));
                        $lv = $this->getLevel($player)/2.5;
                        $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(17), $lv));
                        $lv = $this->getLevel($player)/2.5;
                        $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(18), $lv));
                        $item->setCustomName("§l§c•§e Dụng Cụ Của§b $name §c|§e Cấp Độ:§a $level §c•");
                        $item->setDamage(0);
		    	        $player->getInventory()->setItemInHand($item);
                    }else{
                        $item = Item::get(278, 0, 1);
                        $lv = $this->getLevel($player)/2.5;
                        $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(15), $lv));
                        $lv = $this->getLevel($player)/2.5;
                        $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(17), $lv));
                        $lv = $this->getLevel($player)/2.5;
                        $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(18), $lv));
                        $item->setCustomName("§l§c•§e Dụng Cụ Của§b $name §c|§e Cấp Độ:§a $level §c•");
                        $item->setDamage(0);
		    	        $player->getInventory()->setItemInHand($item);
                    }
                break;
		    }
		    if($event->getAction() !== PlayerInteractEvent::RIGHT_CLICK_AIR){
                if($level >= 50){
                	$item = Item::get(743, 0, 1);
                    $lv = $this->getLevel($player)/2.5;
                    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(15), $lv));
                    $lv = $this->getLevel($player)/2.5;
                    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(17), $lv));
                    $lv = $this->getLevel($player)/2.5;
                    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(18), $lv));
                    $lv = $this->getLevel($player)/2.5;
                    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(9), $lv));
                    $item->setCustomName("§l§c•§e Dụng Cụ Của§b $name §c|§e Cấp Độ:§a $level §c•");
                    $item->setDamage(0);
		    	    $player->getInventory()->setItemInHand($item);
                }else{
                    $item = Item::get(276, 0, 1);
                    $lv = $this->getLevel($player)/2.5;
                    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(15), $lv));
                    $lv = $this->getLevel($player)/2.5;
                    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(17), $lv));
                    $lv = $this->getLevel($player)/2.5;
                    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(18), $lv));
                    $lv = $this->getLevel($player)/2.5;
                    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(9), $lv));
                    $item->setCustomName("§l§c•§e Dụng Cụ Của§b $name §c|§e Cấp Độ:§a $level §c•");
                    $item->setDamage(0);
		    	    $player->getInventory()->setItemInHand($item);
                }
            }
		}
	}

    public function onDeath(PlayerDeathEvent $event){
		$player = $event->getPlayer();
		$cause = $player->getLastDamageCause();
		$level = $this->player->get(strtolower($player->getName()))["Level"];
        $exp = $this->player->get(strtolower($player->getName()))["exp"];
        $nextexp = $this->player->get(strtolower($player->getName()))["nextexp"];
        $item = $player->getInventory()->getItemInHand();
        $name = $player->getName();
		if($cause instanceof EntityDamageByEntityEvent){
			$damager = $cause->getDamager();
			if($damager instanceof Player){
                if($item->getCustomName() === "§l§c•§e Dụng Cụ Của§b $name §c|§e Cấp Độ:§a $level §c•"){
				    $this->player->set(strtolower($player->getName()), ["Level" => $level, "exp" => $exp+10, "nextexp" => $nextexp]);
                    $this->player->save();
                }
			}
			if($exp >= $nextexp){
			    $this->player->set(strtolower($player->getName()), ["Level" => $level+1, "exp" => 0, "nextexp" => $nextexp+100]);
                $this->player->save();
                $this->level->set(strtolower($player->getName()), $level+1);
                $this->level->save();
			    $money = $level * 1000;
			    $this->money->addMoney($player, $money);
			    $token = 1;
			    $this->token->addToken($player, $token);
			    if(in_array($level, array(100, 200, 300, 400, 500, 600, 700, 800, 900, 1000, 1100, 1200, 1300, 1400, 1500, 1600, 1700, 1800, 1900, 2000, 2100, 2200, 2300, 2400, 2500, 2600, 2700, 2800, 2900, 3000, 3100, 3200, 3300, 3400, 3500, 3600, 3700, 3800, 3900, 4000, 4100, 4200, 4300, 4400, 45000, 4600, 4700, 4800, 4900, 5000, 5100, 5200, 5300, 5400, 5500, 5600, 5700, 5800, 5900, 6000, 6100, 6200, 6300, 6400, 6500, 6600, 6700, 6800, 6900, 7000, 7100, 7200, 7300, 7400, 7500, 7600, 7700, 7800, 7900, 8000, 8100, 8200, 8300, 8400, 8500, 8600, 8700, 8800, 8900, 9000, 9100, 9200, 9300, 9400, 9500, 9600, 9700, 9800, 9900, 10000))){
                    $credits = 1;
                    $this->credits->addCredits($player, $credits);
			    }
			    $this->getServer()->broadcastMessage("§l§c•§e Dụng Cụ Của Người Chơi§b $name §eVừa Lên Cấp§a $level");
			    $player->sendMessage("§l§c•§e Chúc Mừng Dụng Cụ Của Bạn Đã Đạt Cấp§a $level");
                $player->addTitle("§l§c•§e Dụng Cụ Cấp:§b $level §c•", "§l§9•§a Chúc Mừng Bạn Đã Lên Level §9•");
			}
            if($level == 50){
                $player->sendMessage("§l§c•§e Dụng Cụ Của Bạn Đã Được Cường Hóa Thành §bNetherite");
            }
		}
	}

	public function onItemHeld(PlayerItemHeldEvent $ev){
        $task = new PopupTask($this, $ev->getPlayer());
        $this->tasks[$ev->getPlayer()->getId()] = $task;
        $this->getScheduler()->scheduleRepeatingTask($task, 20);
    }

    public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args) : bool {
    	switch($cmd->getName()){
    		case "toollevel":
    		    if(!$sender instanceof Player){
    		    	$sender->sendMessage("§l§c•§e Hãy Sử Dụng Lệnh Trong Trò Chơi !");
    		    	return true;
    		    }else{
    		    	$this->MenuToolLevel($sender);
    		    }
    		break;
    	}
    	return true;
    }

    public function MenuToolLevel($player){
    	$this->menutool->readonly();
        $this->menutool->setListener([$this, "MenuToolLevelListener"]);
        $this->menutool->setName("       §l§c•§9 Menu Tool Levels §c•");
        $inventory = $this->menutool->getInventory();
        $inventory->setItem(0, Item::get(160, 14, 1)->setCustomName("§r"));
        $inventory->setItem(1, Item::get(160, 14, 1)->setCustomName("§r"));
        $inventory->setItem(2, Item::get(160, 14, 1)->setCustomName("§r"));
        $inventory->setItem(3, Item::get(160, 14, 1)->setCustomName("§r"));
        $inventory->setItem(4, Item::get(160, 14, 1)->setCustomName("§r"));
        $inventory->setItem(5, Item::get(160, 14, 1)->setCustomName("§r"));
        $inventory->setItem(6, Item::get(160, 14, 1)->setCustomName("§r"));
        $inventory->setItem(7, Item::get(160, 14, 1)->setCustomName("§r"));
        $inventory->setItem(8, Item::get(160, 14, 1)->setCustomName("§r"));
        $inventory->setItem(9, Item::get(160, 14, 1)->setCustomName("§r"));
        $inventory->setItem(10, Item::get(160, 14, 1)->setCustomName("§r"));
        $inventory->setItem(11, Item::get(160, 14, 1)->setCustomName("§r"));
        $inventory->setItem(12, Item::get(160, 14, 1)->setCustomName("§r"));
        $inventory->setItem(13, Item::get(160, 14, 1)->setCustomName("§r"));
        $inventory->setItem(14, Item::get(160, 14, 1)->setCustomName("§r"));
        $inventory->setItem(15, Item::get(160, 14, 1)->setCustomName("§r"));
        $inventory->setItem(16, Item::get(160, 14, 1)->setCustomName("§r"));
        $inventory->setItem(17, Item::get(160, 14, 1)->setCustomName("§r"));
        $inventory->setItem(18, Item::get(160, 14, 1)->setCustomName("§r"));
        $inventory->setItem(19, Item::get(160, 14, 1)->setCustomName("§r"));
        $inventory->setItem(20, Item::get(745, 0, 1)->setCustomName("§l§c•§e Nhận Dụng Cụ §c•")->setLore(["§l§c•§e Ném Hoặc Nhấn Để Nhận Dụng Cụ !"]));
        $inventory->setItem(21, Item::get(160, 14, 1)->setCustomName("§r"));
        $inventory->setItem(22, Item::get(160, 14, 1)->setCustomName("§r"));
        $inventory->setItem(23, Item::get(160, 14, 1)->setCustomName("§r"));
        $inventory->setItem(24, Item::get(743, 0, 1)->setCustomName("§l§c•§e TOP Dụng Cụ §c•")->setLore(["§l§c•§e Ném Hoặc Nhấn Để Mở Menu TOP Dụng Cụ !"]));
        $inventory->setItem(25, Item::get(160, 14, 1)->setCustomName("§r"));
        $inventory->setItem(26, Item::get(160, 14, 1)->setCustomName("§r"));
        $inventory->setItem(27, Item::get(160, 14, 1)->setCustomName("§r"));
        $inventory->setItem(28, Item::get(160, 14, 1)->setCustomName("§r"));
        $inventory->setItem(29, Item::get(160, 14, 1)->setCustomName("§r"));
        $inventory->setItem(30, Item::get(160, 14, 1)->setCustomName("§r"));
        $inventory->setItem(31, Item::get(339, 0, 1)->setCustomName("§l§c•§e Cách Sử Dụng §c•")->setLore(["§l§c•§e Ném Hoặc Nhấn Để Xem Cách Sử Dụng !"]));
        $inventory->setItem(32, Item::get(160, 14, 1)->setCustomName("§r"));
        $inventory->setItem(33, Item::get(160, 14, 1)->setCustomName("§r"));
        $inventory->setItem(34, Item::get(160, 14, 1)->setCustomName("§r"));
        $inventory->setItem(35, Item::get(160, 14, 1)->setCustomName("§r"));
        $inventory->setItem(36, Item::get(160, 14, 1)->setCustomName("§r"));
        $inventory->setItem(37, Item::get(160, 14, 1)->setCustomName("§r"));
        $inventory->setItem(38, Item::get(160, 14, 1)->setCustomName("§r"));
        $inventory->setItem(39, Item::get(160, 14, 1)->setCustomName("§r"));
        $inventory->setItem(40, Item::get(152, 0, 1)->setCustomName("§l§c•§e Thoát Menu ToolLevels §c•")->setLore(["§l§c•§e Ném Hoặc Nhấn Để Thoát !"]));
        $inventory->setItem(41, Item::get(160, 14, 1)->setCustomName("§r"));
        $inventory->setItem(42, Item::get(160, 14, 1)->setCustomName("§r"));
        $inventory->setItem(43, Item::get(160, 14, 1)->setCustomName("§r"));
        $inventory->setItem(44, Item::get(160, 14, 1)->setCustomName("§r"));
        $inventory->setItem(45, Item::get(160, 14, 1)->setCustomName("§r"));
        $inventory->setItem(46, Item::get(160, 14, 1)->setCustomName("§r"));
        $inventory->setItem(47, Item::get(160, 14, 1)->setCustomName("§r"));
        $inventory->setItem(48, Item::get(160, 14, 1)->setCustomName("§r"));
        $inventory->setItem(49, Item::get(160, 14, 1)->setCustomName("§r"));
        $inventory->setItem(50, Item::get(160, 14, 1)->setCustomName("§r"));
        $inventory->setItem(51, Item::get(160, 14, 1)->setCustomName("§r"));
        $inventory->setItem(52, Item::get(160, 14, 1)->setCustomName("§r"));
        $inventory->setItem(53, Item::get(160, 14, 1)->setCustomName("§r"));
        $this->menutool->send($player);
    }

    public function MenuToolLevelListener(Player $player, Item $item){
    	$inventory = $this->menutool->getInventory();
    	if($item->getCustomName() === "§l§c•§e Thoát Menu ToolLevels §c•"){
            $player->removeWindow($inventory);
		    $packet = new PlaySoundPacket();
		    $packet->soundName = "random.explode";
		    $packet->x = $player->getX();
		    $packet->y = $player->getY();
		    $packet->z = $player->getZ();
		    $packet->volume = 1;
		    $packet->pitch = 1;
		    $player->sendDataPacket($packet);
        }
        if($item->getCustomName() === "§l§c•§e Nhận Dụng Cụ §c•"){
        	$player->removeWindow($inventory);
        	$this->NhanDungCu($player);
        }
        if($item->getCustomName() === "§l§c•§e TOP Dụng Cụ §c•"){
        	$player->removeWindow($inventory);
        	$this->TopDungCu($player);
        }
        if($item->getCustomName() === "§l§c•§e Cách Sử Dụng §c•"){
        	$player->removeWindow($inventory);
        	$this->MenuCachSuDung($player);
        }
    }

    public function NhanDungCu($player){
    	$inv = $player->getInventory();
    	$name = $player->getName();
    	$level = $this->player->get(strtolower($player->getName()))["Level"];
    	switch(mt_rand(1, 4)){
    		case 1:
    		    if($level >= 50){
    		    	$item = Item::get(745, 0, 1);
    		    	$lv = $this->getLevel($player)/2.5;
                    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(15), $lv));
                    $lv = $this->getLevel($player)/2.5;
                    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(17), $lv));
                    $lv = $this->getLevel($player)/2.5;
                    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(18), $lv));
    		    	$item->setCustomName("§l§c•§e Dụng Cụ Của§b $name §c|§e Cấp Độ:§a $level §c•");
    		    	$inv->addItem($item);
    		    	$packet = new PlaySoundPacket();
		            $packet->soundName = "random.levelup";
		            $packet->x = $player->getX();
		            $packet->y = $player->getY();
		            $packet->z = $player->getZ();
		            $packet->volume = 1;
		            $packet->pitch = 1;
		            $player->sendDataPacket($packet);
    		    }else{
    		    	$item = Item::get(278, 0, 1);
    		    	$lv = $this->getLevel($player)/2.5;
                    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(15), $lv));
                    $lv = $this->getLevel($player)/2.5;
                    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(17), $lv));
                    $lv = $this->getLevel($player)/2.5;
                    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(18), $lv));
    		    	$item->setCustomName("§l§c•§e Dụng Cụ Của§b $name §c|§e Cấp Độ:§a $level §c•");
    		    	$inv->addItem($item);
    		    	$packet = new PlaySoundPacket();
		            $packet->soundName = "random.levelup";
		            $packet->x = $player->getX();
		            $packet->y = $player->getY();
		            $packet->z = $player->getZ();
		            $packet->volume = 1;
		            $packet->pitch = 1;
		            $player->sendDataPacket($packet);
    		    }
    		break;
    		case 2:
    		    if($level >= 50){
    		    	$item = Item::get(746, 0, 1);
    		    	$lv = $this->getLevel($player)/2.5;
                    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(15), $lv));
                    $lv = $this->getLevel($player)/2.5;
                    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(17), $lv));
                    $lv = $this->getLevel($player)/2.5;
                    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(18), $lv));
    		    	$item->setCustomName("§l§c•§e Dụng Cụ Của§b $name §c|§e Cấp Độ:§a $level §c•");
    		    	$inv->addItem($item);
    		    	$packet = new PlaySoundPacket();
		            $packet->soundName = "random.levelup";
		            $packet->x = $player->getX();
		            $packet->y = $player->getY();
		            $packet->z = $player->getZ();
		            $packet->volume = 1;
		            $packet->pitch = 1;
		            $player->sendDataPacket($packet);
    		    }else{
    		    	$item = Item::get(279, 0, 1);
    		    	$lv = $this->getLevel($player)/2.5;
                    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(15), $lv));
                    $lv = $this->getLevel($player)/2.5;
                    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(17), $lv));
                    $lv = $this->getLevel($player)/2.5;
                    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(18), $lv));
    		    	$item->setCustomName("§l§c•§e Dụng Cụ Của§b $name §c|§e Cấp Độ:§a $level §c•");
    		    	$inv->addItem($item);
    		    	$packet = new PlaySoundPacket();
		            $packet->soundName = "random.levelup";
		            $packet->x = $player->getX();
		            $packet->y = $player->getY();
		            $packet->z = $player->getZ();
		            $packet->volume = 1;
		            $packet->pitch = 1;
		            $player->sendDataPacket($packet);
    		    }
    		break;
    		case 3:
    		   if($level >= 50){
    		    	$item = Item::get(744, 0, 1);
    		    	$lv = $this->getLevel($player)/2.5;
                    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(15), $lv));
                    $lv = $this->getLevel($player)/2.5;
                    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(17), $lv));
                    $lv = $this->getLevel($player)/2.5;
                    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(18), $lv));
    		    	$item->setCustomName("§l§c•§e Dụng Cụ Của§b $name §c|§e Cấp Độ:§a $level §c•");
    		    	$inv->addItem($item);
    		    	$packet = new PlaySoundPacket();
		            $packet->soundName = "random.levelup";
		            $packet->x = $player->getX();
		            $packet->y = $player->getY();
		            $packet->z = $player->getZ();
		            $packet->volume = 1;
		            $packet->pitch = 1;
		            $player->sendDataPacket($packet);
    		    }else{
    		    	$item = Item::get(277, 0, 1);
    		    	$lv = $this->getLevel($player)/2.5;
                    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(15), $lv));
                    $lv = $this->getLevel($player)/2.5;
                    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(17), $lv));
                    $lv = $this->getLevel($player)/2.5;
                    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(18), $lv));
    		    	$item->setCustomName("§l§c•§e Dụng Cụ Của§b $name §c|§e Cấp Độ:§a $level §c•");
    		    	$inv->addItem($item);
    		    	$packet = new PlaySoundPacket();
		            $packet->soundName = "random.levelup";
		            $packet->x = $player->getX();
		            $packet->y = $player->getY();
		            $packet->z = $player->getZ();
		            $packet->volume = 1;
		            $packet->pitch = 1;
		            $player->sendDataPacket($packet);
    		    }
    		break;
    		case 4:
    		    if($level >= 50){
    		    	$item = Item::get(743, 0, 1);
    		    	$lv = $this->getLevel($player)/2.5;
                    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(15), $lv));
                    $lv = $this->getLevel($player)/2.5;
                    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(17), $lv));
                    $lv = $this->getLevel($player)/2.5;
                    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(18), $lv));
                    $lv = $this->getLevel($player)/2.5;
                    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(9), $lv));
    		    	$item->setCustomName("§l§c•§e Dụng Cụ Của§b $name §c|§e Cấp Độ:§a $level §c•");
    		    	$inv->addItem($item);
    		    	$packet = new PlaySoundPacket();
		            $packet->soundName = "random.levelup";
		            $packet->x = $player->getX();
		            $packet->y = $player->getY();
		            $packet->z = $player->getZ();
		            $packet->volume = 1;
		            $packet->pitch = 1;
		            $player->sendDataPacket($packet);
    		    }else{
    		    	$item = Item::get(276, 0, 1);
    		    	$lv = $this->getLevel($player)/2.5;
                    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(15), $lv));
                    $lv = $this->getLevel($player)/2.5;
                    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(17), $lv));
                    $lv = $this->getLevel($player)/2.5;
                    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(18), $lv));
                    $lv = $this->getLevel($player)/2.5;
                    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(9), $lv));
    		    	$item->setCustomName("§l§c•§e Dụng Cụ Của§b $name §c|§e Cấp Độ:§a $level §c•");
    		    	$inv->addItem($item);
    		    	$packet = new PlaySoundPacket();
		            $packet->soundName = "random.levelup";
		            $packet->x = $player->getX();
		            $packet->y = $player->getY();
		            $packet->z = $player->getZ();
		            $packet->volume = 1;
		            $packet->pitch = 1;
		            $player->sendDataPacket($packet);
    		    }
    		break;
    	}
    }

    public function TopDungCu($player){
		$lv = $this->level->getAll();
		$message = "";
		$message1 = "";
		if(count($lv) > 0){
			arsort($lv);
			$i = 1;
			foreach($lv as $name => $level){
				$message .= "§l§c•§e TOP§d " . $i . " §b" . $name . " §c→§f Cấp§a " . $level . "\n";
				if($name == $player->getName())$xh=$i;
				if($i == 1000)break;
				++$i;
			}
		}
		$form = new SimpleForm(function(Player $player, $data){
			if($data == null){
				return true;
		    }
		    switch($data){
		    	case 0:
		    	    $packet = new PlaySoundPacket();
		            $packet->soundName = "random.click";
		            $packet->x = $player->getX();
		            $packet->y = $player->getY();
		            $packet->z = $player->getZ();
		            $packet->volume = 1;
		            $packet->pitch = 1;
		            $player->sendDataPacket($packet);
		    	break;
		    }
		});
		$form->setTitle("§l§c•§9 Menu Dụng Cụ §c•");
		$form->setContent("§l§c•§e TOP Dụng Cụ Trong Server:\n$message ");
		$form->addButton("§l§c•§9 Thoát Menu §c•", 0, "textures/other/exit");
		$form->sendToPlayer($player);
		return true;
    }

    public function MenuCachSuDung($player){
    	$form = new SimpleForm(function(Player $player, $data){
    		if($data == null){
    			return true;
    		}
    		switch($data){
    			case 0:
    			    $packet = new PlaySoundPacket();
		            $packet->soundName = "random.click";
		            $packet->x = $player->getX();
		            $packet->y = $player->getY();
		            $packet->z = $player->getZ();
		            $packet->volume = 1;
		            $packet->pitch = 1;
		            $player->sendDataPacket($packet);
    			break;
    		}
    	});
    	$form->setTitle("§l§c•§9 Menu Dụng Cụ §c•");
    	$form->setContent("§l§c•§e Cách Sử Dụng §aDụng Cụ:\n§l§c•§e Đào Hoặc Ghết Người Để Lên Level Dụng Cụ\n§l§c•§e Khi Dụng Cụ Lên Level 50, Chúng Sẽ Cường hóa Thành Đồ §bNetherite\n§l§c•§e Khi Đủ Level Theo Dụng Cụ, Dụng Cụ Sẽ Mở Khóa Các Skill !\n§l§c•§e Dụng Cụ Có Khả Năng Biến Đổi Cực Chất\n§l§c•§e Khi Lên Level Bạn Sẽ Nhận Được Nhiều Phần Thưởng Lớn !\n§l§c•§e Cứ Mỗi 100 Level Bạn Sẽ Nhận Được§f 1 Credits\n§l§c•§e Khi Xài Skill, Bạn Chỉ Việc Đào, Giữ, Hoặc Nhấn Xuống Đất !");
    	$form->addButton("§l§c•§9 Thoát Menu §c•");
    }

    public function getLevel($player){
        if($player instanceof Player){
           $name = $player->getName();
        }
        $level = $this->player->get(strtolower($player->getName()))["Level"];
        return $level;
    }

    public function getExp($player){
        if($player instanceof Player){
            $name = $player->getName();
        }

        $exp = $this->player->get(strtolower($player->getName()))["exp"];
        return $exp;
    }

    public function getNextExp($player){
        if($player instanceof Player){
            $name = $player->getName();
        }

        $nextexp = $this->player->get(strtolower($player->getName()))["nextexp"];
        return $nextexp;
    }

    public function CapDungCu($player){
        $lv = $this->level->get(strtolower($player->getName()));
        $cap = "Dụng Cụ Thường";
        if($lv >= 50) $cap = "Dụng Cụ Ma Lôi";
        if($lv >= 100) $cap = "Dụng Cụ Ma Vương";
        if($lv >= 150) $cap = "Dụng Cụ Thiên Vương";
        if($lv >= 200) $cap = "Dụng Cụ Thiên Lôi";
        if($lv >= 250) $cap = "Dụng Cụ Thủy Vương";
        if($lv >= 300) $cap = "Dụng Cụ Hỏa Vương";
        if($lv >= 350) $cap = "Dụng Cụ Thần Vương";
        if($lv >= 400) $cap = "Dụng Cụ Thánh Vương";
        if($lv >= 500) $cap = "Dụng Cụ Diêm Vương";
        return $cap;
    }
}