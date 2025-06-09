<?php
// Start session and check admin authentication
session_start();

// Check if admin is not logged in
if (!isset($_SESSION['admin_logged_in'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_password'])) {
        // In a real application, use proper password hashing and secure storage
        $correct_password = 'admin123'; // Replace with hashed password
        if ($_POST['admin_password'] === $correct_password) {
            $_SESSION['admin_logged_in'] = true;
        } else {
            $login_error = "Invalid admin password";
        }
    }
    
    if (!isset($_SESSION['admin_logged_in'])) {
        // Show login form
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Admin Login</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
            <style>
                body {
                    background-color: #f8f9fa;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    height: 100vh;
                }
                .login-container {
                    max-width: 400px;
                    width: 100%;
                    padding: 2rem;
                    background: white;
                    border-radius: 10px;
                    box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
                }
            </style>
        </head>
        <body>
            <div class="login-container">
                <h2 class="text-center mb-4">Admin Dashboard Login</h2>
                <?php if (isset($login_error)): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($login_error); ?></div>
                <?php endif; ?>
                <form method="POST">
                    <div class="mb-3">
                        <label for="admin_password" class="form-label">Admin Password</label>
                        <input type="password" class="form-control" id="admin_password" name="admin_password" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Login</button>
                </form>
            </div>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        </body>
        </html>
        <?php
        exit();
    }
}

// Admin is logged in, proceed with dashboard
require_once 'conn.php'; // Database connection

// Update the connection to use the dating_app database
try {
    $connect->exec("USE dating_app");
} catch (PDOException $e) {
    die("Database selection failed: " . $e->getMessage());
}

// Function to safely fetch single value from database
function fetchSingleValue($conn, $query, $params = []) {
    $stmt = $conn->prepare($query);
    if ($params) {
        $stmt->execute($params);
    } else {
        $stmt->execute();
    }
    return $stmt->fetchColumn();
}

// Fetch all statistics with optimized queries
try {
    // Total users
    $totalUsers = fetchSingleValue($connect, "SELECT COUNT(*) FROM users");
    
    // Total matches
    $totalMatches = fetchSingleValue($connect, "SELECT COUNT(*) FROM matches");
    
    // Approved matches
    $approvedMatches = fetchSingleValue($connect, "SELECT COUNT(*) FROM matches WHERE approved = 1");
    
    // Messages sent
    $messagesSent = fetchSingleValue($connect, "SELECT COUNT(*) FROM chatmessage");
    
    // Messages that received a reply (conversations with at least 2 messages)
    $messagesWithReply = fetchSingleValue($connect, "
        SELECT COUNT(DISTINCT conversation_id) FROM (
            SELECT LEAST(sender_id, receiver_id) AS user1, 
                   GREATEST(sender_id, receiver_id) AS user2,
                   CONCAT(LEAST(sender_id, receiver_id), '-', GREATEST(sender_id, receiver_id)) AS conversation_id
            FROM chatmessage
            GROUP BY user1, user2, conversation_id
            HAVING COUNT(*) >= 2
        ) AS conversations
    ");
    
    // Gender distribution (assuming 1=male, 2=female, 0=other/not specified)
    $maleCount = fetchSingleValue($connect, "SELECT COUNT(*) FROM users WHERE gender = 1");
    $femaleCount = fetchSingleValue($connect, "SELECT COUNT(*) FROM users WHERE gender = 2");
    $otherGenderCount = fetchSingleValue($connect, "SELECT COUNT(*) FROM users WHERE gender = 0 OR gender IS NULL");
    
    // New users in last 7 days
    $newUsersLast7Days = fetchSingleValue($connect, "
        SELECT COUNT(*) FROM users 
        WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
    ");
    
    // Most active users (by message count)
    $stmt = $connect->prepare("
        SELECT u.id, u.username, COUNT(cm.id) AS message_count 
        FROM users u
        JOIN chatmessage cm ON u.id = cm.sender_id
        GROUP BY u.id, u.username
        ORDER BY message_count DESC
        LIMIT 5
    ");
    $stmt->execute();
    $mostActiveUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Dating/Social Connection</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary-color: #6c5ce7;
            --secondary-color: #a29bfe;
            --success-color: #00b894;
            --info-color: #0984e3;
            --warning-color: #fdcb6e;
            --danger-color: #d63031;
        }
        body {
            background-color: #f5f6fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .sidebar {
            background: linear-gradient(135deg, #2d3436 0%, #000000 100%);
            color: white;
            height: 100vh;
            position: fixed;
            padding-top: 20px;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        }
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 5px;
            border-radius: 5px;
            padding: 10px 15px;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
        }
        .sidebar .nav-link i {
            margin-right: 10px;
        }
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            margin-bottom: 20px;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            background-color: white;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            font-weight: 600;
        }
        .stat-card {
            text-align: center;
            padding: 20px;
        }
        .stat-card .stat-value {
            font-size: 2.5rem;
            font-weight: 700;
            margin: 10px 0;
        }
        .stat-card .stat-label {
            color: #6c757d;
            font-size: 0.9rem;
        }
        .bg-primary-light {
            background-color: rgba(108, 92, 231, 0.1);
            color: var(--primary-color);
        }
        .bg-success-light {
            background-color: rgba(0, 184, 148, 0.1);
            color: var(--success-color);
        }
        .bg-info-light {
            background-color: rgba(9, 132, 227, 0.1);
            color: var(--info-color);
        }
        .bg-warning-light {
            background-color: rgba(253, 203, 110, 0.1);
            color: #e17055;
        }
        .table-responsive {
            border-radius: 10px;
            overflow: hidden;
        }
        .table {
            margin-bottom: 0;
        }
        .table th {
            background-color: #f8f9fa;
            border-top: none;
        }
        .badge-success {
            background-color: var(--success-color);
        }
        .badge-primary {
            background-color: var(--primary-color);
        }
        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>


    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Dashboard Overview</h2>
            <span class="text-muted">Last updated: <?php echo date('Y-m-d H:i:s'); ?></span>
        </div>

        <!-- Stats Cards Row -->
        <div class="row">
            <div class="col-md-6 col-lg-3">
                <div class="card bg-primary-light">
                    <div class="card-body stat-card">
                        <i class="bi bi-people-fill" style="font-size: 1.5rem;"></i>
                        <div class="stat-value"><?php echo number_format($totalUsers); ?></div>
                        <div class="stat-label">Total Users</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card bg-success-light">
                    <div class="card-body stat-card">
                        <i class="bi bi-heart-fill" style="font-size: 1.5rem;"></i>
                        <div class="stat-value"><?php echo number_format($totalMatches); ?></div>
                        <div class="stat-label">Total Matches</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card bg-info-light">
                    <div class="card-body stat-card">
                        <i class="bi bi-check-circle-fill" style="font-size: 1.5rem;"></i>
                        <div class="stat-value"><?php echo number_format($approvedMatches); ?></div>
                        <div class="stat-label">Approved Matches</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card bg-warning-light">
                    <div class="card-body stat-card">
                        <i class="bi bi-chat-left-text-fill" style="font-size: 1.5rem;"></i>
                        <div class="stat-value"><?php echo number_format($messagesSent); ?></div>
                        <div class="stat-label">Messages Sent</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts and Additional Stats Row -->
        <div class="row mt-4">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-gender-ambiguous me-2"></i>Gender Distribution
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="genderChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-activity me-2"></i>Recent Activity
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-primary-light p-3 rounded me-3">
                                        <i class="bi bi-person-plus" style="font-size: 1.5rem;"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-0"><?php echo number_format($newUsersLast7Days); ?></h5>
                                        <small class="text-muted">New Users (7 days)</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-success-light p-3 rounded me-3">
                                        <i class="bi bi-chat-square-text" style="font-size: 1.5rem;"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-0"><?php echo number_format($messagesWithReply); ?></h5>
                                        <small class="text-muted">Conversations</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4">
                            <h6>Most Active Users</h6>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>User</th>
                                            <th>Messages Sent</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($mostActiveUsers as $user): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                                            <td><?php echo number_format($user['message_count']); ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Statistics Row -->
        <div class="row mt-4">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-bar-chart-line me-2"></i>Match Approval Rate
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 text-center">
                                <h3><?php echo $totalMatches > 0 ? round(($approvedMatches / $totalMatches) * 100, 2) : 0; ?>%</h3>
                                <p class="text-muted">Approval Rate</p>
                            </div>
                            <div class="col-md-8">
                                <div class="progress" style="height: 30px;">
                                    <div class="progress-bar bg-success" role="progressbar" 
                                         style="width: <?php echo $totalMatches > 0 ? ($approvedMatches / $totalMatches) * 100 : 0; ?>%" 
                                         aria-valuenow="<?php echo $totalMatches > 0 ? ($approvedMatches / $totalMatches) * 100 : 0; ?>" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100">
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between mt-2">
                                    <small>Approved: <?php echo number_format($approvedMatches); ?></small>
                                    <small>Pending: <?php echo number_format($totalMatches - $approvedMatches); ?></small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Gender Distribution Pie Chart
        const genderCtx = document.getElementById('genderChart').getContext('2d');
        const genderChart = new Chart(genderCtx, {
            type: 'doughnut',
            data: {
                labels: ['Male', 'Female', 'Other/Not Specified'],
                datasets: [{
                    data: [<?php echo $maleCount; ?>, <?php echo $femaleCount; ?>, <?php echo $otherGenderCount; ?>],
                    backgroundColor: [
                        '#3498db',
                        '#e84393',
                        '#636e72'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Logout functionality
        document.querySelector('.nav-link.text-danger').addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to logout?')) {
                e.preventDefault();
            }
        });
    </script>
</body>
</html>
<?php
// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: admindash.php');
    exit();
}
?>
