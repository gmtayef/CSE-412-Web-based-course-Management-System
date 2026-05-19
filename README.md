<div align="center">

<img src="https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white"/>
<img src="https://img.shields.io/badge/MySQL-005C84?style=for-the-badge&logo=mysql&logoColor=white"/>
<img src="https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white"/>
<img src="https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white"/>
<img src="https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white"/>

<br/><br/>

# 📚 BOT Learning
### Web-Based Course Management System

**CSE-412 · Web Engineering Project**

*A full-featured online learning platform with multi-role dashboards, video lessons, quizzes, assignments, and certificate generation.*

<br/>

[![GitHub repo](https://img.shields.io/badge/Repository-gmtayef-181717?style=flat-square&logo=github)](https://github.com/gmtayef/CSE-412-Web-based-course-Management-System)
![Language](https://img.shields.io/badge/PHP-72.7%25-777BB4?style=flat-square)
![CSS](https://img.shields.io/badge/CSS-19.8%25-1572B6?style=flat-square)
![HTML](https://img.shields.io/badge/HTML-7.1%25-E34F26?style=flat-square)

</div>

---

## 📖 Table of Contents

- [Overview](#-overview)
- [Screenshots](#-screenshots)
- [Features](#-features)
- [Tech Stack](#-tech-stack)
- [Project Structure](#-project-structure)
- [Installation](#-installation--setup)
- [User Roles](#-user-roles--access)
- [Database](#-database)
- [Security Notes](#-security-notes)

---

## 🌟 Overview

**BOT Learning** is a feature-rich, full-stack online course management system built with PHP and MySQL. The platform supports three distinct user roles — **Student**, **Instructor**, and **Admin** — each with a dedicated dashboard. Students can browse courses, enroll, watch video episodes, take quizzes, submit assignments, and earn downloadable PDF certificates upon completion.

---

## 🖼️ Screenshots

### 🔐 Authentication

<table>
  <tr>
    <td align="center" width="50%">
      <img src="screenshots/login.png" alt="Login Page" width="100%"/>
      <br/><b>Login Page</b>
    </td>
    <td align="center" width="50%">
      <img src="screenshots/register.png" alt="Registration Page" width="100%"/>
      <br/><b>Register / Sign Up</b>
    </td>
  </tr>
</table>

---

### 🛡️ Admin Panel

<table>
  <tr>
    <td align="center" width="50%">
      <img src="screenshots/admin_dashboard.png" alt="Admin Dashboard" width="100%"/>
      <br/><b>Admin Overview Dashboard</b><br/>
      <sub>Approve enrollments and manage the platform</sub>
    </td>
    <td align="center" width="50%">
      <img src="screenshots/admin_manage_courses.png" alt="Manage Courses" width="100%"/>
      <br/><b>Manage Courses</b><br/>
      <sub>Publish new courses, view active courses & instructors</sub>
    </td>
  </tr>
  <tr>
    <td align="center" width="50%">
      <img src="screenshots/admin_add_instructor.png" alt="Add Instructor" width="100%"/>
      <br/><b>Instructor Management</b><br/>
      <sub>Add and manage course instructors</sub>
    </td>
    <td align="center" width="50%">
      <img src="screenshots/instructors_page.png" alt="Instructors Page" width="100%"/>
      <br/><b>Public Instructors Page</b><br/>
      <sub>Browse all expert instructors</sub>
    </td>
  </tr>
</table>

---

### 👨‍🏫 Instructor Panel — BOT Tutor

<table>
  <tr>
    <td align="center" width="50%">
      <img src="screenshots/instructor_dashboard.png" alt="Instructor Dashboard" width="100%"/>
      <br/><b>Instructor Dashboard</b><br/>
      <sub>Active courses & enrolled student overview</sub>
    </td>
    <td align="center" width="50%">
      <img src="screenshots/instructor_manage_content.png" alt="Manage Content" width="100%"/>
      <br/><b>Manage Content</b><br/>
      <sub>Upload video lessons or attach YouTube links</sub>
    </td>
  </tr>
  <tr>
    <td align="center" width="50%">
      <img src="screenshots/instructor_assignments.png" alt="Assignments Setup" width="100%"/>
      <br/><b>Assignments Setup</b><br/>
      <sub>Create tasks with due dates and guidelines</sub>
    </td>
    <td align="center" width="50%">
      <img src="screenshots/instructor_grading.png" alt="Grade Submissions" width="100%"/>
      <br/><b>Grade Submissions</b><br/>
      <sub>Review student work and issue grades with feedback</sub>
    </td>
  </tr>
  <tr>
    <td align="center">
      <img src="screenshots/instructor_progress.png" alt="Student Progress Tracker" width="50%"/>
      <br/><b>Student Progress Tracker</b><br/>
      <sub>Monitor video completion rates and assignment status per student</sub>
    </td>
  </tr>
</table>

---

### 👨‍🎓 Student Panel — BOT Student

<table>
  <tr>
    <td align="center" width="50%">
      <img src="screenshots/student_dashboard.png" alt="Student Dashboard" width="100%"/>
      <br/><b>Student Dashboard</b><br/>
      <sub>Track enrolled courses, completions & certificates</sub>
    </td>
    <td align="center" width="50%">
      <img src="screenshots/student_explore.png" alt="Explore Courses" width="100%"/>
      <br/><b>Explore Courses</b><br/>
      <sub>Discover and enroll in new courses</sub>
    </td>
  </tr>
  <tr>
    <td align="center" width="50%">
      <img src="screenshots/student_assignments.png" alt="My Assignments" width="100%"/>
      <br/><b>My Assignments</b><br/>
      <sub>Submit work and track review status</sub>
    </td>
    <td align="center" width="50%">
      <img src="screenshots/cart.png" alt="Shopping Cart" width="100%"/>
      <br/><b>Shopping Cart</b><br/>
      <sub>Review selected courses before checkout</sub>
    </td>
  </tr>
</table>

---

### 🌐 Public Pages & Certificate

<table>
  <tr>
    <td align="center" width="50%">
      <img src="screenshots/course_listing.png" alt="Course Listing" width="100%"/>
      <br/><b>Course Listing Page</b><br/>
      <sub>Browse all available courses with pricing</sub>
    </td>
    <td align="center" width="50%">
      <img src="screenshots/certificate.png" alt="Certificate of Excellence" width="100%"/>
      <br/><b>Certificate of Excellence</b><br/>
      <sub>Downloadable & printable PDF certificate on completion</sub>
    </td>
  </tr>
</table>

---

> 📁 **Setup screenshots:** Create a `/screenshots` folder in the repository root and add the images with these exact filenames:
>
> `login.png` · `register.png` · `admin_dashboard.png` · `admin_manage_courses.png` · `admin_add_instructor.png` · `instructors_page.png` · `instructor_dashboard.png` · `instructor_manage_content.png` · `instructor_assignments.png` · `instructor_grading.png` · `instructor_progress.png` · `student_dashboard.png` · `student_explore.png` · `student_assignments.png` · `cart.png` · `course_listing.png` · `certificate.png`

---

## ✨ Features

### 👨‍🎓 Student
- Secure registration and login (with account type selection)
- Browse, filter, and explore all available courses
- Add courses to cart and proceed to checkout/enrollment
- Watch video episodes per enrolled course
- Take quizzes per course (Java, Python, PHP, C)
- Submit assignments by file upload
- Track personal learning progress
- Generate and **download PDF certificates** upon completion
- Personal student profile management

### 👨‍🏫 Instructor
- Dedicated **BOT Tutor** dashboard
- View active courses and total enrolled students at a glance
- Upload video lessons (file or YouTube URL)
- Create and manage assignments with due dates
- Grade student submissions with percentage scores and feedback
- Monitor student progress tracker (video completion % + assignment status)

### 🛡️ Admin
- **BOT Admin** control panel with dark sidebar layout
- Approve/manage student enrollment requests (pending approvals)
- Publish new courses with title, price, and details
- Add, update, and remove instructors (with profile image upload)
- View platform-wide stats (active courses, total instructors)

### 🌐 General
- Dark-themed hero landing page with glassmorphism navigation
- Public course listing with "Add to Cart" functionality
- Instructors directory with contact and LinkedIn links
- FAQ, About, Contact, and Privacy Policy pages
- Fully responsive layout powered by Tailwind CSS

---

## 🛠️ Tech Stack

| Layer       | Technology                              |
|-------------|------------------------------------------|
| Backend     | PHP (server-side scripting)              |
| Database    | MySQL (schema: `course1_db.sql`)         |
| Frontend    | HTML5, CSS3, Tailwind CSS (CDN)          |
| Icons       | Font Awesome                             |
| Fonts       | Google Fonts — Inter                     |
| Environment | XAMPP / WAMP / LAMP (Apache + MySQL)     |

---

## 📁 Project Structure

```
📦 CSE-412-Web-based-course-Management-System/
│
├── 📄 index.php                   # Landing / Home page (BOT Learning)
├── 📄 config.php                  # Database connection configuration
├── 📄 login_form.php              # User login
├── 📄 register_form.php           # User registration (Student/Instructor)
├── 📄 logout.php                  # Session logout
│
├── 🎓 STUDENT PANEL
│   ├── student_panel.php          # Student dashboard (enrolled, completed, certs)
│   ├── my-courses.php             # Student's enrolled courses list
│   ├── assignments.php            # Submit & track assignments
│   ├── cart.php                   # Shopping cart
│   ├── add_to_cart.php            # Add course to cart
│   ├── remove-from-cart.php       # Remove from cart
│   ├── checkout.php               # Checkout & enrollment
│   ├── enroll.php                 # Enrollment handler
│   ├── generate_certificate.php   # PDF Certificate generation
│   └── useProfile.php             # Student profile
│
├── 📚 COURSES (PUBLIC)
│   ├── course-page.php            # All courses listing
│   ├── course-details.php         # Generic course detail
│   ├── course-details-java.php    # Java course detail
│   ├── course-details-python.php  # Python course detail
│   ├── course-details-php.php     # PHP course detail
│   ├── course-list.php            # Filtered course listing
│   ├── explore_courses.php        # Student: explore all courses
│   └── filterCourse-page.php      # Course filter logic
│
├── 🧑‍🏫 INSTRUCTOR PANEL
│   ├── instructor_panel.php       # Instructor dashboard (BOT Tutor)
│   ├── episodeUpload.php          # Upload video lessons
│   ├── episodeList.php            # Manage episode list
│   └── userEpisodeList.php        # Episode list (student view)
│
├── 📝 QUIZZES
│   ├── quiz-java.php              # Java quiz
│   ├── quiz-python.php            # Python quiz
│   ├── quiz-php.php               # PHP quiz
│   └── quiz-C.php                 # C programming quiz
│
├── 🛡️ ADMIN PANEL
│   ├── admin.php                  # Admin login
│   ├── admin_panel.php            # Admin dashboard (overview/approvals)
│   ├── add-course.php             # Publish new course
│   ├── edit-course.php            # Edit existing course
│   ├── delete-course.php          # Delete course
│   ├── add_instructor.php         # Add instructor form
│   └── manage_instructors.php     # Manage all instructors
│
├── 🌐 PUBLIC PAGES
│   ├── about.php / about.html     # About page
│   ├── contact.php / contact.html # Contact page
│   ├── FAQ.php / FAQ.html         # FAQ page
│   ├── Instructors.php            # Instructors directory
│   └── privacy-policy.php         # Privacy policy
│
├── 🧩 SHARED COMPONENTS
│   ├── header.php / header1.php   # Navigation header
│   └── footer.php                 # Footer
│
├── 🎨 STYLESHEETS (CSS/)
│   ├── style.css / style1.css     # Global styles
│   ├── admin-style.css            # Admin panel styles
│   ├── cart.css, checkout.css     # Cart & checkout
│   ├── course-details-*.css       # Per-course styles
│   └── quiz-*.css                 # Per-quiz styles
│
├── 🗃️ DATABASE
│   └── course1_db.sql             # Full MySQL schema + seed data
│
└── 📁 screenshots/                # ← Add your screenshots here
```

---

## ⚙️ Installation & Setup

### Prerequisites

- PHP >= 7.4
- MySQL >= 5.7
- Apache web server — **XAMPP** (recommended) / WAMP / LAMP

### Steps

**1. Clone the repository**
```bash
git clone https://github.com/gmtayef/CSE-412-Web-based-course-Management-System.git
cd CSE-412-Web-based-course-Management-System
```

**2. Move to server root**
- XAMPP → copy folder to `C:/xampp/htdocs/`
- WAMP → copy folder to `C:/wamp/www/`

**3. Import the database**
```
1. Open phpMyAdmin → http://localhost/phpmyadmin
2. Create a new database named: course1_db
3. Click "Import" → select course1_db.sql → click "Go"
```

**4. Configure database connection**

Open `config.php` and update:
```php
$conn = mysqli_connect("localhost", "root", "", "course1_db");
```

**5. Start the server and open in browser**
```
1. Start Apache & MySQL from XAMPP Control Panel
2. Open: http://localhost/CSE-412-Web-based-course-Management-System/
```

---

## 👥 User Roles & Access

| Role            | Login URL          | Dashboard Page         | Key Capabilities                                            |
|-----------------|--------------------|------------------------|-------------------------------------------------------------|
| 🎓 Student      | `login_form.php`   | `student_panel.php`    | Enroll, watch lessons, submit assignments, get certificates |
| 👨‍🏫 Instructor | `login_form.php`   | `instructor_panel.php` | Upload lessons, create assignments, grade students          |
| 🛡️ Admin       | `admin.php`        | `admin_panel.php`      | Manage courses, instructors, approve enrollments            |

---

## 🗃️ Database

The full MySQL database dump is included as **`course1_db.sql`** at the repository root. It contains the complete schema for:

- Users (students), Instructors, Admins
- Products (courses), Course Activity
- Enrollments, Cart, Orders
- Episodes (video lessons)
- Assignments & Submissions
- Quizzes & Certificates

---

## 📖 Available Courses

| Course              | Quiz | Certificate |
|---------------------|:----:|:-----------:|
| ☕ Java Programming  | ✅   | ✅          |
| 🐍 Python           | ✅   | ✅          |
| 🐘 PHP Development  | ✅   | ✅          |
| 💻 C Programming    | ✅   | ✅          |

---

## 🔐 Security Notes

> ⚠️ Before deploying to production, consider the following:

- **`config.php`** contains plain-text DB credentials — add it to `.gitignore` and use environment variables in production.
- Implement **`password_hash()` / `password_verify()`** for secure password storage.
- Add **CSRF token validation** on all form submissions.
- Use **prepared statements** throughout to prevent SQL injection.

---

## 👨‍💻 Contributors

| Name    | Role              | GitHub                                    |
|---------|-------------------|-------------------------------------------|
| Tayef   | Developer / Admin | [@gmtayef](https://github.com/gmtayef)    |

---

## 📜 License

This project was developed as part of the **CSE-412: Web Engineering** course assignment. It is intended for **academic and educational purposes**.

---

<div align="center">

**⭐ Star this repository if you found it helpful!**

*"Unlock Your Potential" — BOT Learning © 2026*

</div>
