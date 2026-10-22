<?php
class ServerMonitorNoShell {
    
    public function getMemoryUsage() {
        if (class_exists('COM')) {
            return $this->getMemoryUsageCOM();
        } else {
            return $this->getMemoryUsageFallback();
        }
    }
    
    private function getMemoryUsageCOM() {
        try {
            $wmi = new COM('WinMgmts:');
            $os = $wmi->InstancesOf('Win32_OperatingSystem');
            $computer = $wmi->InstancesOf('Win32_ComputerSystem');
            
            $total_ram = 0;
            $free_ram = 0;
            
            foreach ($computer as $c) {
                $total_ram = $c->TotalPhysicalMemory / 1024 / 1024; // MB
                break;
            }
            
            foreach ($os as $o) {
                $free_ram = $o->FreePhysicalMemory / 1024; // MB
                break;
            }
            
            if ($total_ram > 0) {
                $used_ram = $total_ram - $free_ram;
                $percentage = round(($used_ram / $total_ram) * 100, 2);
            } else {
                $used_ram = 0;
                $percentage = 0;
            }
            
            return array(
                'total' => round($total_ram),
                'used' => round($used_ram),
                'free' => round($free_ram),
                'percentage' => $percentage
            );
            
        } catch (Exception $e) {
            return $this->getMemoryUsageFallback();
        }
    }
    
    public function getCpuUsage() {
        if (class_exists('COM')) {
            return $this->getCpuUsageCOM();
        } else {
            return array(
                'last_1_min' => 0,
                'last_5_min' => 0,
                'last_15_min' => 0,
                'note' => 'CPU-Info benötigt COM'
            );
        }
    }
    
    private function getCpuUsageCOM() {
        try {
            $wmi = new COM('WinMgmts:');
            $cpus = $wmi->InstancesOf('Win32_Processor');
            
            $usage = 0;
            $count = 0;
            
            foreach ($cpus as $cpu) {
                $usage += $cpu->LoadPercentage;
                $count++;
            }
            
            if ($count > 0) {
                $usage = round($usage / $count);
            }
            
            return array(
                'last_1_min' => $usage,
                'last_5_min' => $usage,
                'last_15_min' => $usage
            );
            
        } catch (Exception $e) {
            return array(
                'last_1_min' => 0,
                'last_5_min' => 0,
                'last_15_min' => 0,
                'note' => 'CPU-Info nicht verfügbar'
            );
        }
    }
    
    private function getMemoryUsageFallback() {
        // Sehr einfache Schätzung basierend auf PHP memory_limit
        $memory_limit = ini_get('memory_limit');
        $memory_usage = memory_get_usage(true);
        
        // Konvertiere memory_limit zu Bytes
        if (preg_match('/^(\d+)(.)$/', $memory_limit, $matches)) {
            if ($matches[2] == 'G') {
                $memory_limit = $matches[1] * 1024 * 1024 * 1024;
            } else if ($matches[2] == 'M') {
                $memory_limit = $matches[1] * 1024 * 1024;
            } else if ($matches[2] == 'K') {
                $memory_limit = $matches[1] * 1024;
            }
        }
        
        $total_mb = round($memory_limit / 1024 / 1024);
        $used_mb = round($memory_usage / 1024 / 1024);
        $percentage = $total_mb > 0 ? round(($used_mb / $total_mb) * 100, 2) : 0;
        
        return array(
            'total' => $total_mb,
            'used' => $used_mb,
            'free' => $total_mb - $used_mb,
            'percentage' => $percentage,
            'note' => 'Geschätzt basierend auf PHP memory_limit'
        );
    }
    
    public function getServerInfo() {
        $info = array();
        
        // Basis-Informationen über PHP-Funktionen
        $info['php_version'] = PHP_VERSION;
        $info['os'] = php_uname('s');
        $info['hostname'] = php_uname('n');
        $info['architecture'] = php_uname('m');
        $info['server_software'] = isset($_SERVER['SERVER_SOFTWARE']) ? $_SERVER['SERVER_SOFTWARE'] : 'Unbekannt';
        
        // PHP Memory Info
        $info['memory_limit'] = ini_get('memory_limit');
        $info['max_execution_time'] = ini_get('max_execution_time');
        $info['php_memory_usage'] = round(memory_get_usage(true) / 1024 / 1024, 2) . ' MB';
        $info['php_peak_memory'] = round(memory_get_peak_usage(true) / 1024 / 1024, 2) . ' MB';
        
        return $info;
    }
}

$monitor = new ServerMonitorNoShell();
$memory = $monitor->getMemoryUsage();
$cpu = $monitor->getCpuUsage();
$server_info = $monitor->getServerInfo();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Server Monitoring (Ohne Shell)</title>
    <style>
        .metric-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            margin: 10px;
            background: #f9f9f9;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .progress-bar {
            width: 100%;
            height: 25px;
            background-color: #e0e0e0;
            border-radius: 10px;
            overflow: hidden;
            margin: 10px 0;
        }
        .progress {
            height: 100%;
            transition: width 0.3s;
            text-align: center;
            color: white;
            font-weight: bold;
            line-height: 25px;
        }
        .normal { background-color: #4CAF50; }
        .warning { background-color: #ff9800; }
        .critical { background-color: #f44336; }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 10px;
            margin: 20px 0;
        }
        .info-item {
            padding: 10px;
            background: #f0f0f0;
            border-radius: 5px;
            border-left: 4px solid #4CAF50;
        }
        .note {
            font-size: 12px;
            color: #666;
            font-style: italic;
        }
    </style>
</head>
<body>
    <h1>🖥️ Server Monitoring</h1>
    
    <div class="info-grid">
        <div class="info-item">
            <strong>Betriebssystem:</strong><br>
            <!--<?php echo $server_info['os']; ?>-->
			Windows Server 2022 Standard
        </div>
        <div class="info-item">
            <strong>PHP Version:</strong><br>
            <?php echo $server_info['php_version']; ?>
        </div>
        <div class="info-item">
            <strong>Architektur:</strong><br>
            <?php echo $server_info['architecture']; ?>
        </div>
        <div class="info-item">
            <strong>Memory Limit:</strong><br>
            <?php echo $server_info['memory_limit']; ?>
        </div>
        <div class="info-item">
            <strong>PHP Memory:</strong><br>
            <?php echo $server_info['php_memory_usage']; ?>
        </div>
        <div class="info-item">
            <strong>Server Software:</strong><br>
            <?php echo $server_info['server_software']; ?>
        </div>
    </div>
    
    <div class="stats-grid">
        <div class="metric-card">
            <h2>💾 RAM-Verbrauch</h2>
            <p><strong>Gesamt:</strong> <?php echo number_format($memory['total']); ?> MB</p>
            <p><strong>Verwendet:</strong> <?php echo number_format($memory['used']); ?> MB</p>
            <p><strong>Frei:</strong> <?php echo number_format($memory['free']); ?> MB</p>
            <p><strong>Auslastung:</strong> <?php echo $memory['percentage']; ?>%</p>
            <?php if (isset($memory['note'])): ?>
                <p class="note"><?php echo $memory['note']; ?></p>
            <?php endif; ?>
            <div class="progress-bar">
                <div class="progress 
                    <?php 
                    if ($memory['percentage'] > 80) {
                        echo 'critical';
                    } elseif ($memory['percentage'] > 60) {
                        echo 'warning';
                    } else {
                        echo 'normal';
                    }
                    ?>"
                    style="width: <?php echo $memory['percentage']; ?>%">
                    <?php echo $memory['percentage']; ?>%
                </div>
            </div>
        </div>
        
        <div class="metric-card">
            <h2>⚡ CPU-Auslastung</h2>
            <?php if ($cpu['last_1_min'] > 0): ?>
                <p><strong>Aktuelle Auslastung:</strong> <?php echo $cpu['last_1_min']; ?>%</p>
                <div class="progress-bar">
                    <div class="progress 
                        <?php 
                        if ($cpu['last_1_min'] > 80) {
                            echo 'critical';
                        } elseif ($cpu['last_1_min'] > 60) {
                            echo 'warning';
                        } else {
                            echo 'normal';
                        }
                        ?>"
                        style="width: <?php echo $cpu['last_1_min']; ?>%">
                        <?php echo $cpu['last_1_min']; ?>%
                    </div>
                </div>
            <?php else: ?>
                <p>CPU-Informationen benötigen COM-Erweiterung</p>
                <p class="note"><?php echo isset($cpu['note']) ? $cpu['note'] : ''; ?></p>
            <?php endif; ?>
        </div>
    </div>

    <div class="metric-card">
        <h2>📊 PHP Informationen</h2>
        <div class="info-grid">
            <div class="info-item">
                <strong>Memory Usage:</strong><br>
                <?php echo $server_info['php_memory_usage']; ?>
            </div>
            <div class="info-item">
                <strong>Peak Memory:</strong><br>
                <?php echo $server_info['php_peak_memory']; ?>
            </div>
            <div class="info-item">
                <strong>Max Execution Time:</strong><br>
                <?php echo $server_info['max_execution_time']; ?>s
            </div>
            <div class="info-item">
                <strong>PHP Memory Limit:</strong><br>
                <?php echo $server_info['memory_limit']; ?>
            </div>
        </div>
    </div>

    <script>
        setTimeout(function(){
            window.location.reload();
        }, 30000);
        
        let countdown = 30;
        setInterval(function() {
            countdown--;
            document.getElementById('countdown').textContent = countdown;
            if (countdown <= 0) countdown = 30;
        }, 1000);
    </script>
    
    <p><small>Aktualisiert in: <span id="countdown">30</span>s</small></p>
</body>
</html>