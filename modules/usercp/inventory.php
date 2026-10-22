<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */
?>
<div class="page-header-block usercp"></div>
<br /><br />

<h3>View Inventory</h3>

<?php
try {
	
	# load server database
	$sdb = Handler::loadDB('siel');
	
	# load web database (für die itemlist)
	$db = Handler::loadDB();
	
	# player data
	$playerData = $sdb->queryFetchSingle("SELECT * FROM `players` WHERE `account_id` = ? AND `name` = ?", array($_SESSION['userid'], $_GET['player']));
	if(!is_array($playerData)) throw new Exception('Your request could not be completed, please try again later. [2]');
	
	echo '<p>You are now viewing <strong>'.$playerData['name'].'\'s</strong> inventory. For the complete list of enchantable items you may <a href="'.module_url('usercp/enchantable/', true).'">click here</a>.</p>';
	echo '<br /><br />';
	
	# player inventory (non-equipped)
	$playerInventory = $sdb->queryFetch("SELECT * FROM `inventory` WHERE `item_owner` = ? AND `is_equiped` = ?", array($playerData['id'], 0));
	
	# player inventory (equipped)
	$playerInventoryEquipped = $sdb->queryFetch("SELECT * FROM `inventory` WHERE `item_owner` = ? AND `is_equiped` = ?", array($playerData['id'], 1));

	# --- STYLES FÜR DAS AION INVENTAR ---
	echo '<style>
	.aion-equipped-wrapper {
		display: flex;
		flex-direction: column;
		align-items: center;
		background: #111;
		padding: 20px;
		border-radius: 10px;
		max-width: 600px;
		margin: 0 auto;
		box-shadow: 0 0 15px rgba(0,0,0,0.5);
	}
	.aion-slot {
		width: 50px;
		height: 50px;
		background-color: #222;
		border: 1px solid #444;
		border-radius: 4px;
		display: flex;
		justify-content: center;
		align-items: center;
		position: relative;
		cursor: pointer;
		transition: all 0.2s;
	}
	.aion-slot:hover {
		border-color: #C99B4B;
		box-shadow: 0 0 8px #C99B4B;
	}
	.aion-slot img {
		max-width: 44px;
		max-height: 44px;
	}
	.aion-slot .enchant {
		position: absolute;
		bottom: -5px;
		right: -5px;
		background: #c00;
		color: #fff;
		font-size: 10px;
		font-weight: bold;
		padding: 1px 4px;
		border-radius: 3px;
		border: 1px solid #fff;
	}
	.aion-slot .temper {
		position: absolute;
		bottom: -5px;
		left: -5px;
		background: #e6a300;
		color: #fff;
		font-size: 10px;
		font-weight: bold;
		padding: 1px 4px;
		border-radius: 3px;
		border: 1px solid #fff;
	}
	.aion-grid-top {
		display: grid;
		grid-template-columns: repeat(5, 50px);
		gap: 10px;
		justify-content: center;
		margin-bottom: 20px;
	}
	.aion-grid-main {
		display: grid;
		grid-template-columns: 50px 50px 80px 50px 50px;
		gap: 10px;
		align-items: center;
	}
	.aion-left-col, .aion-right-col {
		display: flex;
		flex-direction: column;
		gap: 10px;
	}
	.aion-center {
		display: flex;
		justify-content: center;
		align-items: center;
		height: 100%;
	}
	.aion-center img {
		width: 80px;
		opacity: 0.2; /* Silhouette-Effekt */
	}
	</style>';

	# --- EQUIPPED ITEMS ANZEIGE ---
	if (is_array($playerInventoryEquipped)) {
		echo '<h4 style="margin-bottom: 15px; font-size: 18px; color: #000; text-align: center;">Equipped Items:</h4>';
		
		# Hilfsfunktion zum Erzeugen eines Slots (nutzt jetzt $db für die itemlist)
		$renderSlot = function($itemInfo) use ($db) {
			$itemId = $itemInfo['item_id'];
			$itemName = getItemName($itemId);
			if (!check($itemName)) {
				aioncmsBuildAionItemList($itemId);
				$itemName = 'Unknown';
			}
			
			# Icon aus der aioncms-Datenbank holen (wie im Shop)
			$itemIcon = '';
			$itemInfoDetails = $db->queryFetchSingle("SELECT `item_icon` FROM `aion_itemlist` WHERE `item_id` = ?", array($itemId));
			
			if (is_array($itemInfoDetails) && check($itemInfoDetails['item_icon'])) {
				$itemIcon = 'https://aioncodex.com/items/'.$itemInfoDetails['item_icon'].'.png';
			} else {
				# Fallback: AionPowerbook falls aioncodex nichts hat
				$itemIcon = 'https://aionpowerbook.com/item/'.$itemId.'.png';
			}

			$ench = $itemInfo['enchant'] > 0 ? '<span class="enchant">+'.$itemInfo['enchant'].'</span>' : '';
			$temp = $itemInfo['authorize'] > 0 ? '<span class="temper">+'.$itemInfo['authorize'].'</span>' : '';

			return '<div class="aion-slot" title="'.$itemName.'">
						<img src="'.$itemIcon.'" alt="'.$itemName.'" onerror="this.style.display=\'none\'" />
						'.$ench.$temp.'
					</div>';
		};
		
		echo '<div class="aion-equipped-wrapper">';
		
			# Obere Reihe (Waffe, Flügel, etc.)
			echo '<div class="aion-grid-top">';
			for ($k = 0; $k < 5; $k++) {
				if (isset($playerInventoryEquipped[$k])) {
					echo $renderSlot($playerInventoryEquipped[$k]);
				} else {
					echo '<div class="aion-slot"></div>';
				}
			}
			echo '</div>';
			
			# Haupt-Grid (Links, Mitte, Rechts)
			echo '<div class="aion-grid-main">';
				
				# Linke Spalte (Rüstung)
				echo '<div class="aion-left-col">';
				for ($k = 5; $k < 10; $k++) {
					if (isset($playerInventoryEquipped[$k])) {
						echo $renderSlot($playerInventoryEquipped[$k]);
					} else {
						echo '<div class="aion-slot"></div>';
					}
				}
				echo '</div>';

				# Linke Innere Spalte
				echo '<div class="aion-left-col">';
				for ($k = 10; $k < 15; $k++) {
					if (isset($playerInventoryEquipped[$k])) {
						echo $renderSlot($playerInventoryEquipped[$k]);
					} else {
						echo '<div class="aion-slot"></div>';
					}
				}
				echo '</div>';
				
				# Mitte (Charakter Silhouette)
				echo '<div class="aion-center">';
					echo '<img src="'.template_img(true).'char_silhouette.png" alt="Character" onerror="this.style.display=\'none\'" />';
				echo '</div>';
				
				# Rechte Innere Spalte
				echo '<div class="aion-right-col">';
				for ($k = 15; $k < 20; $k++) {
					if (isset($playerInventoryEquipped[$k])) {
						echo $renderSlot($playerInventoryEquipped[$k]);
					} else {
						echo '<div class="aion-slot"></div>';
					}
				}
				echo '</div>';

				# Rechte Spalte (Rüstung)
				echo '<div class="aion-right-col">';
				for ($k = 20; $k < 25; $k++) {
					if (isset($playerInventoryEquipped[$k])) {
						echo $renderSlot($playerInventoryEquipped[$k]);
					} else {
						echo '<div class="aion-slot"></div>';
					}
				}
				echo '</div>';
				
			echo '</div>';
			
		echo '</div>';
		echo '<br /><br />';
	}

	# --- NON-EQUIPPED ITEMS (Bleibt wie vorher) ---
	#$enchantableItems = config('enchantable_items', true);
	#$enchantablePrice = config('enchant_price', true);
		
/*	if(is_array($playerInventory)) {
		
		echo '<br /><br />';
		echo '<h4>Non-Equipped Items:</h4>';
		echo '<table class="inventory-table">';
		foreach($playerInventory as $itemInfo) {
			$itemName = getItemName($itemInfo['item_id']);
			
			if(!check($itemName)) {
				aioncmsBuildAionItemList($itemInfo['item_id']);
				$itemName = 'Unknown';
			}
			
			$itemEnchantment = ($itemInfo['enchant'] > 0 ? ' <span style="color:red;font-weight:bold;">+'.$itemInfo['enchant'].'</span>' : null);
			$itemTempering = ($itemInfo['authorize'] > 0 ? ' <span style="color:orange;font-weight:bold;">+'.$itemInfo['authorize'].'</span>' : null);
			
			echo '<tr>';
				echo '<td>'.number_format($itemInfo['item_count']).'x</td>';
				echo '<td>';
					echo '<a href="'.aionDatabaseLink($itemInfo['item_id']).'" target="_blank">'.$itemName.'</a>'.$itemEnchantment.$itemTempering.'';
					
					if(array_key_exists($itemInfo['item_id'], $enchantableItems)) {
						if(array_key_exists($itemInfo['enchant'], $enchantablePrice)) {
							echo ' <a href="'.module_url('usercp/enchant/server/'.$_GET['server'].'/player/'.$playerData['name'].'/item/'.$itemInfo['item_unique_id'].'/', true).'" class="btn btn-xs btn-primary">Enchant</a>';
						}
						
						if($itemInfo['enchant'] == 20) {
							echo ' <a href="'.module_url('usercp/changeskill/server/'.$_GET['server'].'/player/'.$playerData['name'].'/item/'.$itemInfo['item_unique_id'].'/', true).'" class="btn btn-xs btn-default">Change Skill</a>';
						}
					}
				echo '</td>';
			echo '</tr>';
		}
		echo '</table>';
	}*/
	
} catch(Exception $ex) {
	die($ex->getMessage());
}
?>