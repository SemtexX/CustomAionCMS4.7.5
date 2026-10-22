<?php
/**
 * BattlePass System for AionCMS
 * Mirrors the structure of lib.shop.php
 */

class BattlePass
{
    private $_db;              // aioncms DB (website)
    private $_sdb;             // siel DB (players/inventory/mail)
    private $_account;         // Account object
    private $_accountData;
    private $_userId;

    private $_currentSeason;
    private $_tiers = [];
    private $_progress = null;

    private $_itemDetailUrl;
    private $_bpUrl;

    private $_uniqueItemIdIncrementValue = 190000000;

    public function __construct()
    {
        $this->_db = Handler::loadDB();
        $this->_bpUrl = __PAGE_URL__ . 'battlepass/';
        $this->_itemDetailUrl = __PAGE_URL__ . 'shop/item/id/';

        // Load active season (if any)
        $this->_loadCurrentSeason();
    }

    // =========================================================
    //  USER / ACCOUNT
    // =========================================================

    public function setUserId($userId)
    {
        if (!Validator::UnsignedNumber($userId)) throw new Exception('Invalid user id.');
        $this->_userId = (int)$userId;

        $this->_account = new Account();
        $this->_account->setId($this->_userId);
        $this->_accountData = $this->_account->getAccountData();
        if (!is_array($this->_accountData)) throw new Exception('Could not load account.');

        $this->_loadProgress();
    }

    public function getAccountData() { return $this->_accountData; }

    public function setServer($server)
    {
        // Da dein CMS im Moment hart auf 'siel' steht, setzen wir das hier ab.
        $this->_sdb = Handler::loadDB('siel');
    }

    public function hasServer() {
        return is_object($this->_sdb);
    }

    // =========================================================
    //  SEASON
    // =========================================================

    private function _loadCurrentSeason()
    {
        $now = date('Y-m-d H:i:s');
        $season = $this->_db->queryFetchSingle(
            "SELECT * FROM `aioncms`.`battlepass_seasons`
             WHERE `status` = 1 AND `start_date` <= ? AND `end_date` >= ?
             ORDER BY `id` DESC LIMIT 1",
            array($now, $now)
        );
        if (is_array($season)) {
            $this->_currentSeason = $season;
            $this->_loadTiers();
        }
    }

    private function _loadTiers()
    {
        if (!is_array($this->_currentSeason)) return;
        $rows = $this->_db->queryFetch(
            "SELECT * FROM `aioncms`.`battlepass_tiers`
             WHERE `season_id` = ? ORDER BY `tier_number` ASC",
            array($this->_currentSeason['id'])
        );
        $this->_tiers = is_array($rows) ? $rows : [];
    }

    public function getCurrentSeason() { return $this->_currentSeason; }
    public function getTiers()        { return $this->_tiers; }
    public function hasActiveSeason() { return is_array($this->_currentSeason); }

    // =========================================================
    //  PROGRESS
    // =========================================================

    private function _loadProgress()
    {
        if (!is_array($this->_currentSeason)) {
            $this->_progress = null;
            return;
        }
        $row = $this->_db->queryFetchSingle(
            "SELECT * FROM `aioncms`.`battlepass_progress`
             WHERE `account_id` = ? AND `season_id` = ?",
            array($this->_userId, $this->_currentSeason['id'])
        );
        $this->_progress = is_array($row) ? $row : [
            'account_id' => $this->_userId,
            'season_id'  => $this->_currentSeason['id'],
            'points'     => 0,
            'last_daily_login' => null,
        ];
    }

    public function getPoints()
    {
        return is_array($this->_progress) ? (int)$this->_progress['points'] : 0;
    }

    /**
     * Current tier number based on current points.
     */
    public function getCurrentTierNumber()
    {
        $pts = $this->getPoints();
        $currentTier = 0;
        foreach ($this->_tiers as $t) {
            if ($pts >= (int)$t['required_points']) {
                $currentTier = (int)$t['tier_number'];
            } else {
                break;
            }
        }
        return $currentTier;
    }

    /**
     * Adds points to an account.
     */
    public static function addPoints($accountId, $points, $source = 'other', $reference = '', $seasonId = null)
    {
        $db = Handler::loadDB();
        if ($seasonId === null) {
            $now = date('Y-m-d H:i:s');
            $season = $db->queryFetchSingle(
                "SELECT * FROM `aioncms`.`battlepass_seasons`
                 WHERE `status` = 1 AND `start_date` <= ? AND `end_date` >= ?
                 ORDER BY `id` DESC LIMIT 1",
                array($now, $now)
            );
            if (!is_array($season)) return false; // no active season
            $seasonId = (int)$season['id'];
        }

        $points = (int)$points;
        if ($points === 0) return true;

        // Ensure progress row exists
        $db->query(
            "INSERT INTO `aioncms`.`battlepass_progress` (`account_id`, `season_id`, `points`)
             VALUES (?, ?, 0)
             ON DUPLICATE KEY UPDATE `account_id` = `account_id`",
            array($accountId, $seasonId)
        );

        // Add points (never below 0)
        $db->query(
            "UPDATE `aioncms`.`battlepass_progress`
             SET `points` = GREATEST(0, `points` + ?)
             WHERE `account_id` = ? AND `season_id` = ?",
            array($points, $accountId, $seasonId)
        );

        // Log
        $db->query(
            "INSERT INTO `aioncms`.`battlepass_points_log`
             (`account_id`, `season_id`, `source`, `points`, `reference`)
             VALUES (?, ?, ?, ?, ?)",
            array($accountId, $seasonId, $source, $points, substr($reference, 0, 255))
        );

        return true;
    }

    /**
     * Grants daily login points once per calendar day (idempotent).
     */
    public function grantDailyLoginIfDue()
    {
        if (!is_array($this->_currentSeason)) return false;
        $today = date('Y-m-d');
        if (isset($this->_progress['last_daily_login']) && $this->_progress['last_daily_login'] === $today) {
            return false;
        }

        $pts = (int)$this->_currentSeason['daily_login_points'];
        if ($pts <= 0) return false;

        // Atomically update last_daily_login only if not already set today
        $this->_db->query(
            "UPDATE `aioncms`.`battlepass_progress`
             SET `last_daily_login` = ?
             WHERE `account_id` = ? AND `season_id` = ?
               AND (`last_daily_login` IS NULL OR `last_daily_login` < ?)",
            array($today, $this->_userId, $this->_currentSeason['id'], $today)
        );

        // Reload to confirm the update actually happened
        $this->_loadProgress();
        if (!isset($this->_progress['last_daily_login']) || $this->_progress['last_daily_login'] !== $today) {
            return false;
        }

        self::addPoints($this->_userId, $pts, 'daily_login', 'Daily login ' . $today, $this->_currentSeason['id']);
        $this->_loadProgress();
        return true;
    }

    // =========================================================
    //  CLAIM
    // =========================================================

    public function canClaimTier($tierId)
    {
        if (!is_array($this->_currentSeason)) return false;
        if (!is_array($this->_accountData)) return false;

        $tier = $this->_findTierById($tierId);
        if (!is_array($tier)) return false;
        if ($this->getPoints() < (int)$tier['required_points']) return false;

        $exists = $this->_db->queryFetchSingle(
            "SELECT `id` FROM `aioncms`.`battlepass_claims`
             WHERE `account_id` = ? AND `season_id` = ? AND `tier_id` = ?",
            array($this->_userId, $this->_currentSeason['id'], $tierId)
        );
        return !is_array($exists);
    }

    private function _findTierById($tierId)
    {
        foreach ($this->_tiers as $t) {
            if ((int)$t['id'] === (int)$tierId) return $t;
        }
        return null;
    }

    /**
     * Claims a tier and delivers the reward to the given character.
     */
    public function claimTier($tierId, $characterName)
    {
        if (!is_array($this->_currentSeason)) throw new Exception('No active season.');
        if (!is_array($this->_accountData)) throw new Exception('Account not loaded.');
        
        // Wir prüfen hier direkt, ob die Variable belegt ist, anstatt auf eine Methode zu vertrauen
        if (!is_object($this->_sdb)) {
            // Fallback: Wenn der Server nicht gesetzt ist, versuchen wir ihn hier zu laden
            $this->_sdb = Handler::loadDB('siel');
            if (!is_object($this->_sdb)) {
                throw new Exception('Server not set. Please contact support.');
            }
        }

        if (!Validator::AlphaNumeric($characterName))
            throw new Exception('Invalid character name.');

        // Verify character belongs to this account
        $char = $this->_sdb->queryFetchSingle(
            "SELECT * FROM `players` WHERE `name` = ? AND `account_id` = ?",
            array($characterName, $this->_accountData['id'])
        );
        if (!is_array($char)) throw new Exception('Character does not belong to your account.');

        $tier = $this->_findTierById($tierId);
        if (!is_array($tier)) throw new Exception('Tier not found.');

        if ($this->getPoints() < (int)$tier['required_points'])
            throw new Exception('You do not have enough points for this tier.');

        // Insert claim (UNIQUE key protects against double-claim)
        $this->_db->query(
            "INSERT INTO `aioncms`.`battlepass_claims`
             (`account_id`, `season_id`, `tier_id`, `tier_number`, `character_name`)
             VALUES (?, ?, ?, ?, ?)",
            array(
                $this->_userId,
                $this->_currentSeason['id'],
                $tierId,
                $tier['tier_number'],
                $characterName
            )
        );

        // Confirm the insert actually happened
        $check = $this->_db->queryFetchSingle(
            "SELECT `id` FROM `aioncms`.`battlepass_claims`
             WHERE `account_id` = ? AND `season_id` = ? AND `tier_id` = ?",
            array($this->_userId, $this->_currentSeason['id'], $tierId)
        );
        if (!is_array($check)) {
            throw new Exception('This tier has already been claimed.');
        }

        // Deliver reward; rollback claim on failure
        try {
            $this->_deliverReward($tier, $char);
        } catch (Exception $ex) {
            $this->_db->query(
                "DELETE FROM `aioncms`.`battlepass_claims`
                 WHERE `account_id` = ? AND `season_id` = ? AND `tier_id` = ?",
                array($this->_userId, $this->_currentSeason['id'], $tierId)
            );
            throw new Exception('Reward could not be delivered: ' . $ex->getMessage());
        }

        return $this->_rewardDescription($tier);
    }

    private function _deliverReward($tier, $char)
    {
        switch ($tier['reward_type']) {
            case 'item':
                $this->_deliverItem($tier, $char);
                break;
            case 'credits':
                $credits = (int)$tier['reward_credits'];
                if ($credits > 0) {
                    $add = $this->_account->addCredits($credits);
                    if (!$add) throw new Exception('Could not credit account.');
                }
                break;
            case 'title':
            case 'web_badge':
                logSystem::add('BattlePass: reward '.$tier['reward_type'].' '.$tier['reward_value'].' for '.$char['name']);
                break;
            default:
                throw new Exception('Unknown reward type.');
        }
    }

    private function _deliverItem($tier, $char)
    {
        $itemId    = (int)$tier['reward_item_id'];
        $itemCount = (int)$tier['reward_item_count'];
        $enchant   = (int)$tier['reward_item_enchant'];
        if ($itemId <= 0 || $itemCount <= 0) throw new Exception('Invalid item reward.');

        // Inventory ID (same offset logic as Shop)
        $lastId = $this->_sdb->queryFetchSingle("SELECT MAX(item_unique_id) as result FROM `inventory`");
        if (!is_array($lastId)) throw new Exception('Could not determine inventory id.');
        $uniqueItemId = $lastId['result'] > $this->_uniqueItemIdIncrementValue
            ? $lastId['result'] + 1
            : $lastId['result'] + $this->_uniqueItemIdIncrementValue;

        $invData = array(
            'item_unique_id'   => $uniqueItemId,
            'item_id'          => $itemId,
            'item_skin'        => $itemId,
            'item_count'       => $itemCount,
            'item_owner'       => (int)$char['id'],
            'item_creator'     => 'BattlePass',
            'activation_count' => 1,
            'item_location'    => 127,
            'rnd_bonus'        => 0,
            'enchant'          => $enchant,
            'authorize'        => 0,
        );
        $ok = $this->_sdb->query(
            "INSERT INTO `inventory`
             (item_unique_id, item_id, item_skin, item_count, item_owner, item_creator,
              activation_count, rnd_bonus, item_location, enchant, authorize)
             VALUES (:item_unique_id, :item_id, :item_skin, :item_count, :item_owner,
                     :item_creator, :activation_count, :rnd_bonus, :item_location,
                     :enchant, :authorize)",
            $invData
        );
        if (!$ok) throw new Exception('Could not insert item into inventory.');

        // Mail
        $mailLast = $this->_sdb->queryFetchSingle("SELECT MAX(mail_unique_id) as result FROM `mail`");
        $mailId = (is_array($mailLast) ? (int)$mailLast['result'] : 0) + 1;

        $mailData = array(
            'mail_unique_id'       => $mailId,
            'mail_recipient_id'    => (int)$char['id'],
            'sender_name'          => 'Siel',
            'mail_title'           => 'SielsPass Reward',
            'mail_message'         => 'Tier '.$tier['tier_number'].' reward. Enjoy!',
            'unread'               => 1,
            'attached_item_id'     => (int)$uniqueItemId,
            'attached_kinah_count' => 0,
            'express'              => 1,
        );
        $ok2 = $this->_sdb->query(
            "INSERT INTO `mail`
             (mail_unique_id, mail_recipient_id, sender_name, mail_title,
              mail_message, unread, attached_item_id, attached_kinah_count, express)
             VALUES (:mail_unique_id, :mail_recipient_id, :sender_name, :mail_title,
                     :mail_message, :unread, :attached_item_id, :attached_kinah_count, :express)",
            $mailData
        );
        if (!$ok2) throw new Exception('Could not create mail.');
    }

    private function _rewardDescription($tier)
    {
        switch ($tier['reward_type']) {
            case 'item':
                $name = $this->_getItemName((int)$tier['reward_item_id']);
                return $tier['reward_item_count'].'x '.$name.' has been sent via express mail.';
            case 'credits':
                return $tier['reward_credits'].' credits have been credited.';
            case 'title':
                return 'Title "'.$tier['reward_label'].'" unlocked.';
            case 'web_badge':
                return 'Badge "'.$tier['reward_label'].'" unlocked.';
        }
        return 'Reward delivered.';
    }

    private function _getItemName($itemId)
    {
        $info = $this->_db->queryFetchSingle(
            "SELECT `item_name` FROM `aion_itemlist` WHERE `item_id` = ?",
            array($itemId)
        );
        return is_array($info) ? $info['item_name'] : ('Item #'.$itemId);
    }

    // =========================================================
    //  DISPLAY HELPERS
    // =========================================================

    public function getClaimedTierIds()
    {
        if (!is_array($this->_currentSeason) || !$this->_userId) return [];
        $rows = $this->_db->queryFetch(
            "SELECT `tier_id` FROM `aioncms`.`battlepass_claims`
             WHERE `account_id` = ? AND `season_id` = ?",
            array($this->_userId, $this->_currentSeason['id'])
        );
        
        if (!is_array($rows)) return [];
        
        // FIX für PHP 5.4: array_column() gibt es erst ab PHP 5.5
        $result = array();
        foreach ($rows as $row) {
            $result[] = $row['tier_id'];
        }
        return $result;
    }

    public function getItemIconUrl($itemId)
    {
        $info = $this->_db->queryFetchSingle(
            "SELECT `item_icon` FROM `aion_itemlist` WHERE `item_id` = ?",
            array($itemId)
        );
        if (!is_array($info) || !check($info['item_icon'])) return '';
        return 'https://aioncodex.com/items/'.$info['item_icon'].'.png';
    }

    public function getItemName($itemId) { return $this->_getItemName($itemId); }

    public function getBattlePassUrl() { return $this->_bpUrl; }
}