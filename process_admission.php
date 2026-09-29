<?php
// Minimal Data Transaction: Submitting, Processing, Storing, Retrieving & Displaying

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. PROCESSING: Collect and sanitize input
    $fullname = htmlspecialchars(trim($_POST['fullname'] ?? ''));
    $email    = htmlspecialchars(trim($_POST['email'] ?? ''));
    $phone    = htmlspecialchars(trim($_POST['phone'] ?? ''));
    $program  = htmlspecialchars(trim($_POST['program'] ?? ''));
    $date     = date("Y-m-d H:i:s");

    // 2. STORING: Append data record to a server-side storage file
    $dataFile = "inquiries.txt";
    $record   = "$fullname | $email | $phone | " . strtoupper($program) . " | $date" . PHP_EOL;
    file_put_contents($dataFile, $record, FILE_APPEND | LOCK_EX);

    // 3. RETRIEVING: Read all stored records from file
    $savedRecords = file_exists($dataFile) ? file($dataFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Inquiry Confirmation & Records | Apex Institute</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="nav-container">
            <a href="index.html" class="logo">
                <div class="logo-icon">A</div>
                <span class="logo-text">Apex Institute</span>
            </a>
        </div>
    </header>

    <main class="section">
        <!-- 4. DISPLAYING: Newly Processed Submission -->
        <div class="card" style="max-width: 750px; margin: 0 auto 2rem auto; text-align: center;">
            <h2 style="color: #2e7d32;">Inquiry Successfully Stored!</h2>
            <p>Thank you, <strong><?php echo $fullname; ?></strong>. Your application has been processed and saved.</p>
        </div>

        <!-- 5. DISPLAYING: Retrieved Records from Server Storage -->
        <div class="card" style="max-width: 750px; margin: 0 auto;">
            <h3 class="card-title">Stored Inquiries on Server (Retrieved from inquiries.txt)</h3>
            <div class="table-container" style="margin-top: 1rem;">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Program</th>
                            <th>Saved At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($savedRecords as $index => $row): 
                            $fields = explode(" | ", $row);
                        ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td><strong><?php echo $fields[0] ?? ''; ?></strong></td>
                            <td><?php echo $fields[1] ?? ''; ?></td>
                            <td><?php echo $fields[2] ?? ''; ?></td>
                            <td><span class="badge-program"><?php echo $fields[3] ?? ''; ?></span></td>
                            <td><?php echo $fields[4] ?? ''; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div style="text-align: center; margin-top: 1.5rem;">
                <a href="admissions.html" class="btn btn-outline" style="color: #990000; border-color: #990000;">New Inquiry</a>
                <a href="index.html" class="btn btn-primary" style="background-color: #990000; color: #ffffff; margin-left: 0.5rem;">Back to Home</a>
            </div>
        </div>
    </main>
</body>
</html>
<?php
} else {
    header("Location: admissions.html");
    exit();
}
?>
