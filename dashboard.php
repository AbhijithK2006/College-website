<?php
require_once __DIR__ . '/auth.php';

// Strict Access Protection: Only logged-in registered students can access
require_student_login();

$student = current_student();

// Compute initials for the avatar
$nameParts = explode(' ', trim($student['name'] ?? 'Student'));
$initials = '';
foreach ($nameParts as $part) {
    if (!empty($part)) {
        $initials .= strtoupper(substr($part, 0, 1));
        if (strlen($initials) >= 2) break;
    }
}
if (empty($initials)) {
    $initials = 'ST';
}

// Student Enrolled Courses Data
$courses = [
    [
        'code' => 'CS301',
        'title' => 'Data Structures & Algorithms',
        'faculty' => 'Dr. K. Narayanan',
        'credits' => 4,
        'schedule' => 'Mon, Wed 09:30 - 10:45 AM',
        'venue' => 'Lecture Hall 2B',
        'attendance' => '96%',
        'status' => 'Ongoing'
    ],
    [
        'code' => 'CS302',
        'title' => 'Database Management Systems',
        'faculty' => 'Prof. Anitha Menon',
        'credits' => 4,
        'schedule' => 'Tue, Thu 11:00 - 12:15 PM',
        'venue' => 'CS Lab 3',
        'attendance' => '92%',
        'status' => 'Ongoing'
    ],
    [
        'code' => 'CS303',
        'title' => 'Web Application Architecture',
        'faculty' => 'Prof. Rahul Varma',
        'credits' => 3,
        'schedule' => 'Mon, Fri 02:00 - 03:30 PM',
        'venue' => 'Software Studio 1',
        'attendance' => '95%',
        'status' => 'Ongoing'
    ],
    [
        'code' => 'MA301',
        'title' => 'Discrete Mathematics & Graph Theory',
        'faculty' => 'Dr. E. Sreedharan',
        'credits' => 4,
        'schedule' => 'Wed, Fri 11:00 - 12:15 PM',
        'venue' => 'Seminar Hall A',
        'attendance' => '91%',
        'status' => 'Ongoing'
    ],
    [
        'code' => 'CS305',
        'title' => 'Operating Systems & System Programming',
        'faculty' => 'Prof. Priya Nair',
        'credits' => 3,
        'schedule' => 'Tue, Thu 02:00 - 03:30 PM',
        'venue' => 'Systems Lab 2',
        'attendance' => '98%',
        'status' => 'Ongoing'
    ]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Student Portal Dashboard | Apex Institute of Technology</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

  <!-- Header Navigation -->
  <header>
    <div class="nav-container">
      <a href="index.html" class="logo" id="header-logo">
        <div class="logo-icon">A</div>
        <span class="logo-text">Apex Institute</span>
      </a>
      <nav aria-label="Main Navigation">
        <ul class="nav-links">
          <li><a href="index.html" id="nav-home">Home</a></li>
          <li><a href="academics.html" id="nav-academics">Academics</a></li>
          <li><a href="admissions.html" id="nav-admissions">Admissions</a></li>
          <li><a href="campus.html" id="nav-campus">Campus Life</a></li>
          <li><a href="dashboard.php" class="active" id="nav-dashboard">Dashboard</a></li>
          <li><a href="logout.php" id="nav-logout" style="background-color: #333; color: #fff; border-radius: 4px;">Logout</a></li>
        </ul>
      </nav>
    </div>
  </header>

  <main class="section">
    <!-- Student Profile Banner -->
    <div class="student-banner">
      <div class="student-profile-lead">
        <div class="student-avatar" title="<?php echo htmlspecialchars($student['name']); ?>">
          <?php echo htmlspecialchars($initials); ?>
        </div>
        <div class="student-meta">
          <h2>Welcome, <?php echo htmlspecialchars($student['name']); ?>!</h2>
          <p><?php echo htmlspecialchars($student['program']); ?></p>
          <div class="student-pill-group">
            <span class="pill-badge">ID: <?php echo htmlspecialchars($student['id']); ?></span>
            <span class="pill-badge active">&#9679; Status: Enrolled &amp; Active</span>
            <span class="pill-badge"><?php echo htmlspecialchars($student['email']); ?></span>
          </div>
        </div>
      </div>
      <div>
        <a href="logout.php" class="btn btn-outline" style="border-color: #ffffff; color: #ffffff;" id="banner-logout-btn">
          Sign Out &rarr;
        </a>
      </div>
    </div>

    <!-- Quick Stats Metric Grid -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-label">Cumulative GPA</div>
        <div class="stat-value">3.85 <span style="font-size: 1rem; color: #666;">/ 4.0</span></div>
        <div class="stat-desc">Top 5% &bull; Dean's Honour List</div>
      </div>

      <div class="stat-card">
        <div class="stat-label">Semester Attendance</div>
        <div class="stat-value" style="color: #2e7d32;">94.4%</div>
        <div class="stat-desc">Eligible for all End-Sem Examinations</div>
      </div>

      <div class="stat-card">
        <div class="stat-label">Enrolled Courses</div>
        <div class="stat-value"><?php echo count($courses); ?></div>
        <div class="stat-desc">18 Total Academic Credits</div>
      </div>

      <div class="stat-card">
        <div class="stat-label">Current Academic Term</div>
        <div class="stat-value" style="font-size: 1.5rem; line-height: 2rem;">Spring 2026</div>
        <div class="stat-desc">Semester 4 &bull; Midterms in 3 weeks</div>
      </div>
    </div>

    <!-- Two-Column Dashboard Content -->
    <div class="dashboard-grid">

      <!-- Left Column: Registered Coursework -->
      <div>
        <div class="card" style="margin-bottom: 2rem;">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h3 class="card-title" style="margin-bottom: 0;">Enrolled Courses &amp; Timetable</h3>
            <span class="badge-program"><?php echo count($courses); ?> Courses Active</span>
          </div>
          
          <div class="table-container" style="margin-bottom: 0;">
            <table>
              <thead>
                <tr>
                  <th>Course</th>
                  <th>Title &amp; Instructor</th>
                  <th>Credits</th>
                  <th>Class Schedule</th>
                  <th>Attendance</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($courses as $c): ?>
                <tr>
                  <td><strong><?php echo htmlspecialchars($c['code']); ?></strong></td>
                  <td>
                    <div><strong><?php echo htmlspecialchars($c['title']); ?></strong></div>
                    <div style="font-size: 0.85rem; color: #666;"><?php echo htmlspecialchars($c['faculty']); ?> &bull; <?php echo htmlspecialchars($c['venue']); ?></div>
                  </td>
                  <td><?php echo htmlspecialchars($c['credits']); ?></td>
                  <td style="font-size: 0.9rem;"><?php echo htmlspecialchars($c['schedule']); ?></td>
                  <td>
                    <span style="font-weight: bold; color: #2e7d32;"><?php echo htmlspecialchars($c['attendance']); ?></span>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>

        <!-- College Notices / Circulars -->
        <div class="card">
          <h3 class="card-title">Campus Notices &amp; Circulars</h3>
          <ul class="styled-list" style="margin-top: 1rem;">
            <li>
              <strong>Mid-Semester Examination Timetable</strong>
              The Spring 2026 theory and practical mid-term examinations commence from March 18, 2026. Hall tickets available next week.
            </li>
            <li>
              <strong>National Hackathon Registration Open</strong>
              Apex Institute is hosting the annual 'CodeApex 2026' National Hackathon. Form teams of 3-4 and submit abstracts by Friday.
            </li>
            <li>
              <strong>Digital Library Access Renewed (IEEE / ACM)</strong>
              All enrolled students now have updated proxy credentials to access the IEEE and ACM digital libraries through the student intranet.
            </li>
          </ul>
        </div>
      </div>

      <!-- Right Column: Student Profile & Quick Actions -->
      <div>
        <!-- Student Info Card -->
        <div class="card" style="margin-bottom: 2rem;">
          <h3 class="card-title">Student Details</h3>
          <ul class="info-list" style="margin-top: 1rem;">
            <li>
              <span class="info-label">Full Name:</span>
              <span class="info-value"><strong><?php echo htmlspecialchars($student['name']); ?></strong></span>
            </li>
            <li>
              <span class="info-label">Student ID:</span>
              <span class="info-value"><code><?php echo htmlspecialchars($student['id']); ?></code></span>
            </li>
            <li>
              <span class="info-label">Registered Email:</span>
              <span class="info-value"><?php echo htmlspecialchars($student['email']); ?></span>
            </li>
            <li>
              <span class="info-label">Program:</span>
              <span class="info-value"><?php echo htmlspecialchars($student['program']); ?></span>
            </li>
            <li>
              <span class="info-label">Registered Date:</span>
              <span class="info-value"><?php echo htmlspecialchars($student['created_at']); ?></span>
            </li>
            <li>
              <span class="info-label">Session Started:</span>
              <span class="info-value"><?php echo htmlspecialchars($student['logged_in_at'] ?? 'Active'); ?></span>
            </li>
          </ul>
        </div>

        <!-- Quick Actions Card -->
        <div class="card">
          <h3 class="card-title">Quick Actions</h3>
          <div class="action-stack" style="margin-top: 1rem;">
            <a href="academics.html" class="btn btn-outline" style="color: #990000; border-color: #990000;">
              View Curriculum &amp; Syllabi
            </a>
            <a href="admissions.html" class="btn btn-outline" style="color: #990000; border-color: #990000;">
              Submit Admission Inquiry
            </a>
            <a href="campus.html" class="btn btn-outline" style="color: #990000; border-color: #990000;">
              Explore Campus Life &amp; Labs
            </a>
            <a href="logout.php" class="btn btn-primary" style="background-color: #990000; color: #ffffff;">
              Log Out of Student Portal
            </a>
          </div>
        </div>
      </div>

    </div>
  </main>

  <!-- Footer -->
  <footer>
    <div class="footer-container">
      <div class="footer-grid">
        <div class="footer-col">
          <div class="footer-logo">
            <div class="logo-icon">A</div>
            <span class="logo-text">Apex Institute</span>
          </div>
          <p>Dedicated to delivering quality technical education, fostering computational innovation, and building skilled careers.</p>
        </div>

        <div class="footer-col">
          <h4>Contact Details</h4>
          <p>
            Vazhayur, Malappuram,<br>
            Kerala.<br><br>
            Email: admissions@apex.edu.mock<br>
            Phone: +91 XXXXXXXXXX
          </p>
        </div>
      </div>
      <div class="footer-bottom">
        <p>&copy; 2026 Apex Institute of Technology. All Rights Reserved.</p>
      </div>
    </div>
  </footer>

  <script src="jquery-3.7.1.min.js"></script>
  <script src="main.js"></script>
</body>
</html>
