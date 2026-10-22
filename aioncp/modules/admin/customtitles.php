<?php
// ==========================================
// 1. CONFIGURATION
// ==========================================
// Enter the path to your file here
$logFilePath = "C:\\Users\\Administrator\\Desktop\\AionXcentra-4.7.5\\UniServerZ\\www\\0_1.xml";

$message = "";

// ==========================================
// 2. SAVE LOGIC (When the form is submitted)
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['log_content'])) {
        // The text from the textarea
        $newContent = $_POST['log_content'];
        
        // Open the file in write mode ("w" completely overwrites the file)
        // If there are permission issues, the Apache service might not have 
        // write access to the specified folder.
        if (is_writable($logFilePath) || !file_exists($logFilePath)) {
            if (file_put_contents($logFilePath, $newContent) !== false) {
                $message = "<p style='color: green; font-weight: bold;'>File successfully saved!</p>";
            } else {
                $message = "<p style='color: red; font-weight: bold;'>Error: Could not write to file (check permissions).</p>";
            }
        } else {
            $message = "<p style='color: red; font-weight: bold;'>Error: No write permissions for this file!</p>";
        }
    }
}

// ==========================================
// 3. DISPLAY LOGIC (Read file)
// ==========================================
$fileContent = "";
if (file_exists($logFilePath)) {
    $fileContent = file_get_contents($logFilePath);
} else {
    $message .= "<p style='color: orange; font-weight: bold;'>Warning: The file does not exist yet. It will be created when you save.</p>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>File Editor</title>
    <style>
        body { font-family: Arial, sans-serif; background: #2c3e50; color: #ecf0f1; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; background: #34495e; padding: 20px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.3); }
        h2 { margin-top: 0; color: #3498db; }
        textarea { 
            width: 100%; 
            height: 600px; 
            background: #1e1e1e; 
            color: #d4d4d4; 
            font-family: Consolas, "Courier New", monospace; 
            font-size: 14px; 
            padding: 10px; 
            border: 1px solid #7f8c8d; 
            border-radius: 4px; 
            box-sizing: border-box;
            resize: vertical;
        }
        .btn-container { margin-top: 15px; display: flex; justify-content: space-between; align-items: center; }
        button { 
            background: #27ae60; 
            color: white; 
            border: none; 
            padding: 10px 20px; 
            font-size: 16px; 
            border-radius: 4px; 
            cursor: pointer; 
            transition: background 0.2s;
        }
        button:hover { background: #2ecc71; }
        .file-path { font-family: monospace; background: #2c3e50; padding: 5px 10px; border-radius: 4px; font-size: 12px; color: #95a5a6; }
    </style>
</head>
<body>

<div class="container">
    <h2>Title Editor</h2>
    <p>Currently editing file: 0_1.xml for all titles</p>
    
    <?php echo $message; ?>

    <form method="POST" action="">
        <textarea name="log_content" spellcheck="false"><?php echo htmlspecialchars($fileContent); ?></textarea>
        <div class="btn-container">
            <button type="submit">Save Changes</button>
            <small>Reload the page to see the current state of the file.</small>
        </div>
    </form>
</div>

</body>
</html>