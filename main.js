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

  // 2. Student Login & Registration Alert (Login Page)
  $('#login-panel form').on('submit', function (e) {
    e.preventDefault();
    const username = $('#login-username').val().trim();
    alert('Welcome back, ' + username + '! Logged in successfully.');
    this.reset();
  });

  $('#register-panel form').on('submit', function (e) {
    e.preventDefault();
    const pass = $('#register-password').val();
    const confirm = $('#register-confirm').val();

    if (pass !== confirm) {
      alert('Passwords do not match. Please try again.');
      return;
    }

    alert('Account created successfully! You can now sign in.');
    this.reset();
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
