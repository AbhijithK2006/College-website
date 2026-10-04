// Basic Client-Side Interactivity using JavaScript and jQuery

$(document).ready(function () {

  // 1. Interactive Form Validation before Server Submission (Admissions Page)
  $('#inquiry-form').on('submit', function (e) {
    const name = $('#fullname').val().trim();
    const email = $('#email').val().trim();
    const program = $('#program-select').val();

    if (name === '' || email === '' || !program) {
      e.preventDefault();
      alert('Please fill in all mandatory fields before submitting.');
      return;
    }

    alert('Submitting inquiry to server for ' + name + '...');
  });

  // ========================================================
  // 2. Student Authentication & Dashboard View Management
  // ========================================================

  function getInitials(name) {
    if (!name) return 'ST';
    const parts = name.trim().split(' ');
    let init = parts[0].charAt(0).toUpperCase();
    if (parts.length > 1) {
      init += parts[parts.length - 1].charAt(0).toUpperCase();
    }
    return init;
  }

  function showDashboard(user) {
    $('#dash-student-name').text(user.name || 'Student');
    $('#dash-avatar').text(getInitials(user.name));
    $('#dash-student-id').text('ID: ' + (user.id || 'AIT-2026-1001'));
    $('#dash-student-email').text(user.email || '');
    $('#dash-student-program').text(user.program || 'B.Tech Computer Science');

    $('#dash-info-name').text(user.name || '');
    $('#dash-info-id').text(user.id || 'AIT-2026-1001');
    $('#dash-info-email').text(user.email || '');
    $('#dash-info-program').text(user.program || 'B.Tech Computer Science');
    $('#dash-info-dept').text(user.department || 'School of Computing & Technology');

    $('#hero-title').text('Student Dashboard');
    $('#hero-subtitle').text('Welcome back, ' + (user.name || 'Student') + '. View your coursework, attendance, and campus notices.');

    $('#auth-section').hide();
    $('#dashboard-section').fadeIn(300);
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }

  function hideDashboard() {
    $('#dashboard-section').hide();
    $('#hero-title').text('Student Portal Access');
    $('#hero-subtitle').text('Sign in to access your student dashboard. Only registered student accounts are authorized to log in.');
    $('#auth-section').fadeIn(300);
    $('#student-login-form')[0].reset();
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }

  // Local fallback storage for offline/file:// execution without server
  function getLocalUsers() {
    try {
      const stored = localStorage.getItem('apex_registered_users');
      if (stored) return JSON.parse(stored);
    } catch (err) {}
    return [
      {
        id: 'AIT-2026-1001',
        name: 'Alex Johnson',
        email: 'alex.johnson@apex.edu',
        program: 'B.Tech Computer Science & Engineering',
        department: 'School of Computing & Technology',
        password: 'apex123'
      }
    ];
  }

  function saveLocalUsers(users) {
    try {
      localStorage.setItem('apex_registered_users', JSON.stringify(users));
    } catch (err) {}
  }

  // Handle Student Login via AJAX
  $('#student-login-form').on('submit', function (e) {
    e.preventDefault();
    const username = $('#login-username').val().trim();
    const password = $('#login-password').val();

    if (username === '' || password === '') {
      alert('Please enter both your email/username and password.');
      return;
    }

    // Call PHP Authentication Backend
    $.ajax({
      url: 'login.php',
      type: 'POST',
      dataType: 'json',
      data: {
        action: 'login',
        username: username,
        password: password,
        ajax: 1
      },
      success: function (resp) {
        if (resp && resp.success) {
          // Success Pop-up!
          alert('Login Successful! Welcome, ' + (resp.user ? resp.user.name : username) + '.');
          showDashboard(resp.user || { name: username, email: username });
        } else {
          // Failure Pop-up!
          if (resp && resp.code === 'not_registered') {
            alert('Login Failed: This account is not registered!\n\nPlease register your account first using the registration form.');
            $('#register-name').focus();
          } else {
            alert(resp.message || 'Login Failed: Invalid credentials. If you are not registered, please create an account.');
          }
        }
      },
      error: function () {
        // Offline / file:// protocol fallback
        const users = getLocalUsers();
        const user = users.find(u => u.email.toLowerCase() === username.toLowerCase());
        if (!user) {
          alert('Login Failed: This account is not registered!\n\nPlease register your account first using the registration form.');
          $('#register-name').focus();
        } else if (user.password !== password) {
          alert('Login Failed: Incorrect password!\n\nIf you do not have an account yet, please register first.');
        } else {
          alert('Login Successful! Welcome, ' + user.name + '.');
          showDashboard(user);
        }
      }
    });
  });

  // Handle New Registration via AJAX
  $('#student-register-form').on('submit', function (e) {
    e.preventDefault();
    const name = $('#register-name').val().trim();
    const email = $('#register-email').val().trim();
    const program = $('#register-program').val();
    const pass = $('#register-password').val();
    const confirm = $('#register-confirm').val();

    if (pass !== confirm) {
      alert('Passwords do not match! Please verify and enter identical passwords.');
      return;
    }

    // Call PHP Registration Backend
    $.ajax({
      url: 'login.php',
      type: 'POST',
      dataType: 'json',
      data: {
        action: 'register',
        name: name,
        email: email,
        program: program,
        password: pass,
        'confirm-password': confirm,
        ajax: 1
      },
      success: function (resp) {
        if (resp && resp.success) {
          // Success Registration Pop-up!
          alert('Registration Successful!\n\nYour account has been registered. You can now log in.');
          $('#login-username').val(email);
          $('#login-password').val('').focus();
          $('#student-register-form')[0].reset();
          // Sync local storage as well
          const localUsers = getLocalUsers();
          localUsers.push({
            id: 'AIT-2026-' + (1000 + localUsers.length + 1),
            name: name,
            email: email,
            program: program,
            department: 'School of Computing & Technology',
            password: pass
          });
          saveLocalUsers(localUsers);
        } else {
          alert('Registration Failed:\n\n' + (resp.message || 'An error occurred during registration.'));
        }
      },
      error: function () {
        // Offline / file:// protocol fallback
        const localUsers = getLocalUsers();
        const existing = localUsers.find(u => u.email.toLowerCase() === email.toLowerCase());
        if (existing) {
          alert('Registration Failed:\n\nAn account with this email is already registered. Please sign in instead.');
        } else {
          const newUser = {
            id: 'AIT-2026-' + (1000 + localUsers.length + 1),
            name: name,
            email: email,
            program: program,
            department: 'School of Computing & Technology',
            password: pass
          };
          localUsers.push(newUser);
          saveLocalUsers(localUsers);
          alert('Registration Successful!\n\nYour account has been registered. You can now log in.');
          $('#login-username').val(email);
          $('#login-password').val('').focus();
          $('#student-register-form')[0].reset();
        }
      }
    });
  });

  // Handle Logout
  $('#btn-logout, #btn-quick-logout').on('click', function () {
    $.ajax({
      url: 'login.php',
      type: 'POST',
      dataType: 'json',
      data: { action: 'logout', ajax: 1 },
      complete: function () {
        alert('Logged out successfully.');
        hideDashboard();
      }
    });
  });

  // 3. Highlight Table Row on Click (Academics Page)
  $('table tbody tr').on('click', function () {
    $(this).toggleClass('highlight-row');
  });

  // 4. Click to View Campus Photo Info (Campus Life Page)
  $('.gallery-item').on('click', function () {
    const title = $(this).find('h4').text();
    alert('You clicked on: ' + title);
  });

  // 5. Draw University Seal on HTML5 Canvas (Home Page)
  const canvas = document.getElementById('academic-shield');
  if (canvas) {
    const ctx = canvas.getContext('2d');

    // Draw Shield Background
    ctx.fillStyle = '#990000';
    ctx.beginPath();
    ctx.moveTo(30, 20);
    ctx.lineTo(150, 20);
    ctx.lineTo(150, 100);
    ctx.quadraticCurveTo(90, 170, 90, 170);
    ctx.quadraticCurveTo(30, 100, 30, 100);
    ctx.closePath();
    ctx.fill();

    // Shield Border
    ctx.strokeStyle = '#f5f2eb';
    ctx.lineWidth = 4;
    ctx.stroke();

    // Text on Shield
    ctx.fillStyle = '#f5f2eb';
    ctx.font = 'bold 24px Georgia';
    ctx.textAlign = 'center';
    ctx.fillText('AIT', 90, 80);

    ctx.font = '12px Arial';
    ctx.fillText('EST. 1998', 90, 115);
  }

  // 6. Live Course Search Filter (Academics Page)
  $('#course-search').on('keyup', function () {
    const term = $(this).val().toLowerCase();
    $('table tbody tr').each(function () {
      $(this).toggle($(this).text().toLowerCase().indexOf(term) > -1);
    });
  });

});
